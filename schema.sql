CREATE DATABASE IF NOT EXISTS helpdesk;
USE helpdesk;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','dev','user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200),
  description TEXT NOT NULL,
  type ENUM('bug','feature','support','query') DEFAULT 'support',
  impact ENUM('low','medium','high','critical') DEFAULT 'medium',
  priority_user ENUM('low','medium','high') DEFAULT 'medium',
  status ENUM('new','in_progress','review','done') DEFAULT 'new',
  phase ENUM('information','creation','in_progress','review','done') DEFAULT 'information',
  steps_to_reproduce TEXT,
  technical_context TEXT,
  requester_name VARCHAR(100),
  requester_email VARCHAR(150) NOT NULL,
  user_id INT,
  assigned_to INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE ticket_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  user_id INT NOT NULL,
  note TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE evidences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  user_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  file_type VARCHAR(50),
  file_size INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE knowledge_base (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  content TEXT NOT NULL,
  tags VARCHAR(500),
  links TEXT,
  tag_type ENUM('template','plan','weekly_status','repository','documentation') DEFAULT 'documentation',
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE weekly_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  week_start DATE NOT NULL,
  project ENUM('A','B','C','D') DEFAULT 'A',
  summary TEXT,
  assigned_to INT,
  status ENUM('pending','in_progress','completed') DEFAULT 'pending',
  progress_percent INT DEFAULT 0,
  file_path VARCHAR(255),
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE weekly_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  plan_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  status ENUM('done','not_done') DEFAULT 'not_done',
  FOREIGN KEY (plan_id) REFERENCES weekly_plans(id) ON DELETE CASCADE
);

-- Default admin user (password: password)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@helpdesk.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
-- Fabrizzio admin user (password: fabrizzio)
('Fabrizzio', 'fabrizzio@fabrizzio.com', '$2y$10$wH7QiIUNTopW14eHuaRCVOJW6kIlodq3OW9Pf6WlBlAxEvPoXndKm', 'admin');

-- ─────────────────────────────────────────────────────────
-- API Keys (for REST API authentication)
-- ─────────────────────────────────────────────────────────
CREATE TABLE api_keys (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL COMMENT 'Friendly label, e.g. "CI Pipeline"',
  token VARCHAR(96) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_used_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
