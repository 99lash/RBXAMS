<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\AccountModel;
use App\Utils\IdGeneratorFactory; 
use App\Utils\IdType; 
use mysqli;
class AccountRepository
{
  private mysqli $mysqli;

  public function __construct()
  {
    $this->mysqli = (new Database())->getConnection();
  }

  public function findALl()
  {
    $results = [];
    $accountsData = [];
    // $query = "SELECT * FROM accounts";
    $query = "
    SELECT
      a.id,
      a.user_id,
      a.account_type,
      s.name AS status,
      a.name,
      a.robux,
      a.cost_php,
      a.price_php,
      a.usd_to_php_rate_on_sale,
      a.sold_rate_usd,
      a.unpend_date,
      a.sold_date,
      a.created_at,
      a.updated_at,
      a.deleted_at
    FROM accounts AS a
    LEFT JOIN account_status AS s 
    ON a.account_status_id = s.id
    WHERE a.deleted_at IS NULL
    ";

    $result = $this->mysqli->query($query);
    if (!$result) {
      error_log('FindAll query failed: ' . $this->mysqli->error);
      return [];
    }

    $accountsData = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    foreach ($accountsData as $data) {
      $account = AccountModel::fromArray($data);
      $results[] = [
        'model' => $account,
        'status' => $data['status']
      ];
      // $accounts[] = $accountData;
    }
    return $results;
  }

  public function findALlExisting()
  {
    $accounts = [];
    $accountsData = [];
    // $query = "SELECT * FROM accounts";
    $query = "
    SELECT
      a.id,
      a.user_id,
      a.account_type,
      s.name AS status,
      a.name,
      a.robux,
      a.cost_php,
      a.price_php,
      a.profit_php,
      a.sold_rate_usd,
      a.unpend_date,
      a.sold_date,
      a.created_at,
      a.updated_at
    FROM accounts AS a
    LEFT JOIN account_status AS s 
    ON a.account_status_id = s.id
    WHERE a.deleted_at = NULL;
    ";
    $result = $this->mysqli->query($query);
    if ($result) {
      $accountsData = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }
    foreach ($accountsData as $accountData) {
      // $account = AccountModel::fromArray($accountData);
      $accounts[] = $accountData;
    }
    return $accounts;
  }

  public function findAccountStatusId($status)
  {
    $stmt = $this->mysqli->prepare("SELECT id FROM account_status WHERE name = ?");
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row['id'];
  }


  public function findById($id)
  {
    // $query = "SELECT * FROM accounts WHERE id = ?";
    $query = "
      SELECT
        a.id, a.user_id, a.account_type, a.account_status_id,
        s.name AS status, a.name, a.cookie_enc, a.robux, a.cost_php, a.price_php,
        a.usd_to_php_rate_on_sale, a.sold_rate_usd, a.unpend_date,
        a.sold_date, a.created_at, a.updated_at, a.deleted_at
      FROM accounts AS a
      LEFT JOIN account_status AS s ON a.account_status_id = s.id
      WHERE a.id = ? AND a.deleted_at IS NULL";

    $stmt = $this->mysqli->prepare($query);

    if (!$stmt) {
      die("Prepare failed: ({$this->mysqli->errno}) {$this->mysqli->error}");
    }

    // var_dump($id);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
      $account = AccountModel::fromArray($row);
      // $createdAt = new DateTimeImmutable($row['created_at']);
      // $updatedAt = new DateTimeImmutable($row['updated_at']);
      // $account->setCreatedAt($createdAt)->setUpdatedAt($updatedAt);
      return [
        'model' => $account,
        'status' => $row['status']
      ];

    }
    return null;
  }

  public function findByCookie($cookieEnc): ?AccountModel
  {
    $stmt = $this->mysqli->prepare("SELECT * FROM accounts WHERE cookie_enc = ?");
    $stmt->bind_param("s", $cookieEnc);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
      return AccountModel::fromArray($row);
    }
    return null;
  }

  public function create(AccountModel $account): bool
  {
    $stmt = $this->mysqli->prepare("
        INSERT INTO accounts (
            id, user_id, account_type, account_status_id, name, cookie_enc,
            robux, cost_php, price_php, usd_to_php_rate_on_sale, sold_rate_usd, sold_date, deleted_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $id = $account->getId();
    $userId = $account->getUserId();
    $accountType = $account->getAccountType();
    $statusId = $account->getAccountStatusId();
    $name = $account->getName();
    $cookieEnc = $account->getCookieEnc();
    $robux = $account->getRobux();
    $costPhp = $account->getCostPhp();
    $pricePhp = $account->getPricePhp();
    $usd_to_php_rate_on_sale = $account->getUsdToPhpRateOnSale();
    $soldRateUsd = $account->getSoldRateUsd();
    $soldDate = $account->getSoldDate()?->format('Y-m-d H:i:s');
    $deletedAt = $account->getDeletedAt()?->format('Y-m-d H:i:s');

    $stmt->bind_param(
      "ississdddddss",
      $id,
      $userId,
      $accountType,
      $statusId,
      $name,
      $cookieEnc,
      $robux,
      $costPhp,
      $pricePhp,
      $usd_to_php_rate_on_sale,
      $soldRateUsd,
      $soldDate,
      $deletedAt
    );
    return $stmt->execute();
  }

  public function updatePartial(AccountModel $account): bool
  {
    $fields = [];
    $params = [];
    $types = "";

    // get_object_vars can see private props only inside the same class,
    // but here we call from Repository, so use jsonSerialize
    $data = $account->toArray();
    // var_dump($data);
    unset($data['id']);
    unset($data['profit_php']);
    foreach ($data as $column => $value) {
      // if ($column === "id" || $column === 'profit_php') {
      //   continue; // never update primary key
      // }
      if ($value !== null) {
        $fields[] = "{$column} = ?";
        $params[] = $value;
        $types .= is_int($value) ? "i" : "s";
      }
    }

    if (empty($fields)) {
      return false; // nothing to update
    }
    $sql = "UPDATE accounts SET " . implode(", ", $fields) . " WHERE id = ?";

    // echo $sql;
    $stmt = $this->mysqli->prepare($sql);

    if (!$stmt) {
      throw new \Exception("Prepare failed: " . $this->mysqli->error);
    }


    // add the id at the end
    $params[] = $account->getId();
    $types .= "s";

    $stmt->bind_param($types, ...$params);
    // var_dump($stmt);
    return $stmt->execute();
  }

  public function updateStatusBulk($ids, $status)
  {
    $ins = str_repeat('?,', count($ids) - 1) . '?';
    $query = "UPDATE accounts SET account_status_id = ? WHERE id IN ($ins)";
    $stmt = $this->mysqli->prepare($query);
    $types = str_repeat('i', count($ids) + 1);
    $params = array_merge([$status], $ids);
    $stmt->bind_param($types, ...$params);
    // no need to manually update the updated_at field, mysql already handle that hehe thank god.
    return $stmt->execute();
  }

  public function deleteBulk($ids)
  {
    $now = date('Y-m-d H:i:s');
    $ins = str_repeat('?,', count($ids) - 1) . '?';
    $query = "UPDATE accounts SET deleted_at = ? WHERE id IN ($ins)";
    $stmt = $this->mysqli->prepare($query);
    $types = 's' . str_repeat('i', count($ids));
    $params = array_merge([$now], $ids);
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
  }
}