<?php

namespace App\Repositories;

use App\Utils\IdGeneratorFactory;
use App\Utils\IdType;
use mysqli;
use App\Config\Database;

class TransactionRepository
{
  private mysqli $mysqli;

  public function __construct()
  {
    $db = new Database();
    $this->mysqli = $db->getConnection();
  }

  /**
   * Creates a new daily transaction record.
   *
   * @param string $accountId The ID of the related account.
   * @param string $action The action being performed ('buy' or 'sell').
   * @param float $robuxAmount The amount of Robux in the transaction.
   * @param float $phpAmount Represents cost_php for a 'buy' or price_php for a 'sell'.
   * @return bool Returns true on success, false on failure.
   */
  public function create(string $userId, string $accountId, string $action, float $robuxAmount, float $phpAmount): bool
  {
    $query = "
    INSERT INTO transactions (id, user_id, account_id, action, robux_amount, php_amount)
    VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $this->mysqli->prepare($query);

    if ($stmt === false) {
      // Log the error instead of dying for better error handling
      error_log("Prepare failed: " . $this->mysqli->error);
      return false;
    }

    $transactionId = (IdGeneratorFactory::createId(IdType::TRANSACTION))->generate();

    $stmt->bind_param("ssssdd", $transactionId, $userId, $accountId, $action, $robuxAmount, $phpAmount);

    return $stmt->execute();
  }
}