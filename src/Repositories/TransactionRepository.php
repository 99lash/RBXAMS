<?php

namespace App\Repositories;

use App\Config\Database;
use mysqli;

class TransactionRepository
{
	private mysqli $mysqli;

	public function __construct()
	{
		$this->mysqli = (new Database())->getConnection();
	}

	public function beginTransaction(): void
	{
		$this->mysqli->begin_transaction();
	}

	public function commit(): void
	{
		$this->mysqli->commit();
	}

	public function rollback(): void
	{
		$this->mysqli->rollback();
	}

	public function findActiveTransaction(string $accountId, string $transactionType): ?array
	{
		$stmt = $this->mysqli->prepare(
			"SELECT * FROM transactions WHERE account_id = ? AND transaction_type = ? AND txn_status = 'active' LIMIT 1"
		);
		$stmt->bind_param("ss", $accountId, $transactionType);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->fetch_assoc();
	}

	public function voidTransaction(string $transactionId, string $reason): bool
	{
		$stmt = $this->mysqli->prepare(
			"UPDATE transactions SET txn_status = 'voided', reason = ? WHERE transaction_id = ?"
		);
		$stmt->bind_param("ss", $reason, $transactionId);
		return $stmt->execute();
	}

	public function create(
		string $transactionId,
		string $userId,
		string $accountId,
		string $transactionType,
		float $robux_amount,
		float $amount,
		string $txnStatus = 'active',
		?string $relatedTxnId = null,
		?string $reason = null,
		?string $account_type = null
	): bool {
		$stmt = $this->mysqli->prepare(
			"INSERT INTO transactions (transaction_id, user_id, account_id, transaction_type, robux_amount, amount, txn_status, related_txn_id, reason, account_type) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
		);
		$stmt->bind_param(
			"ssssddssss",
			$transactionId,
			$userId,
			$accountId,
			$transactionType,
			$robux_amount,
			$amount,
			$txnStatus,
			$relatedTxnId,
			$reason,
			$account_type
		);
		return $stmt->execute();
	}


	public function findActiveTransactionsByDate(string $userId, string $date): array
	{
		$sql = "SELECT * FROM transactions WHERE user_id = ? AND DATE(created_at) = ? AND txn_status = 'active' AND (transaction_type = 'BUY' OR transaction_type = 'SELL')";
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param("ss", $userId, $date);
		$stmt->execute();
		$result = $stmt->get_result();
		$transactions = $result->fetch_all(MYSQLI_ASSOC);
		return $transactions;
	}
}