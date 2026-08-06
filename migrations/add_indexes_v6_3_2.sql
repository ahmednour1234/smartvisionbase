-- SmartVision CRM v6.3.2 - Performance Indexes (Run Once)
-- NOTE: This script is SAFE: it checks existence before adding indexes.

-- leads.created_at
SET @idx := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name='leads' AND index_name='idx_leads_created_at_v632');
SET @sql := IF(@idx=0, 'ALTER TABLE leads ADD INDEX idx_leads_created_at_v632 (created_at)', 'SELECT "idx_leads_created_at_v632 already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- leads.company_name (for LIKE searches and ordering)
SET @idx := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name='leads' AND index_name='idx_leads_company_name_v632');
SET @sql := IF(@idx=0, 'ALTER TABLE leads ADD INDEX idx_leads_company_name_v632 (company_name)', 'SELECT "idx_leads_company_name_v632 already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- leads.sales_rep_id (My Leads filtering)
SET @idx := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name='leads' AND index_name='idx_leads_sales_rep_v632');
SET @sql := IF(@idx=0, 'ALTER TABLE leads ADD INDEX idx_leads_sales_rep_v632 (sales_rep_id)', 'SELECT "idx_leads_sales_rep_v632 already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- leads.next_followup (daily followups)
SET @idx := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name='leads' AND index_name='idx_leads_next_followup_v632');
SET @sql := IF(@idx=0, 'ALTER TABLE leads ADD INDEX idx_leads_next_followup_v632 (next_followup)', 'SELECT "idx_leads_next_followup_v632 already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Composite index: sales_rep_id + status + next_followup (dashboards)
SET @idx := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name='leads' AND index_name='idx_leads_rep_status_followup_v632');
SET @sql := IF(@idx=0, 'ALTER TABLE leads ADD INDEX idx_leads_rep_status_followup_v632 (sales_rep_id, status, next_followup)', 'SELECT "idx_leads_rep_status_followup_v632 already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
