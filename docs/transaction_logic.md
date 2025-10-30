
# Transaction Trigger System Implementation

## 1️⃣ SQL DDL Statements

These statements modify the existing schema to support the new transaction logic.

```sql
ALTER TABLE `transactions`
ADD COLUMN `txn_status` ENUM('active', 'voided', 'correction') NOT NULL DEFAULT 'active' AFTER `amount`,
ADD COLUMN `related_txn_id` CHAR(36) NULL DEFAULT NULL AFTER `txn_status`,
ADD COLUMN `reason` VARCHAR(255) NULL DEFAULT NULL AFTER `related_txn_id`,
ADD COLUMN `account_type` VARCHAR(20) NULL DEFAULT NULL AFTER `reason`;

ALTER TABLE `daily_summary`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`summary_date`, `user_id`);
```

## 2️⃣ SQL Stored Procedure: `recompute_daily_summary`

This procedure recalculates the daily summary for a specific user and date based on active transactions.

```sql
DELIMITER $$

CREATE OR REPLACE PROCEDURE `recompute_daily_summary`(IN p_user_id CHAR(36), IN p_summary_date DATE)
BEGIN
    -- Declare variables to hold aggregated values
    DECLARE v_total_buy DECIMAL(10, 2) DEFAULT 0;
    DECLARE v_total_sell DECIMAL(10, 2) DEFAULT 0;
    DECLARE v_buy_count INT DEFAULT 0;
    DECLARE v_sell_count INT DEFAULT 0;
    DECLARE v_total_profit_loss DECIMAL(10, 2) DEFAULT 0;

    -- Atomically calculate totals from active transactions for the given user and date
    SELECT
        COALESCE(SUM(CASE WHEN transaction_type = 'BUY' THEN amount ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN transaction_type = 'SELL' THEN amount ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN transaction_type = 'BUY' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN transaction_type = 'SELL' THEN 1 ELSE 0 END), 0)
    INTO
        v_total_buy, v_total_sell, v_buy_count, v_sell_count
    FROM
        `transactions`
    WHERE
        `user_id` = p_user_id
        AND DATE(`transaction_date`) = p_summary_date
        AND `txn_status` = 'active';

    -- Calculate total profit/loss
    SET v_total_profit_loss = v_total_sell - v_total_buy;

    -- Insert or update the daily summary table
    INSERT INTO `daily_summary` (
        `summary_id`,
        `user_id`,
        `summary_date`,
        `total_buy`,
        `total_sell`,
        `buy_count`,
        `sell_count`,
        `total_profit_loss`,
        `last_updated`
    )
    VALUES (
        UUID(),
        p_user_id,
        p_summary_date,
        v_total_buy,
        v_total_sell,
        v_buy_count,
        v_sell_count,
        v_total_profit_loss,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        `total_buy` = VALUES(`total_buy`),
        `total_sell` = VALUES(`total_sell`),
        `buy_count` = VALUES(`buy_count`),
        `sell_count` = VALUES(`sell_count`),
        `total_profit_loss` = VALUES(`total_profit_loss`),
        `last_updated` = NOW();

END$$

DELIMITER ;
```

## 3️⃣ Example Transactional SQL Sequences

### Workflow: User Sets or Edits Cost PHP (Correction)

```sql
-- Input variables
SET @user_id = 'user-uuid-123';
SET @account_id = 'account-uuid-456';
SET @new_cost_php = 150.00;
SET @reason = 'User updated cost';

BEGIN;

-- Find the existing active BUY transaction for the account
SELECT `transaction_id` INTO @old_txn_id
FROM `transactions`
WHERE `account_id` = @account_id AND `transaction_type` = 'BUY' AND `txn_status` = 'active'
LIMIT 1;

-- If an active BUY transaction exists, void it
IF @old_txn_id IS NOT NULL THEN
    UPDATE `transactions`
    SET `txn_status` = 'voided', `reason` = 'Corrected by new entry'
    WHERE `transaction_id` = @old_txn_id;
END IF;

-- Insert the new active BUY transaction
SET @new_txn_id = UUID();
INSERT INTO `transactions` (`transaction_id`, `account_id`, `user_id`, `transaction_type`, `amount`, `txn_status`, `related_txn_id`, `reason`)
VALUES (@new_txn_id, @account_id, @user_id, 'BUY', @new_cost_php, 'active', @old_txn_id, @reason);

-- Update the account's cost
UPDATE `accounts`
SET `cost_php` = @new_cost_php
WHERE `account_id` = @account_id;

-- Recompute the summary for today
CALL recompute_daily_summary(@user_id, CURDATE());

COMMIT;
```

