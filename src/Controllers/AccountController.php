<?php

namespace App\Controllers;

use App\Security\AuthManager;
use App\Services\AccountService;
use App\Services\RobloxAPI\RobloxService;
use App\Utils\AccountType;

class AccountController
{
  private AuthManager $authManager;
  private AccountService $accountService;
  private array $currentUser;
  public function __construct()
  {
    $this->accountService = new AccountService();
    $this->authManager = new AuthManager(); //disabled for debugging hahah
    $this->currentUser = $this->authManager->requireAuth();
  }
  public function index()
  {
    $page = '/accounts';
    $title = 'Accounts | RBXAMS';
    $nav = 'Accounts';
    require __DIR__ . '/../Views/index.php';
  }

  public function getAccountsJson()
  {
    header('Content-Type: application/json');
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 10;
    $sortBy = $_GET['sort_by'] ?? null;
    $sortOrder = $_GET['sort_order'] ?? 'asc'; // Default to ascending
    $search = $_GET['search'] ?? null;
    $status = $_GET['status'] ?? null;
    $accountType = $_GET['account_type'] ?? null;

    $result = $this->accountService->getAllAccounts(
      $this->currentUser['id'],
      $page,
      $limit,
      $sortBy,
      $sortOrder,
      $search,
      $status,
      $accountType
    );
    echo json_encode($result);
    exit;
  }

  public function getById($id)
  {
    header('Content-Type: application/json');
    $account = $this->accountService->getById($id);
    // echo json_encode($id);
    if (empty($account))
      echo json_encode([]);
    else
      echo json_encode($account, JSON_PRETTY_PRINT);
  }

  public function create()
  {
    header('Content-Type: application/json');

    $cookies = isset($_POST['cookies']) ? explode("\n", $_POST['cookies']) : [];
    $cookies = array_map('trim', $cookies);
    $cookies = array_filter($cookies); // tanggalin empty lines

    $response = [
      "created" => [],
      "failed" => [],
      "duplicate" => []
    ];

    foreach ($cookies as $cookie) {
      // Check if account already exists
      if ($account = $this->accountService->getByCookie($cookie)) {
        $response["duplicate"][] = [
          "account" => [
            "id" => $account->getId(),
            "name" => $account->getName(),
            "account_type" => $account->getAccountType(),
          ],
          "message" => "Account already exists"
        ];
        continue;
      }

      // Validate + fetch account details
      $accountDetails = RobloxService::getAccountDetails($cookie);
      if (!isset($accountDetails['id'])) {
        $response["failed"][] = [
          "cookie" => $cookie,
          "message" => "Invalid or expired cookie"
        ];
        continue;
      }

      // Fetch robux + transactions
      $accountRobux = RobloxService::getAccountRobux($cookie);
      $accountTransactions = RobloxService::getAccountTransaction($accountDetails['id'], $cookie);

      // Prevent adding accounts with 0 Robux and 0 Pending Robux
      if (($accountRobux['robux'] ?? 0) <= 0 && ($accountTransactions['pendingRobuxTotal'] ?? 0) <= 0 && ($accountTransactions['incomingRobuxTotal'] ?? 0) <= 0) {
        $response["failed"][] = [
          "cookie" => $accountDetails['displayName'],
          "message" => "Account has 0 Robux and 0 Pending Robux. Cannot add."
        ];
        continue;
      }

      $accountType = ($accountTransactions['pendingRobuxTotal'] > 0
        || $accountTransactions['incomingRobuxTotal'] > 0)
        ? AccountType::PENDING
        : AccountType::FASTFLIP;

      $accountData = [
        "id" => intval($accountDetails['id']),
        "user_id" => $this->currentUser['id'],
        "account_type" => $accountType,
        "name" => $accountDetails['name'],
        "cookie" => $cookie,
        "robux" => $accountRobux['robux'] ?? 0,
        "pendingRobuxTotal" => $accountTransactions['pendingRobuxTotal'] ?? 0,
        "incomingRobuxTotal" => $accountTransactions['incomingRobuxTotal'] ?? 0
      ];

      if ($this->accountService->create($accountData)) {
        $response["created"][] = [
          "id" => $accountData['id'],
          "name" => $accountData['name'],
          "robux" => $accountData['robux']
        ];
      } else {
        $response["failed"][] = [
          "cookie" => $cookie,
          "message" => "DB insert failed"
        ];
      }
    }
    echo json_encode($response, JSON_PRETTY_PRINT);
  }

  public function updateById($id)
  {
    $id = intval($id);
    $patchData = json_decode(file_get_contents("php://input"), true);
    // var_dump($patchData);
    $response = $this->accountService->updateAccountById($this->currentUser['id'], $id, $patchData);
    header('Content-Type: application/json');
    echo json_encode(["success" => $response]);
  }

  public function updateStatusBulk()
  {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = $input['ids'] ?? [];
    $status = $input['status'] ?? null;

    if (empty($ids) || !$status) {
      http_response_code(400);
      echo json_encode(['success' => false, 'detail' => 'Missing required parameters: ids and status.'], JSON_PRETTY_PRINT);
      return;
    }
    $response = $this->accountService->updateStatusBulk($ids, $status);
    if ($response) {
      echo json_encode(['success' => true, 'updated' => $ids], JSON_PRETTY_PRINT);
      return;
    }
    echo json_encode(['success' => false, 'detail' => 'Failed to update accounts.'], JSON_PRETTY_PRINT);
  }

  public function deleteBulk()
  {
    header('Content-Type: application/json');
    $_DELETE = json_decode(file_get_contents('php://input'), true);
    $ids = $_DELETE['ids'] ?? [];

    if (empty($ids)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'detail' => 'Missing params'], JSON_PRETTY_PRINT);
      return;
    }
    $response = $this->accountService->deleteBulk($ids);
    if ($response) {
      echo json_encode(['success' => true, 'deleted' => $ids], JSON_PRETTY_PRINT);
      return;
    }
    echo json_encode(['success' => false], JSON_PRETTY_PRINT);
  }
}