-- SmartVision CRM v6.3.2 - Performance Indexes (Run Once)
-- NOTE: This script is SAFE: it checks existence before adding indexes.
-- IMPORTANT: Base indexes (created_at, company_name, sales_rep_id, next_followup) are already created in schema_core.sql.
-- This script keeps ONLY the composite dashboard index to avoid redundant duplicates.

-- Composite index: sales_rep_id + status + next_followup (dashboards & sales rep workload)
SET @idx := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name='leads'
    AND index_name='idx_leads_rep_status_followup_v632'
);

SET @sql := IF(
  @idx=0,
  'ALTER TABLE leads ADD INDEX idx_leads_rep_status_followup_v632 (sales_rep_id, status, next_followup)',
  'SELECT "idx_leads_rep_status_followup_v632 already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
