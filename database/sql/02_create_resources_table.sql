-- File: database/sql/02_create_resources_table.sql

/**
 * Create resources table for mental health resources
 * Stores helpful resources like articles, videos, hotlines, etc.
 */

CREATE TABLE IF NOT EXISTS `resources` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL COMMENT 'Resource title',
  `url` VARCHAR(1024) NOT NULL COMMENT 'Resource URL or link',
  `description` TEXT NULL COMMENT 'Detailed description of the resource',
  `source` VARCHAR(255) NULL COMMENT 'Source or provider of the resource',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
