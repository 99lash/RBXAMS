-- RBXAMS Multi-User Migration Script
-- Generated on 2025-10-27 13:56:42
-- Purpose: Upgrade schema to support multi-user data separation safely

-- STEP 1: Add user_id columns
ALTER TABLE transactions
ADD COLUMN user_id CHAR(36) NULL AFTER account_id;

ALTER TABLE daily_summary
ADD COLUMN user_id CHAR(36) NULL AFTER summary_date;

-- STEP 2: Backfill user_id data
UPDATE transactions t
JOIN accounts a ON t.account_id = a.id
SET t.user_id = a.user_id;

-- Replace this with the actual admin user UUID
UPDATE daily_summary
SET user_id = 'u644i698d784';

-- STEP 3: Enforce constraints
ALTER TABLE transactions
MODIFY COLUMN user_id CHAR(36) NOT NULL,
ADD CONSTRAINT fk_transactions_user
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE daily_summary
MODIFY COLUMN user_id CHAR(36) NOT NULL,
ADD CONSTRAINT fk_daily_summary_user
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE daily_summary
ADD CONSTRAINT unique_user_date UNIQUE (user_id, summary_date);

-- STEP 4: Index optimizations
CREATE INDEX idx_transactions_user_date ON transactions (user_id, created_at);
CREATE INDEX idx_daily_summary_user_date ON daily_summary (user_id, summary_date);

-- STEP 5: Verification (run manually if needed)
-- SELECT id, user_id FROM transactions WHERE user_id IS NOT NULL LIMIT 10;
-- SELECT user_id, summary_date, COUNT(*) 
-- FROM daily_summary 
-- GROUP BY user_id, summary_date 
-- HAVING COUNT(*) > 1;

-- Migration complete.
