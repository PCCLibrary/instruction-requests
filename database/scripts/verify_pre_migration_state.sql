-- Database State Verification - Pre-Migration
-- Run this BEFORE making any database changes
-- Save the output for comparison after migration
-- Date: January 9, 2026

-- ============================================
-- CURRENT STATUS DISTRIBUTION
-- ============================================
SELECT
    'Current Status Distribution' as check_type,
    status,
    COUNT(*) as count
FROM instruction_requests
GROUP BY status
ORDER BY status;

-- ============================================
-- SPECIFIC STATUS COUNTS
-- ============================================
SELECT
    'Scheduled Count' as check_type,
    COUNT(*) as count
FROM instruction_requests
WHERE status = 'scheduled';

SELECT
    'In Progress Count' as check_type,
    COUNT(*) as count
FROM instruction_requests
WHERE status = 'in_progress';

-- ============================================
-- COMBINED COUNT (What we expect after migration)
-- ============================================
SELECT
    'Combined Scheduled + In Progress' as check_type,
    COUNT(*) as expected_in_progress_after_migration
FROM instruction_requests
WHERE status IN ('scheduled', 'in_progress');

-- ============================================
-- TOTAL REQUESTS
-- ============================================
SELECT
    'Total Requests' as check_type,
    COUNT(*) as count
FROM instruction_requests;

-- ============================================
-- RECENT SCHEDULED REQUESTS (Sample)
-- ============================================
SELECT
    'Sample Scheduled Requests' as check_type,
    id,
    status,
    created_at,
    updated_at
FROM instruction_requests
WHERE status = 'scheduled'
ORDER BY updated_at DESC
LIMIT 5;

-- ============================================
-- BACKUP INSTRUCTIONS
-- ============================================
-- Run this command to create a backup:
-- mysqldump -u [username] -p [database_name] instruction_requests > backup_instruction_requests_20260109_pre_migration.sql

-- To create a backup table within the database:
-- CREATE TABLE instruction_requests_backup_scheduled_migration AS
-- SELECT * FROM instruction_requests WHERE status = 'scheduled';
