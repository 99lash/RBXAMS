<?php

use App\Services\AccountService;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Services\RobloxAPI\RobloxService;
use App\Services\SummaryService;
use App\Models\AccountModel;
use App\Transformers\AccountTransformer;
use App\Utils\AccountType;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
  private $accountRepoMock;
  private $transactionRepoMock;
  private $robloxServiceMock;
  private $summaryServiceMock;
  private $accountService;

  protected function setUp(): void
  {
    $this->accountRepoMock = $this->createMock(AccountRepository::class);
    $this->transactionRepoMock = $this->createMock(TransactionRepository::class);
    $this->robloxServiceMock = $this->createMock(RobloxService::class);
    $this->summaryServiceMock = $this->createMock(SummaryService::class);

    $this->accountService = new AccountService(
      $this->accountRepoMock,
      $this->transactionRepoMock,
      $this->robloxServiceMock,
      $this->summaryServiceMock
    );
  }

  public function test_getAllAccounts_returns_transformed_collection()
  {
    $accountData = [
      [
        'model' => new AccountModel(1, 'user1', 'PENDING', 1, 'test1', 'cookie1', 100, 50, 70),
        'status' => 'pending'
      ],
      [
        'model' => new AccountModel(2, 'user2', 'FASTFLIP', 2, 'test2', 'cookie2', 200, 100, 150),
        'status' => 'unpend'
      ]
    ];

    $this->accountRepoMock->expects($this->once())
      ->method('findALl')
      ->willReturn($accountData);

    $result = $this->accountService->getAllAccounts();

    $this->assertIsArray($result);
    $this->assertCount(2, $result);
    $this->assertEquals(AccountTransformer::transformCollection($accountData), $result);
  }

  public function test_create_pending_account_returns_true()
  {
    $data = [
      'id' => 123,
      'user_id' => 'user1',
      'account_type' => AccountType::PENDING,
      'name' => 'test_account',
      'cookie' => 'test_cookie',
      'robux' => 100,
      'pendingRobuxTotal' => 50,
      'incomingRobuxTotal' => 0
    ];

    $this->accountRepoMock->expects($this->once())
      ->method('findAccountStatusId')
      ->with('pending')
      ->willReturn(1);

    $this->accountRepoMock->expects($this->once())
      ->method('create')
      ->with($this->callback(function ($account) use ($data) {
        $this->assertEquals($data['id'], $account->getId());
        $this->assertEquals(base64_encode($data['cookie']), $account->getCookieEnc());
        $this->assertEquals(1, $account->getAccountStatusId());
        $this->assertEquals($data['pendingRobuxTotal'], $account->getRobux());
        return true;
      }))
      ->willReturn(true);

    $result = $this->accountService->create($data);

    $this->assertTrue($result);
  }

  public function test_create_fastflip_account_returns_true()
  {
    $data = [
      'id' => 456,
      'user_id' => 'user2',
      'account_type' => AccountType::FASTFLIP,
      'name' => 'test_account_2',
      'cookie' => 'test_cookie_2',
      'robux' => 200,
      'pendingRobuxTotal' => 0,
      'incomingRobuxTotal' => 0
    ];

    $this->accountRepoMock->expects($this->once())
      ->method('findAccountStatusId')
      ->with('unpend')
      ->willReturn(2);

    $this->accountRepoMock->expects($this->once())
      ->method('create')
      ->with($this->callback(function ($account) use ($data) {
        $this->assertEquals($data['id'], $account->getId());
        $this->assertEquals(base64_encode($data['cookie']), $account->getCookieEnc());
        $this->assertEquals(2, $account->getAccountStatusId());
        $this->assertEquals($data['robux'], $account->getRobux());
        return true;
      }))
      ->willReturn(true);

    $result = $this->accountService->create($data);

    $this->assertTrue($result);
  }

  public function test_getByCookie_returns_account_when_found()
  {
    $cookie = 'test_cookie';
    $cookieEnc = base64_encode($cookie);
    $account = new AccountModel(1, 'user1', 'PENDING', 1, 'test1', $cookieEnc, 100, 50, 70);

    $this->accountRepoMock->expects($this->once())
      ->method('findByCookie')
      ->with($cookieEnc)
      ->willReturn($account);

    $result = $this->accountService->getByCookie($cookie);

    $this->assertSame($account, $result);
  }

  public function test_getByCookie_returns_null_when_not_found()
  {
    $cookie = 'non_existent_cookie';
    $cookieEnc = base64_encode($cookie);

    $this->accountRepoMock->expects($this->once())
      ->method('findByCookie')
      ->with($cookieEnc)
      ->willReturn(null);

    $result = $this->accountService->getByCookie($cookie);

    $this->assertNull($result);
  }

  public function test_getById_returns_transformed_account_when_found()
  {
    $id = 1;
    $accountData = [
      'model' => new AccountModel($id, 'user1', 'PENDING', 1, 'test1', 'cookie1', 100, 50, 70),
      'status' => 'pending'
    ];

    $this->accountRepoMock->expects($this->once())
      ->method('findById')
      ->with($id)
      ->willReturn($accountData);

    $result = $this->accountService->getById($id);

    $this->assertEquals(AccountTransformer::transform($accountData), $result);
  }

  public function test_getById_returns_null_when_not_found()
  {
    $id = 999;

    $this->accountRepoMock->expects($this->once())
      ->method('findById')
      ->with($id)
      ->willReturn(null);

    $result = $this->accountService->getById($id);

    $this->assertNull($result);
  }

  public function test_updateAccountById_handles_buy_transaction()
  {
    $id = 1;
    $patchData = ['cost_php' => 50.0];
    $account = new AccountModel($id);
    $this->accountRepoMock->expects($this->any())
      ->method('findById')
      ->with($id)
      ->willReturn(['model' => $account, 'status' => 'pending']);

    $this->accountRepoMock->expects($this->once())
      ->method('findAccountStatusId')
      ->with('sold')
      ->willReturn(3);



    $this->transactionRepoMock->expects($this->once())
      ->method('create')
      ->with($id, 'buy', 100, 50.0);

    $this->summaryServiceMock->expects($this->once())
      ->method('updateSummaryOnBuy');

    $this->accountRepoMock->expects($this->once())
      ->method('updatePartial')
      ->willReturn(true);

    $result = $this->accountService->updateAccountById($id, $patchData);

    $this->assertTrue($result);
  }

  public function test_updateAccountById_handles_sell_transaction()
  {
    $id = 1;
    $soldStatusId = 3;
    $patchData = ['account_status_id' => $soldStatusId, 'price_php' => 70.0];
    $account = new AccountModel($id, 'user1', 'PENDING', 1, 'test1', 'cookie1', 100, 50, 0);

    $this->accountRepoMock->expects($this->once())
      ->method('findAccountStatusId')
      ->with('sold')
      ->willReturn($soldStatusId);

    $this->accountRepoMock->expects($this->any())
      ->method('findById')
      ->with($id)
      ->willReturn(['model' => $account, 'status' => 'pending']);

    $this->transactionRepoMock->expects($this->once())
      ->method('create')
      ->with($id, 'sell', 100, 70.0);

    $this->summaryServiceMock->expects($this->once())
      ->method('updateSummaryOnSell');

    $this->accountRepoMock->expects($this->once())
      ->method('updatePartial')
      ->willReturn(true);

    $result = $this->accountService->updateAccountById($id, $patchData);

    $this->assertTrue($result);
  }

  public function test_updateStatusBulk_handles_sell_transaction()
  {
    $ids = [1, 2];
    $status = 3;
    $soldStatusId = 3;

    $account1 = new AccountModel(1, 'user1', 'PENDING', 1, 'test1', 'cookie1', 100, 50, 70);
    $account2 = new AccountModel(2, 'user2', 'FASTFLIP', 2, 'test2', 'cookie2', 200, 100, 150);

    $this->accountRepoMock->expects($this->at(0))
      ->method('findAccountStatusId')
      ->with('sold')
      ->willReturn($soldStatusId);

    $this->accountRepoMock->expects($this->at(1))
      ->method('findById')
      ->with(1)
      ->willReturn(['model' => $account1]);

    $this->transactionRepoMock->expects($this->at(2))
      ->method('create')
      ->with(1, 'sell', 100, 70.0);

    $this->accountRepoMock->expects($this->at(3))
      ->method('findById')
      ->with(2)
      ->willReturn(['model' => $account2]);

    $this->transactionRepoMock->expects($this->at(4))
      ->method('create')
      ->with(2, 'sell', 200, 150.0);

    $this->summaryServiceMock->expects($this->exactly(2))
      ->method('updateSummaryOnSell');

    $this->accountRepoMock->expects($this->at(5))
      ->method('updateStatusBulk')
      ->with($ids, $status)
      ->willReturn(true);

    $result = $this->accountService->updateStatusBulk($ids, $status);

    $this->assertTrue($result);
  }
}