### Workflow: User Sets Account Status to Sold

```sql
-- Input variables
SET @user_id = 'user-uuid-123';
SET @account_id = 'account-uuid-789';
SET @sold_price = 250.00;
SET @reason = 'Account sold by user';

BEGIN;

-- Void any existing active SELL transaction for this account
UPDATE `transactions`
SET `txn_status` = 'voided', `reason` = 'Superseded by new SELL transaction'
WHERE `account_id` = @account_id AND `transaction_type` = 'SELL' AND `txn_status` = 'active';

-- Insert the new active SELL transaction
INSERT INTO `transactions` (`transaction_id`, `account_id`, `user_id`, `transaction_type`, `amount`, `txn_status`, `reason`)
VALUES (UUID(), @account_id, @user_id, 'SELL', @sold_price, 'active', @reason);

-- Update the account status and sold date
UPDATE `accounts`
SET `account_status_id` = 'sold', `sold_date` = NOW()
WHERE `account_id` = @account_id;

-- Recompute the summary for today
CALL recompute_daily_summary(@user_id, CURDATE());

COMMIT;
```

### Workflow: User Reverts "Sold" to "Unpend"

```sql
-- Input variables
SET @user_id = 'user-uuid-123';
SET @account_id = 'account-uuid-789';
SET @reason = 'Reverted from Sold to Unpend by user';

BEGIN;

-- Void the active SELL transaction associated with this account
UPDATE `transactions`
SET `txn_status` = 'voided', `reason` = @reason
WHERE `account_id` = @account_id AND `transaction_type` = 'SELL' AND `txn_status` = 'active';

-- Update the account status and clear the sold date
UPDATE `accounts`
SET `account_status_id` = 'unpend', `sold_date` = NULL
WHERE `account_id` = @account_id;

-- Recompute the summary for the date the transaction was originally made
-- (Assuming we can get this date, otherwise use today)
-- For simplicity, we use CURDATE(). A more robust solution might need to find the original transaction date.
CALL recompute_daily_summary(@user_id, CURDATE());

COMMIT;
```

## 4️⃣ PHP Controller-Level Pseudocode

This pseudocode demonstrates how to implement the transaction logic in a PHP controller, using PDO for database interactions.

