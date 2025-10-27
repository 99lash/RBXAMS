<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use DateTimeImmutable;

/**
 * ScheduledTaskService
 * 
 * Handles scheduled/automated tasks for the account system.
 * Primary responsibility: Auto-update Pending accounts to Unpend when unpend_date is reached.
 */
class ScheduledTaskService
{
  private AccountRepository $accountRepo;

  public function __construct(AccountRepository $accountRepo = null)
  {
    $this->accountRepo = $accountRepo ?? new AccountRepository();
  }

  /**
   * Check and update Pending accounts to Unpend status when unpend_date is reached
   * 
   * This method should be called:
   * - Via cron job (recommended for production)
   * - On page load (for development/small scale)
   * - Before rendering account lists
   * 
   * @return array Statistics about the update operation
   */
  public function updatePendingToUnpendAccounts(): array
  {
    $stats = [
      'checked' => 0,
      'updated' => 0,
      'failed' => 0,
      'account_ids' => []
    ];

    try {
      // Get all accounts with Pending status
      $pendingStatusId = $this->accountRepo->findAccountStatusId('pending');
      $unpendStatusId = $this->accountRepo->findAccountStatusId('unpend');

      if (!$pendingStatusId || !$unpendStatusId) {
        error_log("ScheduledTaskService: Could not find status IDs");
        return $stats;
      }

      // Find all pending accounts that have reached their unpend_date
      $accountsToUpdate = $this->findPendingAccountsToUnpend($pendingStatusId);
      $stats['checked'] = count($accountsToUpdate);

      if (empty($accountsToUpdate)) {
        return $stats;
      }

      // Update each account to Unpend status
      foreach ($accountsToUpdate as $accountId) {
        try {
          $success = $this->accountRepo->updateStatusBulk([$accountId], $unpendStatusId);
          if ($success) {
            $stats['updated']++;
            $stats['account_ids'][] = $accountId;
          } else {
            $stats['failed']++;
            error_log("ScheduledTaskService: Failed to update account {$accountId}");
          }
        } catch (\Exception $e) {
          $stats['failed']++;
          error_log("ScheduledTaskService: Exception updating account {$accountId}: " . $e->getMessage());
        }
      }

      if ($stats['updated'] > 0) {
        error_log("ScheduledTaskService: Updated {$stats['updated']} accounts from Pending to Unpend");
      }

    } catch (\Exception $e) {
      error_log("ScheduledTaskService: Error in updatePendingToUnpendAccounts: " . $e->getMessage());
    }

    return $stats;
  }

  /**
   * Find all Pending accounts that should be updated to Unpend
   * 
   * @param int $pendingStatusId The ID of the Pending status
   * @return array Array of account IDs to update
   */
  private function findPendingAccountsToUnpend(int $pendingStatusId): array
  {
    $accountIds = [];
    
    // Get all accounts with the current status
    $allAccounts = $this->accountRepo->findAll();
    $now = new DateTimeImmutable();

    foreach ($allAccounts as $accountData) {
      $account = $accountData['model'];
      
      // Check if account is Pending and has an unpend_date
      if ($account->getAccountStatusId() === $pendingStatusId && $account->getUnpendDate() !== null) {
        $unpendDate = $account->getUnpendDate();
        
        // If unpend_date has been reached or passed, add to update list
        if ($unpendDate <= $now) {
          $accountIds[] = $account->getId();
        }
      }
    }

    return $accountIds;
  }

  /**
   * Get accounts that are currently Pending and their unpend status
   * Useful for debugging and monitoring
   * 
   * @return array Array of pending accounts with their unpend date info
   */
  public function getPendingAccountsStatus(): array
  {
    $pendingAccounts = [];
    $pendingStatusId = $this->accountRepo->findAccountStatusId('pending');
    
    if (!$pendingStatusId) {
      return $pendingAccounts;
    }

    $allAccounts = $this->accountRepo->findAll();
    $now = new DateTimeImmutable();

    foreach ($allAccounts as $accountData) {
      $account = $accountData['model'];
      
      if ($account->getAccountStatusId() === $pendingStatusId) {
        $unpendDate = $account->getUnpendDate();
        $pendingAccounts[] = [
          'id' => $account->getId(),
          'name' => $account->getName(),
          'unpend_date' => $unpendDate ? $unpendDate->format('Y-m-d H:i:s') : null,
          'is_ready_to_unpend' => $unpendDate && $unpendDate <= $now,
          'days_until_unpend' => $unpendDate ? $now->diff($unpendDate)->days : null
        ];
      }
    }

    return $pendingAccounts;
  }
}
