-- Add assigned_to column to weekly_tasks
ALTER TABLE weekly_tasks ADD COLUMN assigned_to INT NULL AFTER title;
ALTER TABLE weekly_tasks ADD CONSTRAINT fk_wt_assigned_to FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL;

-- Migrate existing status values: 'not_done' -> 'pending'
UPDATE weekly_tasks SET status = 'pending' WHERE status = 'not_done';

-- Expand status ENUM to include in_progress
ALTER TABLE weekly_tasks MODIFY status ENUM('pending','in_progress','done') DEFAULT 'pending';
