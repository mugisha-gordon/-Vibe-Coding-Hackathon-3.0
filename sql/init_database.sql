-- Create database if not exists
CREATE DATABASE IF NOT EXISTS org_management;
USE org_management;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `role` ENUM('admin', 'staff', 'board', 'volunteer') NOT NULL DEFAULT 'volunteer',
    `profile_picture` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create children table
CREATE TABLE IF NOT EXISTS `children` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `age` INT NOT NULL,
    `guardian_name` VARCHAR(100) NOT NULL,
    `guardian_contact` VARCHAR(20) NOT NULL,
    `profile_picture` VARCHAR(255),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Create volunteer_requests table
CREATE TABLE IF NOT EXISTS `volunteer_requests` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `message` TEXT,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create donations table
CREATE TABLE IF NOT EXISTS `donations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `donor_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `child_id` INT,
    `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    `payment_method` VARCHAR(50),
    `transaction_id` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`child_id`) REFERENCES `children`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create feedback table
CREATE TABLE IF NOT EXISTS `feedback` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity_log table
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `action` VARCHAR(50) NOT NULL,
    `details` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create programs table
CREATE TABLE IF NOT EXISTS `programs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE,
    `capacity` INT,
    `current_enrollment` INT DEFAULT 0,
    `status` ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create program_enrollments table
CREATE TABLE IF NOT EXISTS `program_enrollments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `program_id` INT NOT NULL,
    `child_id` INT NOT NULL,
    `enrollment_date` DATE NOT NULL,
    `status` ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`program_id`) REFERENCES `programs`(`id`),
    FOREIGN KEY (`child_id`) REFERENCES `children`(`id`),
    UNIQUE KEY `unique_enrollment` (`program_id`, `child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create success_stories table
CREATE TABLE IF NOT EXISTS `success_stories` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `child_id` INT,
    `image_url` VARCHAR(255),
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`child_id`) REFERENCES `children`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create expenses table
CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `expense_date` DATE NOT NULL,
    `receipt_url` VARCHAR(255),
    `approved_by` INT,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inventory table
CREATE TABLE IF NOT EXISTS `inventory` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `item_name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `quantity` INT NOT NULL,
    `unit` VARCHAR(20) NOT NULL,
    `minimum_quantity` INT DEFAULT 0,
    `location` VARCHAR(100),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inventory_transactions table
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `inventory_id` INT NOT NULL,
    `transaction_type` ENUM('in', 'out') NOT NULL,
    `quantity` INT NOT NULL,
    `transaction_date` DATE NOT NULL,
    `notes` TEXT,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`inventory_id`) REFERENCES `inventory`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create settings table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `description` TEXT,
    `updated_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user if not exists
INSERT INTO `users` (`username`, `password`, `email`, `role`)
SELECT 'admin', '$2y$10$8K1p/a0dR1Ux5Yg3zQb6QOQZQZQZQZQZQZQZQZQZQZQZQZQZQZQ', 'admin@example.com', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin');

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('organization_name', 'Bumbobi Child Support Uganda', 'Organization Name'),
('organization_email', 'info@bumbobi.org', 'Organization Email'),
('organization_phone', '+256 123456789', 'Organization Phone'),
('organization_address', 'Kampala, Uganda', 'Organization Address'),
('currency', 'UGX', 'Default Currency'),
('timezone', 'Africa/Kampala', 'Default Timezone'),
('maintenance_mode', 'false', 'Maintenance Mode Status'),
('donation_goal', '1000000', 'Annual Donation Goal'),
('volunteer_approval_required', 'true', 'Require Approval for Volunteers'),
('enable_registration', 'true', 'Enable User Registration'); 