<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Services\RobloxAPI\RobloxService;
use App\Transformers\AccountTransformer;
use App\Utils\AccountType;
use App\Models\AccountModel;
use App\Services\SummaryService;

class AccountService
{
  private AccountRepository $accountRepo;
  private TransactionRepository $transactionRepo;
  private RobloxService $robloxService;
  private SummaryService $summaryService;

  public function __construct(
    AccountRepository $accountRepo = null,
    TransactionRepository $transactionRepo = null,
    RobloxService $robloxService = null,
    SummaryService $summaryService = null
  ) {
    $this->accountRepo = $accountRepo ?? new AccountRepository();
    $this->transactionRepo = $transactionRepo ?? new TransactionRepository();
    $this->robloxService = $robloxService ?? new RobloxService();
    $this->summaryService = $summaryService ?? new SummaryService();
  }

  public function getAllAccounts(?string $sortBy = null, ?string $sortOrder = null, int $page = 1, int $perPage = 10): array
  {
    $results = $this->accountRepo->findAll($sortBy, $sortOrder, $page, $perPage);
    $totalCount = $this->accountRepo->getTotalCount();
    
    $transformedAccounts = AccountTransformer::transformCollection($results);
    
    return [
      'accounts' => $transformedAccounts,
      'pagination' => [
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $totalCount,
        'total_pages' => ceil($totalCount / $perPage),
        'has_next_page' => $page < ceil($totalCount / $perPage),
        'has_previous_page' => $page > 1
      ]
    ];
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

  public function updateAccountById($id, $patchData)
  {

    if (empty($patchData))
      return false;

    // --- "Buy" Transaction Logic ---
    // Check if a cost is being set for the first time.
    if (isset($patchData['cost_php']) && $patchData['cost_php'] > 0) {
      if ($currentAccountData = $this->accountRepo->findById($id)) {
        $currentModel = $currentAccountData['model'];

        if ($currentModel->getCostPhp() === null || $currentModel->getCostPhp() == 0) {
          $this->transactionRepo->create(
            $id,
            'buy',
            $currentModel->getRobux(),
            (float) $patchData['cost_php']
          );

          $currentModel->setCostPhp((float) $patchData['cost_php']);
          $this->summaryService->updateSummaryOnBuy($currentModel);
        }
      }
    }



    // --- "Sell" Transaction Logic ---
    // Check if the account status is being updated to "sold".
    $SOLD_STATUS_ID = $this->accountRepo->findAccountStatusId('sold');
    if (isset($patchData['account_status_id']) && $patchData['account_status_id'] === $SOLD_STATUS_ID) {
      if ($accountData = $this->accountRepo->findById($id)) {
        $accountModel = $accountData['model'];
        $price_php = $patchData['price_php'] ?? $accountModel->getPricePhp();

        //TODO: prevent multiple sell even if the account status is sold
        if ($price_php !== null && $price_php > 0 && $accountModel->getAccountStatusId() !== $SOLD_STATUS_ID) {
          $this->transactionRepo->create(
            $id,
            'sell',
            $accountModel->getRobux(),
            (float) $price_php
          );

          $accountModel->setPricePhp((float) $price_php);
          $this->summaryService->updateSummaryOnSell($accountModel);
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

      foreach ($ids as $id) {
        if ($accountData = $this->accountRepo->findById($id)) {
          $currentModel = $accountData['model'];
          $price_php = $currentModel->getPricePhp() ?? 0;
          if ($price_php !== null && $price_php > 0) {
            $this->transactionRepo->create(
              $id,
              'sell',
              $currentModel->getRobux(),
              (float) $price_php
            );
            $this->summaryService->updateSummaryOnSell($currentModel);
          }
        }
      }
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