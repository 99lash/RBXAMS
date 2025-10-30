<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Services\RobloxAPI\RobloxService;
use App\Transformers\AccountTransformer;
use App\Utils\AccountType;
use App\Models\AccountModel;
use App\Services\ScheduledTaskService;
use App\Services\SummaryService;
use App\Utils\IdGeneratorFactory;
use App\Utils\IdType;

class AccountService
{
	private AccountRepository $accountRepo;
	private TransactionRepository $transactionRepo;
	private RobloxService $robloxService;
	private ScheduledTaskService $scheduledTaskService;

	private SummaryService $summaryService;

	public function __construct(
		AccountRepository $accountRepo = null,
		TransactionRepository $transactionRepo = null,
		RobloxService $robloxService = null,
		ScheduledTaskService $scheduledTaskService = null,
		SummaryService $summaryService = null
	) {
		$this->accountRepo = $accountRepo ?? new AccountRepository();
		$this->transactionRepo = $transactionRepo ?? new TransactionRepository();
		$this->robloxService = $robloxService ?? new RobloxService();
		$this->scheduledTaskService = $scheduledTaskService ?? new ScheduledTaskService($this->accountRepo);
		$this->summaryService = $summaryService ?? new SummaryService();
	}

	public function getAllAccounts(
		string $userId,
		int $page,
		int $limit,
		?string $sortBy = null,
		?string $sortOrder = null,
		?string $search = null,
		?string $status = null,
		?string $accountType = null
	) {
		$this->scheduledTaskService->updatePendingToUnpendAccounts($userId);
		$offset = ($page - 1) * $limit;

		$totalAccounts = $this->accountRepo->countAll(
			$userId,
			$search,
			$status,
			$accountType
		);

		$accounts = $this->accountRepo->findAll(
			$userId,
			$limit,
			$offset,
			$sortBy,
			$sortOrder,
			$search,
			$status,
			$accountType
		);

		$transformedAccounts = AccountTransformer::transformCollection($accounts);

		$totalPending = $this->accountRepo->countAll($userId, $search, $status, 'pending');
		$totalFastflip = $this->accountRepo->countAll($userId, $search, $status, 'fastflip');


		return [
			'data' => $transformedAccounts,
			'pagination' => [
				'total_items' => $totalAccounts,
				'per_page' => $limit,
				'current_page' => $page,
				'last_page' => ceil($totalAccounts / $limit),
				'from' => $offset + 1,
				'to' => min($offset + $limit, $totalAccounts),
			],
			'total_pending' => $totalPending,
			'total_fastflip' => $totalFastflip,
		];
	}

	public function create(array $data): bool
	{
		$status = $data['account_type'] == AccountType::PENDING ? 'pending' : 'unpend';
		$statusId = $this->accountRepo->findAccountStatusId($status);
		$cookieEnc = base64_encode($data['cookie'] ?? '');

		$account = AccountModel::fromArray($data);
		$account
			->setCookieEnc($cookieEnc)
			->setAccountStatusId($statusId)
			->setRobux($status == AccountType::PENDING ? $data['pendingRobuxTotal'] : $data['robux']);

		$success = $this->accountRepo->create($account);
		// if ($success) {
		// 	$this->summaryService->recomputeDailySummary($account->getUserId(), (new \DateTime())->format('Y-m-d'));
		// }
		return $success;
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

		$originalAccountData = $this->accountRepo->findById($id);
		if (!$originalAccountData) {
			throw new \Exception("Account not found.");
		}
		$originalAccountModel = $originalAccountData['model'];

		if (isset($patchData['status'])) {
			$statusId = $this->accountRepo->findAccountStatusId(strtolower($patchData['status']));
			if ($statusId) {
				$patchData['account_status_id'] = $statusId;
			}
			unset($patchData['status']);
		}

		$revertSoldDate = null;
		if (isset($patchData['revert_sold']) && $patchData['revert_sold']) {
			$revertSoldDate = $this->handleRevertSoldTransaction($userId, $id, "Reverted from Sold to Unpend by user");
		}

		if (isset($patchData['cost_php']) && $patchData['cost_php'] != $originalAccountModel->getCostPhp()) {
			$this->handleBuyTransaction($userId, $id, (float) $patchData['cost_php'], 'User updated cost');
		}

		$SOLD_STATUS_ID = $this->accountRepo->findAccountStatusId('sold');
		if (isset($patchData['account_status_id']) && $patchData['account_status_id'] === $SOLD_STATUS_ID && $originalAccountModel->getAccountStatusId() !== $SOLD_STATUS_ID) {
			$pricePhp = $this->calculatePricePhp($patchData, $originalAccountModel);
			$patchData['price_php'] = $pricePhp;
			$this->handleSellTransaction($userId, $id, $pricePhp, 'Account sold by user');
			$patchData['sold_date'] = new \DateTimeImmutable();
		}

		$account = new AccountModel($id);
		foreach ($patchData as $field => $value) {
			$method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
			if (method_exists($account, $method)) {
				$account->$method($value);
			}
		}

		$updateSuccess = $this->accountRepo->updatePartial($account);

		// Recompute daily summary if any transaction-related data was updated
		if (isset($patchData['cost_php']) || isset($patchData['account_status_id']) || $revertSoldDate !== null) {
			$summaryDate = $revertSoldDate ?? date('Y-m-d');
			$this->summaryService->recomputeDailySummary($userId, $summaryDate);
		}

		return $updateSuccess;
	}

