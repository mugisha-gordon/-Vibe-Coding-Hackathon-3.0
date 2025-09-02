-- Create events table
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATETIME NOT NULL,
    `end_date` DATETIME,
    `location` VARCHAR(255),
    `image_url` VARCHAR(255),
    `event_type` ENUM('past', 'current', 'upcoming') NOT NULL,
    `status` ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    INDEX `idx_event_date` (`event_date`),
    INDEX `idx_event_type` (`event_type`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 