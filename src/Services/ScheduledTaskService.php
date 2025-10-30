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
        public function updatePendingToUnpendAccounts(string $userId): array
        {
          // $logFile = __DIR__ . '/debug.log';
          // file_put_contents($logFile, "ScheduledTaskService: updatePendingToUnpendAccounts called at " . (new DateTimeImmutable())->format('Y-m-d H:i:s') . "\n", FILE_APPEND);
      
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
              // file_put_contents($logFile, "ScheduledTaskService: Could not find status IDs (pending: {$pendingStatusId}, unpend: {$unpendStatusId})\n", FILE_APPEND);
              error_log("ScheduledTaskService: Could not find status IDs");
              return $stats;
            }
      
            // Find all pending accounts that have reached their unpend_date
            // $accountsToUpdate = $this->findPendingAccountsToUnpend($pendingStatusId, $logFile);
            $accountsToUpdate = $this->findPendingAccountsToUnpend($userId, $pendingStatusId);
            $stats['checked'] = count($accountsToUpdate);
            // file_put_contents($logFile, "ScheduledTaskService: Found {$stats['checked']} accounts to update\n", FILE_APPEND);
      
            if (empty($accountsToUpdate)) {
              return $stats;
            }
      
            // Update each account to Unpend status
            foreach ($accountsToUpdate as $accountId) {
              try {
                // file_put_contents($logFile, "ScheduledTaskService: Attempting to update account {$accountId} to Unpend (status ID: {$unpendStatusId})\n", FILE_APPEND);
                $success = $this->accountRepo->updateStatusBulk([$accountId], $unpendStatusId);
                if ($success) {
                  $stats['updated']++;
                  $stats['account_ids'][] = $accountId;
                  // file_put_contents($logFile, "ScheduledTaskService: Successfully updated account {$accountId}\n", FILE_APPEND);
                } else {
                  $stats['failed']++;
                  // file_put_contents($logFile, "ScheduledTaskService: Failed to update account {$accountId}\n", FILE_APPEND);
                  error_log("ScheduledTaskService: Failed to update account {$accountId}");
                }
              } catch (\Exception $e) {
                $stats['failed']++;
                // file_put_contents($logFile, "ScheduledTaskService: Exception updating account {$accountId}: " . $e->getMessage() . "\n", FILE_APPEND);
                error_log("ScheduledTaskService: Exception updating account {$accountId}: " . $e->getMessage());
              }
            }
      
            if ($stats['updated'] > 0) {
              // file_put_contents($logFile, "ScheduledTaskService: Updated {$stats['updated']} accounts from Pending to Unpend\n", FILE_APPEND);
              error_log("ScheduledTaskService: Updated {$stats['updated']} accounts from Pending to Unpend");
            }
      
          } catch (\Exception $e) {
            // file_put_contents($logFile, "ScheduledTaskService: Error in updatePendingToUnpendAccounts: " . $e->getMessage() . "\n", FILE_APPEND);
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
        // private function findPendingAccountsToUnpend(int $pendingStatusId, string $logFile): array
        private function findPendingAccountsToUnpend(string $userId, int $pendingStatusId): array
        {
          $accountIds = [];
          
          // Get all accounts with the current status
          $allAccounts = $this->accountRepo->findAll($userId, 1000000, 0, null, null, null, 'Pending', 'Pending');
          $now = new DateTimeImmutable();
      
          // file_put_contents($logFile, "  findPendingAccountsToUnpend called. Total accounts found: " . count($allAccounts) . ", Pending Status ID: " . $pendingStatusId . ", Current Time: " . $now->format('Y-m-d H:i:s') . "\n", FILE_APPEND);
      
          foreach ($allAccounts as $accountData) {
            $account = $accountData['model'];
            // file_put_contents($logFile, "    Processing Account ID: " . $account->getId() . ", Status ID: " . $account->getAccountStatusId() . ", Unpend Date: " . ($account->getUnpendDate() ? $account->getUnpendDate()->format('Y-m-d H:i:s') : 'NULL') . "\n", FILE_APPEND);
            
            // Check if account is Pending and has an unpend_date
            if ($account->getAccountStatusId() === $pendingStatusId && $account->getUnpendDate() !== null) {
              $unpendDate = $account->getUnpendDate();
              $unpendDateOnly = new DateTimeImmutable($unpendDate->format('Y-m-d'));
              $nowOnly = new DateTimeImmutable($now->format('Y-m-d'));
      
              // file_put_contents($logFile, "      Comparing Unpend Date Only (" . $unpendDateOnly->format('Y-m-d') . ") <= Current Time Only (" . $nowOnly->format('Y-m-d') . "): " . ($unpendDateOnly <= $nowOnly ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
              
              // If unpend_date has been reached or passed, add to update list
              if ($unpendDateOnly <= $nowOnly) {
                $accountIds[] = $account->getId();
                // file_put_contents($logFile, "      Account {$account->getId()} added to update list.\n", FILE_APPEND);
              }
            }
          }
      
          return $accountIds;
        }  /**
   * Get accounts that are currently Pending and their unpend status
   * Useful for debugging and monitoring
   * 
   * @return array Array of pending accounts with their unpend date info
   */
  public function getPendingAccountsStatus(string $userId): array
  {
    $pendingAccounts = [];
    $pendingStatusId = $this->accountRepo->findAccountStatusId('pending');
    
    if (!$pendingStatusId) {
      return $pendingAccounts;
    }

    $allAccounts = $this->accountRepo->findAll($userId, 1000000, 0, null, null, null, 'Pending', 'Pending');
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
