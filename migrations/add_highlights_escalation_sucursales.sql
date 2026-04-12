-- Sucursales table
CREATE TABLE IF NOT EXISTS sucursales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default branches
INSERT INTO sucursales (nombre, descripcion) VALUES
('Sucursal Central', 'Oficina principal'),
('Sucursal Norte', 'Sede norte'),
('Sucursal Sur', 'Sede sur');

-- User sucursal relationship (many-to-many)
CREATE TABLE IF NOT EXISTS user_sucursales (
  user_id INT NOT NULL,
  sucursal_id INT NOT NULL,
  PRIMARY KEY (user_id, sucursal_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE CASCADE
);

-- Add highlight/VIP flag to users
ALTER TABLE users ADD COLUMN is_vip TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE users ADD COLUMN highlight_color VARCHAR(7) NOT NULL DEFAULT '#F59E0B';

-- Add escalation, resolved, sucursal to tickets
ALTER TABLE tickets ADD COLUMN escalation ENUM('none','escalate','no_escalate') NOT NULL DEFAULT 'none';
ALTER TABLE tickets ADD COLUMN is_resolved TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE tickets ADD COLUMN sucursal_id INT NULL;
ALTER TABLE tickets ADD CONSTRAINT fk_ticket_sucursal FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL;
