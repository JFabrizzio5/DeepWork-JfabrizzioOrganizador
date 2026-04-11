-- Migration: add projects table and convert weekly_plans.project to VARCHAR
-- Run this against your helpdesk database.

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  color VARCHAR(7) NOT NULL DEFAULT '#3B82F6',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed default projects (matching original A-D enum values)
INSERT IGNORE INTO projects (name, color, description) VALUES
('Project A', '#3B82F6', 'Default project A'),
('Project B', '#8B5CF6', 'Default project B'),
('Project C', '#10B981', 'Default project C'),
('Project D', '#F59E0B', 'Default project D');

-- Convert weekly_plans.project from ENUM to VARCHAR so it can hold any project name
ALTER TABLE weekly_plans MODIFY COLUMN project VARCHAR(100) NOT NULL DEFAULT 'Project A';

-- Update existing rows: map old single-letter values to full project names
UPDATE weekly_plans SET project = 'Project A' WHERE project = 'A';
UPDATE weekly_plans SET project = 'Project B' WHERE project = 'B';
UPDATE weekly_plans SET project = 'Project C' WHERE project = 'C';
UPDATE weekly_plans SET project = 'Project D' WHERE project = 'D';
