-- Migration: project access control, per-project profiles, files, notes
-- Adds colaborador role, project_id to tickets & knowledge_base, new ticket types

-- 1. Add 'colaborador' to users role ENUM
ALTER TABLE users MODIFY COLUMN role ENUM('admin','dev','user','colaborador') DEFAULT 'user';

-- 2. Add project_id to tickets
ALTER TABLE tickets ADD COLUMN project_id INT NULL;
ALTER TABLE tickets ADD CONSTRAINT fk_ticket_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;

-- 3. Add requerimiento and cambio to tickets type ENUM
ALTER TABLE tickets MODIFY COLUMN type ENUM('bug','feature','support','query','requerimiento','cambio') DEFAULT 'support';

-- 4. Add project_id to knowledge_base
ALTER TABLE knowledge_base ADD COLUMN project_id INT NULL;
ALTER TABLE knowledge_base ADD CONSTRAINT fk_kb_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;

-- 5. user_projects: which projects each user can access (many-to-many)
CREATE TABLE IF NOT EXISTS user_projects (
  user_id    INT NOT NULL,
  project_id INT NOT NULL,
  PRIMARY KEY (user_id, project_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- 6. Per-project user profiles
CREATE TABLE IF NOT EXISTS user_project_profiles (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  project_id   INT NOT NULL,
  display_name VARCHAR(100),
  bio          TEXT,
  contact_info VARCHAR(255),
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_project (user_id, project_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- 7. Project files (documents/resources per project)
CREATE TABLE IF NOT EXISTS project_files (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  project_id    INT NOT NULL,
  user_id       INT NOT NULL,
  filename      VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  file_type     VARCHAR(50),
  file_size     INT,
  description   TEXT,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
);

-- 8. Project notes (communication feed per project)
CREATE TABLE IF NOT EXISTS project_notes (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  user_id    INT NOT NULL,
  note       TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
);
