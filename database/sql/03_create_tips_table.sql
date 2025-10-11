-- File: database/sql/03_create_tips_table.sql

/**
 * Create tips table for daily mental health tips
 * Stores helpful tips and suggestions for users
 * Supports Observer pattern for tip notifications
 */

CREATE TABLE IF NOT EXISTS `tips` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tip_text` TEXT NOT NULL COMMENT 'Mental health tip content',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
