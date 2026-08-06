-- SmartVision CRM v6.3.1 patch
-- Applies fixes for: duplicate company names, pagination support prerequisites, cron health tracking.
-- Run on existing database (MySQL).

-- Drop UNIQUE uq_company on leads.company_name (if exists)
SET @idx_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'leads'
    AND index_name = 'uq_company'
);
SET @sql := IF(@idx_exists > 0, 'ALTER TABLE leads DROP INDEX uq_company', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add index on created_at (if missing)
SET @idx_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'leads'
    AND index_name = 'idx_created_at'
);
SET @sql := IF(@idx_exists = 0, 'ALTER TABLE leads ADD INDEX idx_created_at (created_at)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add index on company_name (if missing)
SET @idx_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'leads'
    AND index_name = 'idx_company_name'
);
SET @sql := IF(@idx_exists = 0, 'ALTER TABLE leads ADD INDEX idx_company_name (company_name)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create job_runs table for cron health (idempotent)
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