	private function handleBuyTransaction(string $userId, string $accountId, float $newCost, string $reason)
	{
		$this->transactionRepo->beginTransaction();
		try {
			$oldTxn = $this->transactionRepo->findActiveTransaction($accountId, 'BUY');
			$oldTxnId = null;
			if ($oldTxn) {
				$oldTxnId = $oldTxn['transaction_id'];
				$this->transactionRepo->voidTransaction($oldTxnId, 'Corrected by new entry');
			}
			$account = $this->accountRepo->findById($accountId)['model'];
			$newTxnId = (IdGeneratorFactory::createId(IdType::TRANSACTION))->generate();
			$this->transactionRepo->create($newTxnId, $userId, $accountId, 'BUY', $account->getRobux(), $newCost, 'active', $oldTxnId, $reason, $account->getAccountType());

			$this->transactionRepo->commit();
		} catch (\Exception $e) {
			$this->transactionRepo->rollback();
			throw $e;
		}
	}

	private function handleSellTransaction(string $userId, string $accountId, float $soldPrice, string $reason)
	{
		$this->transactionRepo->beginTransaction();
		try {
			$oldTxn = $this->transactionRepo->findActiveTransaction($accountId, 'SELL');
			$oldTxnId = null;
			if ($oldTxn) {
				$oldTxnId = $oldTxn['transaction_id'];
				$this->transactionRepo->voidTransaction($oldTxnId, 'Superseded by new SELL transaction');
			}
			$account = $this->accountRepo->findById($accountId)['model'];
			$newTxnId = (IdGeneratorFactory::createId(IdType::TRANSACTION))->generate();
			$this->transactionRepo->create($newTxnId, $userId, $accountId, 'SELL', $account->getRobux(), $soldPrice, 'active', $oldTxnId, $reason, $account->getAccountType());

			$this->transactionRepo->commit();
		} catch (\Exception $e) {
			$this->transactionRepo->rollback();
			throw $e;
		}
	}

	private function handleRevertSoldTransaction(string $userId, string $accountId, string $reason): ?string
	{
		$this->transactionRepo->beginTransaction();
		try {
			$activeSellTxn = $this->transactionRepo->findActiveTransaction($accountId, 'SELL');
			if ($activeSellTxn) {
				$voidSuccess = $this->transactionRepo->voidTransaction($activeSellTxn['transaction_id'], $reason);

				$unpendStatusId = $this->accountRepo->findAccountStatusId('unpend');
				$account = new AccountModel($accountId);
				$account->setAccountStatusId($unpendStatusId);
				$account->setSoldDate(null);
				$this->accountRepo->updatePartial($account);

				$transactionDate = (new \DateTime($activeSellTxn['created_at']))->format('Y-m-d');

				$this->transactionRepo->commit();
				return $transactionDate;
			} else {
				$this->transactionRepo->rollback();
				return null;
			}
		} catch (\Exception $e) {
			$this->transactionRepo->rollback();
			throw $e;
		}
	}

	private function calculatePricePhp(array $patchData, AccountModel $accountModel): float
	{
		if (isset($patchData['rate_sold'])) {
			$patchData['sold_rate_usd'] = $patchData['rate_sold'];
		}
		if (isset($patchData['usd_to_peso_rate'])) {
			$patchData['usd_to_php_rate_on_sale'] = $patchData['usd_to_peso_rate'];
		}

		$robux = $patchData['robux'] ?? $accountModel->getRobux();
		$soldRateUsd = $patchData['sold_rate_usd'] ?? $accountModel->getSoldRateUsd();
		$usdToPhp = $patchData['usd_to_php_rate_on_sale'] ?? $accountModel->getUsdToPhpRateOnSale();

		if ($robux > 0 && $soldRateUsd > 0 && $usdToPhp > 0) {
			return ($robux / 1000) * ($soldRateUsd * $usdToPhp);
		}
		return $patchData['price_php'] ?? $accountModel->getPricePhp() ?? 0;
	}

	public function updateStatusBulk($ids, $statusString)
	{
		$statusId = $this->accountRepo->findAccountStatusId(strtolower($statusString));
		if (!$statusId) {
			error_log("AccountService: Status ID not found for status: " . $statusString);
			return false;
		}

		$SOLD_STATUS_ID = $this->accountRepo->findAccountStatusId('sold');

		$soldDate = null; // Default to null
		if ($statusId === $SOLD_STATUS_ID) {
			$soldDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
		}

		return $this->accountRepo->updateStatusBulk($ids, $statusId, $soldDate);
	}

	public function deleteBulk($ids)
	{
		return $this->accountRepo->deleteBulk($ids);
	}
}