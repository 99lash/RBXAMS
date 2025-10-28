<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Services\RobloxAPI\RobloxService;
use App\Transformers\AccountTransformer;
use App\Utils\AccountType;
use App\Models\AccountModel;
use App\Services\SummaryService;
use App\Services\ScheduledTaskService;

class AccountService
{
  private AccountRepository $accountRepo;
  private TransactionRepository $transactionRepo;
  private RobloxService $robloxService;
  private SummaryService $summaryService;
  private ScheduledTaskService $scheduledTaskService;

  public function __construct(
    AccountRepository $accountRepo = null,
    TransactionRepository $transactionRepo = null,
    RobloxService $robloxService = null,
    SummaryService $summaryService = null,
    ScheduledTaskService $scheduledTaskService = null
  ) {
    $this->accountRepo = $accountRepo ?? new AccountRepository();
    $this->transactionRepo = $transactionRepo ?? new TransactionRepository();
    $this->robloxService = $robloxService ?? new RobloxService();
    $this->summaryService = $summaryService ?? new SummaryService();
    $this->scheduledTaskService = $scheduledTaskService ?? new ScheduledTaskService($this->accountRepo);
  }

  public function getAllAccounts(string $userId, ?string $sortBy = null, ?string $sortOrder = null)
  {
    // ++ WORKFLOW RULE: Auto-update Pending accounts to Unpend when unpend_date is reached
    $this->scheduledTaskService->updatePendingToUnpendAccounts($userId);
    
    $results = $this->accountRepo->findAll($userId, $sortBy, $sortOrder);
    // return AccountTransformer::transform($results);
    return AccountTransformer::transformCollection($results);
  }

  public function create(array $data): bool
  {
    // Encrypt the cookie securely
    $status = $data['account_type'] == AccountType::PENDING ? 'pending' : 'unpend';
    $statusId = $this->accountRepo->findAccountStatusId($status);
    $cookieEnc = base64_encode($data['cookie'] ?? '');

    $account = AccountModel::fromArray($data);
    $account
      ->setCookieEnc($cookieEnc)
      ->setAccountStatusId($statusId)
      ->setRobux($status == AccountType::PENDING ? $data['pendingRobuxTotal'] : $data['robux']);

    return $this->accountRepo->create($account);
  }

  public function getByCookie($cookie): ?AccountModel
  {
    $cookieEnc = base64_encode($cookie) ?? '';
    return $this->accountRepo->findByCookie($cookieEnc);
  }

  public function getById($id)
  {
    $result = $this->accountRepo->findById(intval($id));
    if (!$result) {
      return null;
    }
    return AccountTransformer::transform($result);
  }

