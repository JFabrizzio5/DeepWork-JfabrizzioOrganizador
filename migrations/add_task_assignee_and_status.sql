-- Step 1: Rename existing 'not_done' values to 'pending' before altering the enum
UPDATE weekly_tasks SET status = 'pending' WHERE status = 'not_done';

-- Step 2: Update the status column to support pending / in_progress / done
ALTER TABLE weekly_tasks
    MODIFY COLUMN status ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending';

-- Step 3: Add assigned_to column for per-task member assignment
ALTER TABLE weekly_tasks
    ADD COLUMN assigned_to INT NULL AFTER title;

ALTER TABLE weekly_tasks
    ADD CONSTRAINT fk_wt_assigned_to
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL;