```php
<?php

// File: src/Controllers/AccountController.php (Conceptual)

class AccountController {

    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Handles updating an account's cost_php, creating a BUY transaction.
     */
    public function updateAccountCost(string $accountId, string $userId, float $newCost, string $reason) {
        header('Content-Type: application/json');
        
        try {
            $this->pdo->beginTransaction();

            // 1. Find and void the existing active BUY transaction
            $stmt = $this->pdo->prepare(
                "SELECT transaction_id FROM transactions WHERE account_id = ? AND transaction_type = 'BUY' AND txn_status = 'active' LIMIT 1"
            );
            $stmt->execute([$accountId]);
            $oldTxnId = $stmt->fetchColumn();

            if ($oldTxnId) {
                $stmt = $this->pdo->prepare(
                    "UPDATE transactions SET txn_status = 'voided', reason = 'Corrected by new entry' WHERE transaction_id = ?"
                );
                $stmt->execute([$oldTxnId]);
            }

            // 2. Insert the new active BUY transaction
            $newTxnId = Ramsey\Uuid\Uuid::uuid4()->toString(); // Assuming UUID library
            $stmt = $this->pdo->prepare(
                "INSERT INTO transactions (transaction_id, account_id, user_id, transaction_type, amount, txn_status, related_txn_id, reason) VALUES (?, ?, ?, 'BUY', ?, 'active', ?, ?)"
            );
            $stmt->execute([$newTxnId, $accountId, $userId, $newCost, $oldTxnId, $reason]);

            // 3. Update the account's cost
            $stmt = $this->pdo->prepare("UPDATE accounts SET cost_php = ? WHERE account_id = ?");
            $stmt->execute([$newCost, $accountId]);

            // 4. Recompute the daily summary
            $stmt = $this->pdo->prepare("CALL recompute_daily_summary(?, CURDATE())");
            $stmt->execute([$userId]);

            $this->pdo->commit();

            echo json_encode(['status' => 'success', 'affected_account_id' => $accountId]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Handles setting an account's status to "Sold", creating a SELL transaction.
     */
    public function setAccountToSold(string $accountId, string $userId, float $soldPrice, string $reason) {
        header('Content-Type: application/json');

        try {
            $this->pdo->beginTransaction();

            // 1. Void any previous active SELL transaction for this account
            $stmt = $this->pdo->prepare(
                "UPDATE transactions SET txn_status = 'voided', reason = 'Superseded by new SELL' WHERE account_id = ? AND transaction_type = 'SELL' AND txn_status = 'active'"
            );
            $stmt->execute([$accountId]);

            // 2. Insert the new active SELL transaction
            $stmt = $this->pdo->prepare(
                "INSERT INTO transactions (transaction_id, account_id, user_id, transaction_type, amount, txn_status, reason) VALUES (UUID(), ?, ?, 'SELL', ?, 'active', ?)"
            );
            $stmt->execute([$accountId, $userId, $soldPrice, $reason]);

            // 3. Update the account status
            $stmt = $this->pdo->prepare("UPDATE accounts SET account_status_id = 'sold', sold_date = NOW() WHERE account_id = ?");
            $stmt->execute([$accountId]);

            // 4. Recompute summary
            $stmt = $this->pdo->prepare("CALL recompute_daily_summary(?, CURDATE())");
            $stmt->execute([$userId]);

            $this->pdo->commit();

            echo json_encode(['status' => 'success', 'affected_account_id' => $accountId]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Reverts a "Sold" account back to "Unpend", voiding the SELL transaction.
     */
    public function revertSoldToUnpend(string $accountId, string $userId, string $reason) {
        header('Content-Type: application/json');

        try {
            $this->pdo->beginTransaction();

            // 1. Find the original transaction date before voiding
            $stmt = $this->pdo->prepare(
                "SELECT transaction_date FROM transactions WHERE account_id = ? AND transaction_type = 'SELL' AND txn_status = 'active' ORDER BY transaction_date DESC LIMIT 1"
            );
            $stmt->execute([$accountId]);
            $transactionDate = $stmt->fetchColumn();
            $summaryDate = $transactionDate ? (new DateTime($transactionDate))->format('Y-m-d') : date('Y-m-d');


            // 2. Void the active SELL transaction
            $stmt = $this->pdo->prepare(
                "UPDATE transactions SET txn_status = 'voided', reason = ? WHERE account_id = ? AND transaction_type = 'SELL' AND txn_status = 'active'"
            );
            $stmt->execute([$reason, $accountId]);

            // 3. Update account status
            $stmt = $this->pdo->prepare("UPDATE accounts SET account_status_id = 'unpend', sold_date = NULL WHERE account_id = ?");
            $stmt->execute([$accountId]);

            // 4. Recompute summary for the original transaction date
            $stmt = $this->pdo->prepare("CALL recompute_daily_summary(?, ?)");
            $stmt->execute([$userId, $summaryDate]);

            $this->pdo->commit();

            echo json_encode(['status' => 'success', 'affected_account_id' => $accountId]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
?>
```