  public function updateAccountById($userId, $id, $patchData)
  {

    if (empty($patchData))
      return false;

    // Convert status name to status id if present
    if (isset($patchData['status'])) {
      $statusId = $this->accountRepo->findAccountStatusId(strtolower($patchData['status']));
      if ($statusId) {
        $patchData['account_status_id'] = $statusId;
      }
      unset($patchData['status']);
    }

    // Handle frontend field name mapping for price calculation
    if (isset($patchData['rate_sold'])) {
      $patchData['sold_rate_usd'] = $patchData['rate_sold'];
      unset($patchData['rate_sold']);
    }
    if (isset($patchData['usd_to_peso_rate'])) {
      $patchData['usd_to_php_rate_on_sale'] = $patchData['usd_to_peso_rate'];
      unset($patchData['usd_to_peso_rate']);
    }

    // --- Price Calculation Logic ---
    $priceFields = ['robux', 'sold_rate_usd', 'usd_to_php_rate_on_sale'];
    $isPriceFieldUpdated = false;
    foreach ($priceFields as $field) {
      if (isset($patchData[$field])) {
        $isPriceFieldUpdated = true;
        break;
      }
    }

    // if one of the fields for price calculation is updated, recalculate price_php
    if ($isPriceFieldUpdated) {
      if ($accountData = $this->accountRepo->findById($id)) {
        $accountModel = $accountData['model'];

        // Get current values, and override with new values from patch
        $robux = $patchData['robux'] ?? $accountModel->getRobux();
        $soldRateUsd = $patchData['sold_rate_usd'] ?? $accountModel->getSoldRateUsd();
        $usdToPhp = $patchData['usd_to_php_rate_on_sale'] ?? $accountModel->getUsdToPhpRateOnSale();

        if ($robux > 0 && $soldRateUsd > 0 && $usdToPhp > 0) {
          $pricePhp = ($robux / 1000) * ($soldRateUsd * $usdToPhp);
          $patchData['price_php'] = $pricePhp;
        }
      }
    }

    // --- "Buy" Transaction Logic ---
    // Check if a cost is being set for the first time.
    if (isset($patchData['cost_php']) && $patchData['cost_php'] > 0) {
      if ($currentAccountData = $this->accountRepo->findById($id)) {
        $currentModel = $currentAccountData['model'];

        if ($currentModel->getCostPhp() === null || $currentModel->getCostPhp() == 0) {
          $this->transactionRepo->create(
            $userId,
            $id,
            'buy',
            $currentModel->getRobux(),
            (float) $patchData['cost_php']
          );

          $currentModel->setCostPhp((float) $patchData['cost_php']);
          $this->summaryService->updateSummaryOnBuy($userId, $currentModel);
        }
      }
    }



    // --- "Sell" Transaction Logic ---
    // Check if the account status is being updated to "sold".
    $SOLD_STATUS_ID = $this->accountRepo->findAccountStatusId('sold');
    if (isset($patchData['account_status_id']) && $patchData['account_status_id'] === $SOLD_STATUS_ID) {
      if ($accountData = $this->accountRepo->findById($id)) {
        $accountModel = $accountData['model'];
        
        // Get the values that will be used (either from patch or current model)
        $cost_php = $patchData['cost_php'] ?? $accountModel->getCostPhp();
        $sold_rate_usd = $patchData['sold_rate_usd'] ?? $accountModel->getSoldRateUsd();
        $usd_to_php_rate = $patchData['usd_to_php_rate_on_sale'] ?? $accountModel->getUsdToPhpRateOnSale();
        $price_php = $patchData['price_php'] ?? $accountModel->getPricePhp();
        
        // Calculate profit_php for validation
        $profit_php = $price_php - ($cost_php ?? 0);
        
        // ++ WORKFLOW RULE: Validate that all required fields are set before allowing "Sold" status
        if (!$cost_php || !$sold_rate_usd || !$usd_to_php_rate || !$price_php || $profit_php === null) {
          // Return error - cannot set to Sold without all required fields
          throw new \Exception("Cannot set status to 'Sold': Missing required fields (cost_php, sold_rate_usd, usd_to_php_rate_on_sale, price_php, profit_php)");
        }

        // ++ WORKFLOW RULE: Automatically set sold_date when status changes to "Sold"
        if ($accountModel->getAccountStatusId() !== $SOLD_STATUS_ID) {
          $patchData['sold_date'] = new \DateTimeImmutable();
          
          // Create sell transaction
          $this->transactionRepo->create(
            $userId,
            $id,
            'sell',
            $accountModel->getRobux(),
            (float) $price_php
          );

          $accountModel->setPricePhp((float) $price_php);
          $this->summaryService->updateSummaryOnSell($userId, $accountModel);
        }
      }
    }

    $account = new AccountModel($id);
    foreach ($patchData as $field => $value) {
      $method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
      if (method_exists($account, $method)) {
        $account->$method($value);
      }
    }

    return $this->accountRepo->updatePartial($account);
  }

  public function updateStatusBulk($ids, $status)
  {
    // --- "Sell" Transaction Logic ---
    // Check if the account status is being updated to "sold".
    $SOLD_STATUS_ID = $this->accountRepo->findAccountStatusId('sold');
    if (isset($status) && $status === $SOLD_STATUS_ID) {
      // existing validation logic...
      $soldDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
      return $this->accountRepo->updateStatusBulk($ids, $status, $soldDate);
    }

    return $this->accountRepo->updateStatusBulk($ids, $status);
  }

  public function deleteBulk($ids)
  {
    foreach ($ids as $id) {
      // var_dump($id);
      if ($this->accountRepo->findById($id)) {
      }

      //TODO: implement id not found response
    }

    return $this->accountRepo->deleteBulk($ids);
  }
}