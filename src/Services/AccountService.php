<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Services\RobloxAPI\RobloxService;
use App\Utils\AccountType;
use App\Models\AccountModel;

class AccountService
{
  private AccountRepository $repo;
  private RobloxService $robloxService;

  public function __construct()
  {
    $this->repo = new AccountRepository();
    $this->robloxService = new RobloxService();
  }

  public function getAllAccounts(): array
  {
    return $this->repo->findALl();
  }

  public function create(array $data): bool
  {
    // Encrypt the cookie securely
    $status = $data['account_type'] == AccountType::PENDING ? 'pending' : 'unpend';
    $statusId = $this->repo->findAccountStatusId($status);
    $cookieEnc = base64_encode($data['cookie'] ?? '');

    $account = AccountModel::fromArray($data);
    $account
      ->setCookieEnc($cookieEnc)
      ->setAccountStatusId($statusId)
      ->setRobux($status == AccountType::PENDING ? $data['pendingRobuxTotal'] : $data['robux']);
    // var_dump($account);
    return $this->repo->create($account);
  }

  public function getByCookie($cookie): ?AccountModel
  {
    $cookieEnc = base64_encode($cookie) ?? '';
    return $this->repo->findByCookie($cookieEnc);
  }

  public function getById($id)
  {
    return $this->repo->findById($id);
  }

  public function updateAccountById($id)
  {
    $patchData = json_decode(file_get_contents("php://input"), true);

    $account = new AccountModel($id);

    foreach ($patchData as $field => $value) {
      $method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
      if (method_exists($account, $method)) {
        $account->$method($value);
      }
    }

    return $this->repo->updatePartial($account);
  }

  public function updateStatusBulk($ids, $status)
  {
    return $this->repo->updateStatusBulk($ids, $status);
  }

  public function deleteBulk($ids)
  {
    return $this->repo->deleteBulk($ids);
  }
}