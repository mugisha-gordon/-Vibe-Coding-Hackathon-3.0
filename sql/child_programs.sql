-- Create programs table
CREATE TABLE IF NOT EXISTS `programs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create child_programs table (junction table)
CREATE TABLE IF NOT EXISTS `child_programs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `child_id` int(11) NOT NULL,
    `program_id` int(11) NOT NULL,
    `enrollment_date` date NOT NULL,
    `status` enum('Active','Completed','Dropped') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `child_program` (`child_id`, `program_id`),
    KEY `program_id` (`program_id`),
    CONSTRAINT `child_programs_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `child_programs_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample programs
INSERT INTO `programs` (`name`, `description`, `status`) VALUES
('Education Support', 'Basic education and tutoring program', 'Active'),
('Sports', 'Various sports activities and training', 'Active'),
('Music', 'Music lessons and choir participation', 'Active'),
('Art & Crafts', 'Creative arts and crafts activities', 'Active'),
('Computer Skills', 'Basic computer literacy training', 'Active'),
('Life Skills', 'Essential life skills training', 'Active'),
('Vocational Training', 'Skills development for future employment', 'Active'),
('Health & Nutrition', 'Health education and nutrition support', 'Active'),
('Counseling', 'Psychological support and counseling', 'Active'),
('Cultural Activities', 'Traditional and cultural activities', 'Active');

-- Insert sample child_programs data (assuming you have children with IDs 1-11)
INSERT INTO `child_programs` (`child_id`, `program_id`, `enrollment_date`, `status`) VALUES
(1, 1, '2024-01-01', 'Active'),
(1, 2, '2024-01-01', 'Active'),
(2, 1, '2024-01-01', 'Active'),
(2, 3, '2024-01-01', 'Active'),
(3, 1, '2024-01-01', 'Active'),
(3, 4, '2024-01-01', 'Active'),
(4, 1, '2024-01-01', 'Active'),
(4, 5, '2024-01-01', 'Active'),
(5, 1, '2024-01-01', 'Active'),
(5, 6, '2024-01-01', 'Active'); 