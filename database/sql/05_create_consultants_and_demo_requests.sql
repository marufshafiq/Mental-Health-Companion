-- File: database/04_create_consultants_and_demo_requests.sql

-- ===================================================
-- Feature 6: User Profile - Consultants & Demo Requests
-- Creates tables for consultant listing and demo booking
-- ===================================================

-- Create consultants table
CREATE TABLE IF NOT EXISTS `consultants` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `title` VARCHAR(150) NULL,
  `bio` TEXT NULL,
  `contact_info` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create demo_requests table
CREATE TABLE IF NOT EXISTS `demo_requests` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NULL,
  `consultant_id` INT NULL,
  `preferred_datetime` DATETIME NULL,
  `message` TEXT NULL,
  `status` ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_consultant_id` (`consultant_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add bio and profile_image columns to users table if they don't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `bio` TEXT NULL AFTER `password`,
ADD COLUMN IF NOT EXISTS `profile_image` VARCHAR(255) NULL AFTER `bio`;

-- Insert sample consultants
INSERT INTO `consultants` (`name`, `title`, `bio`, `contact_info`) VALUES
('Dr. Sarah Johnson', 'Licensed Clinical Psychologist', 'Specializing in CBT and anxiety disorders with 15+ years of experience.', 'sarah.johnson@mentalhealth.com'),
('Dr. Michael Chen', 'Psychiatrist', 'Expert in depression treatment and medication management.', 'michael.chen@mentalhealth.com'),
('Dr. Emily Rodriguez', 'Mental Health Counselor', 'Focuses on stress management and mindfulness-based therapies.', 'emily.rodriguez@mentalhealth.com'),
('Dr. James Williams', 'Clinical Therapist', 'Specializes in trauma and PTSD treatment using evidence-based approaches.', 'james.williams@mentalhealth.com')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);
