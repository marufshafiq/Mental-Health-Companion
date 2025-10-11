-- File: database/sql/01_create_messages_table.sql

/**
 * Create messages table for storing chat conversations
 * Supports user messages, bot responses, and counselor interactions
 */

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL COMMENT 'References users.id',
  `sender` ENUM('user','bot','counselor') DEFAULT 'user' COMMENT 'Message sender type',
  `message` TEXT NOT NULL COMMENT 'User message or question',
  `response` TEXT NULL COMMENT 'Bot or counselor response',
  `conversation_id` VARCHAR(100) NULL COMMENT 'Groups related messages',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_conversation_id` (`conversation_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
