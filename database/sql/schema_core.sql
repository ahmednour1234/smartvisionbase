CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS countries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  iso2 CHAR(2) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  INDEX(name),
  INDEX(is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  event_date_from DATE NULL,
  event_date_to DATE NULL,
  location VARCHAR(180) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_event_name (name),
  INDEX idx_events_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lost_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_lost_cat (name),
  INDEX idx_lost_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS packages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(255) NOT NULL,
  contact_person VARCHAR(120) NULL,
  contact_mobile VARCHAR(50) NULL,
  contact_email VARCHAR(160) NULL,
  contact_linkedin VARCHAR(255) NULL,
  company_website VARCHAR(255) NULL,
  event_id INT NULL,
  interested_package_id INT NULL,
  expected_value DECIMAL(12,2) NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  probability TINYINT NULL,
  expected_close_date DATE NULL,
  lead_notes TEXT NULL,
  status ENUM('new','contacted','meeting','negotiation','won','lost') NOT NULL DEFAULT 'new',
  lost_category_id INT NULL,
  lost_reason VARCHAR(255) NULL,
  lost_at TIMESTAMP NULL,
  sales_rep_id INT NULL,
  last_meeting DATE NULL,
  next_followup DATE NULL,
  created_by INT NULL,
  updated_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_sales_rep (sales_rep_id),
  INDEX idx_followup (next_followup),
  INDEX idx_event (event_id),
  INDEX idx_status (status),
  INDEX idx_lost_cat (lost_category_id),
  INDEX idx_created_at (created_at),
  INDEX idx_company_name (company_name),

  CONSTRAINT fk_leads_pkg FOREIGN KEY (interested_package_id) REFERENCES packages(id) ON DELETE SET NULL,
  CONSTRAINT fk_leads_lost_cat FOREIGN KEY (lost_category_id) REFERENCES lost_categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_leads_rep FOREIGN KEY (sales_rep_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_leads_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_leads_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lead_countries (
  lead_id INT NOT NULL,
  country_id INT NOT NULL,
  PRIMARY KEY (lead_id, country_id),
  CONSTRAINT fk_lc_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
  CONSTRAINT fk_lc_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS meetings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  lead_id INT NULL,
  meeting_date DATE NOT NULL,
  duration_minutes INT NOT NULL DEFAULT 0,
  meeting_type ENUM('call','online','in_person') NOT NULL DEFAULT 'call',
  notes VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_meetings_user (user_id),
  INDEX idx_meetings_date (meeting_date),
  CONSTRAINT fk_meetings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_meetings_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS lead_activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lead_id INT NOT NULL,
  user_id INT NULL,
  activity_type ENUM('create','update','status_change','meeting','note','import') NOT NULL,
  message VARCHAR(255) NULL,
  meta JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_act_lead (lead_id),
  INDEX idx_act_date (created_at),
  CONSTRAINT fk_act_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
  CONSTRAINT fk_act_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(40) NOT NULL,
  entity_id INT NULL,
  meta JSON NULL,
  ip VARCHAR(64) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id),
  INDEX idx_action (action),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed all countries (ISO 3166-1)

-- Seed a few default packages (Admin can edit/add/delete later)
INSERT INTO packages (name, is_active, sort_order) VALUES
('Diamond Sponsor',1,1),
('Platinum Sponsor',1,2),
('Gold Sponsor',1,3),
('Silver Sponsor',1,4),
('Exhibitor Only',1,5)
ON DUPLICATE KEY UPDATE is_active=VALUES(is_active), sort_order=VALUES(sort_order);

-- Seed default lost categories (Admin can edit later)
INSERT INTO lost_categories (name, is_active, sort_order) VALUES
('Price',1,1),
('Timing',1,2),
('No Budget',1,3),
('Competitor',1,4),
('No Response',1,5)
ON DUPLICATE KEY UPDATE is_active=VALUES(is_active), sort_order=VALUES(sort_order);

-- Seed default event placeholder
INSERT INTO events (name, is_active, sort_order) VALUES
('General',1,1)
ON DUPLICATE KEY UPDATE is_active=VALUES(is_active), sort_order=VALUES(sort_order);


CREATE TABLE IF NOT EXISTS job_runs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_name VARCHAR(80) NOT NULL,
  started_at TIMESTAMP NULL,
  finished_at TIMESTAMP NULL,
  status ENUM('running','success','failed') NOT NULL DEFAULT 'running',
  message VARCHAR(255) NULL,
  INDEX idx_job_name (job_name),
  INDEX idx_finished_at (finished_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
