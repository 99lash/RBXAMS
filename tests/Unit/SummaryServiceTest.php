<?php

use App\Services\SummaryService;
use App\Repositories\SummaryRepository;
use App\Models\SummaryModel;
use App\Models\AccountModel;
use App\Utils\AccountType;
use PHPUnit\Framework\TestCase;

class SummaryServiceTest extends TestCase
{
  private $summaryRepoMock;
  private $summaryService;

  protected function setUp(): void
  {
    $this->summaryRepoMock = $this->createMock(SummaryRepository::class);
    $this->summaryService = new SummaryService($this->summaryRepoMock);
  }

  public function test_getSummaryForDate_creates_new_summary_if_not_exists()
  {
    $date = '2025-10-18';
    $this->summaryRepoMock->expects($this->once())
      ->method('create')
      ->with($date);

    $this->summaryRepoMock->expects($this->exactly(2))
      ->method('findByDate')
      ->with($date)
      ->willReturnOnConsecutiveCalls(null, new SummaryModel($date));

    $this->summaryService->getSummaryForDate($date);
  }

  public function test_updateSummaryOnBuy_updates_pending_summary()
  {
    $today = (new DateTime())->format('Y-m-d');
    $summary = new SummaryModel($today);
    $account = $this->createMock(AccountModel::class);

    $account->method('getAccountType')->willReturn(AccountType::PENDING);
    $account->method('getRobux')->willReturn(100.0);
    $account->method('getCostPhp')->willReturn(50.0);

    $this->summaryRepoMock->expects($this->exactly(2))
      ->method('findByDate')
      ->with($today)
      ->willReturn($summary);

    $this->summaryRepoMock->expects($this->once())
      ->method('update')
      ->with($this->callback(function ($updatedSummary) {
        $this->assertEquals(100.0, $updatedSummary->getPendingRobuxBought());
        $this->assertEquals(50.0, $updatedSummary->getPendingExpensesPhp());
        return true;
      }));

    $this->summaryService->updateSummaryOnBuy($account);
  }

  public function test_updateSummaryOnBuy_updates_fastflip_summary()
  {
    $today = (new DateTime())->format('Y-m-d');
    $summary = new SummaryModel($today);
    $account = $this->createMock(AccountModel::class);

    $account->method('getAccountType')->willReturn(AccountType::FASTFLIP);
    $account->method('getRobux')->willReturn(200.0);
    $account->method('getCostPhp')->willReturn(100.0);

    $this->summaryRepoMock->expects($this->exactly(2))
      ->method('findByDate')
      ->with($today)
      ->willReturn($summary);

    $this->summaryRepoMock->expects($this->once())
      ->method('update')
      ->with($this->callback(function ($updatedSummary) {
        $this->assertEquals(200.0, $updatedSummary->getFastflipRobuxBought());
        $this->assertEquals(100.0, $updatedSummary->getFastflipExpensesPhp());
        return true;
      }));

    $this->summaryService->updateSummaryOnBuy($account);
  }

  public function test_updateSummaryOnSell_updates_pending_summary()
  {
    $today = (new DateTime())->format('Y-m-d');
    $summary = new SummaryModel($today);
    $account = $this->createMock(AccountModel::class);

    $account->method('getAccountType')->willReturn(AccountType::PENDING);
    $account->method('getRobux')->willReturn(100.0);
    $account->method('getCostPhp')->willReturn(50.0);
    $account->method('getPricePhp')->willReturn(70.0);

    $this->summaryRepoMock->expects($this->exactly(2))
      ->method('findByDate')
      ->with($today)
      ->willReturn($summary);

    $this->summaryRepoMock->expects($this->once())
      ->method('update')
      ->with($this->callback(function ($updatedSummary) {
        $this->assertEquals(100.0, $updatedSummary->getPendingRobuxSold());
        $this->assertEquals(20.0, $updatedSummary->getPendingProfitPhp());
        return true;
      }));

    $this->summaryService->updateSummaryOnSell($account);
  }

  public function test_updateSummaryOnSell_updates_fastflip_summary()
  {
    $today = (new DateTime())->format('Y-m-d');
    $summary = new SummaryModel($today);
    $account = $this->createMock(AccountModel::class);

    $account->method('getAccountType')->willReturn(AccountType::FASTFLIP);
    $account->method('getRobux')->willReturn(200.0);
    $account->method('getCostPhp')->willReturn(100.0);
    $account->method('getPricePhp')->willReturn(150.0);

    $this->summaryRepoMock->expects($this->exactly(2))
      ->method('findByDate')
      ->with($today)
      ->willReturn($summary);

    $this->summaryRepoMock->expects($this->once())
      ->method('update')
      ->with($this->callback(function ($updatedSummary) {
        $this->assertEquals(200.0, $updatedSummary->getFastflipRobuxSold());
        $this->assertEquals(50.0, $updatedSummary->getFastflipProfitPhp());
        return true;
      }));

    $this->summaryService->updateSummaryOnSell($account);
  }
}
