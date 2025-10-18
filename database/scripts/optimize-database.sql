-- ========================================
-- Database Optimization Script
-- Social Media Manager - Production
-- ========================================

-- This script contains SQL commands to optimize the database for production
-- Run this after deploying to production and whenever needed for maintenance

-- ========================================
-- 1. ANALYZE TABLES
-- ========================================
-- Update table statistics for the query optimizer

ANALYZE TABLE users;
ANALYZE TABLE posts;
ANALYZE TABLE social_accounts;
ANALYZE TABLE brand_kits;
ANALYZE TABLE analytics;
ANALYZE TABLE media;
ANALYZE TABLE comments;
ANALYZE TABLE post_schedules;

-- ========================================
-- 2. OPTIMIZE TABLES
-- ========================================
-- Defragment and optimize tables

OPTIMIZE TABLE users;
OPTIMIZE TABLE posts;
OPTIMIZE TABLE social_accounts;
OPTIMIZE TABLE brand_kits;
OPTIMIZE TABLE analytics;
OPTIMIZE TABLE media;
OPTIMIZE TABLE comments;
OPTIMIZE TABLE post_schedules;

-- ========================================
-- 3. CHECK AND REPAIR TABLES (if needed)
-- ========================================
-- Uncomment if you need to check table integrity

-- CHECK TABLE users;
-- CHECK TABLE posts;
-- CHECK TABLE social_accounts;

-- If any table needs repair:
-- REPAIR TABLE table_name;

-- ========================================
-- 4. ADDITIONAL INDEXES FOR FULL-TEXT SEARCH
-- ========================================
-- Add full-text indexes for content search

-- For posts content search
ALTER TABLE posts ADD FULLTEXT INDEX posts_content_fulltext (content);

-- For users name search
ALTER TABLE users ADD FULLTEXT INDEX users_name_fulltext (name);

-- ========================================
-- 5. PARTITIONING (Optional - for large datasets)
-- ========================================
-- Partition analytics table by month for better performance
-- Uncomment and modify as needed

/*
ALTER TABLE analytics
PARTITION BY RANGE (YEAR(date) * 100 + MONTH(date)) (
    PARTITION p202501 VALUES LESS THAN (202502),
    PARTITION p202502 VALUES LESS THAN (202503),
    PARTITION p202503 VALUES LESS THAN (202504),
    PARTITION p202504 VALUES LESS THAN (202505),
    PARTITION p202505 VALUES LESS THAN (202506),
    PARTITION p202506 VALUES LESS THAN (202507),
    PARTITION p202507 VALUES LESS THAN (202508),
    PARTITION p202508 VALUES LESS THAN (202509),
    PARTITION p202509 VALUES LESS THAN (202510),
    PARTITION p202510 VALUES LESS THAN (202511),
    PARTITION p202511 VALUES LESS THAN (202512),
    PARTITION p202512 VALUES LESS THAN (202601),
    PARTITION pfuture VALUES LESS THAN MAXVALUE
);
*/

-- ========================================
-- 6. ARCHIVE OLD DATA (Optional)
-- ========================================
-- Move old data to archive tables to improve performance

/*
-- Create archive table for old posts
CREATE TABLE posts_archive LIKE posts;

-- Move posts older than 1 year to archive
INSERT INTO posts_archive
SELECT * FROM posts
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Delete moved posts
DELETE FROM posts
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
*/

-- ========================================
-- 7. VIEW STATISTICS
-- ========================================
-- Check table sizes and row counts

SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC;

-- ========================================
-- 8. SLOW QUERY LOG
-- ========================================
-- Enable slow query log for monitoring
-- Add to my.cnf or run these commands

-- SET GLOBAL slow_query_log = 'ON';
-- SET GLOBAL long_query_time = 2;
-- SET GLOBAL slow_query_log_file = '/var/log/mysql/slow-query.log';

-- ========================================
-- 9. QUERY CACHE (MySQL 5.7 and earlier)
-- ========================================
-- Note: Query cache is deprecated in MySQL 8.0+

-- SET GLOBAL query_cache_size = 268435456; -- 256MB
-- SET GLOBAL query_cache_type = 1;
-- SET GLOBAL query_cache_limit = 2097152; -- 2MB

-- ========================================
-- 10. InnoDB BUFFER POOL
-- ========================================
-- Optimize InnoDB buffer pool (add to my.cnf)

-- innodb_buffer_pool_size = 2G
-- innodb_buffer_pool_instances = 8
-- innodb_log_file_size = 512M
-- innodb_flush_log_at_trx_commit = 2
-- innodb_flush_method = O_DIRECT

-- ========================================
-- NOTES
-- ========================================
-- 1. Run ANALYZE and OPTIMIZE weekly in low-traffic periods
-- 2. Monitor slow queries and add indexes as needed
-- 3. Archive old data quarterly
-- 4. Back up database before running optimization
-- 5. Test queries with EXPLAIN before adding indexes
-- 6. Monitor disk space usage regularly
