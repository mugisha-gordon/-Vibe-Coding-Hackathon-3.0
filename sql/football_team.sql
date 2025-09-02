-- Create football_team table
CREATE TABLE IF NOT EXISTS `football_team` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `child_id` int(11) NOT NULL,
    `position` enum('Goalkeeper','Defender','Midfielder','Forward') NOT NULL,
    `jersey_number` int(11) NOT NULL,
    `join_date` date NOT NULL,
    `status` enum('Active','Inactive','Injured') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `jersey_number` (`jersey_number`),
    KEY `child_id` (`child_id`),
    CONSTRAINT `football_team_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample data
INSERT INTO `football_team` (`child_id`, `position`, `jersey_number`, `join_date`, `status`) VALUES
(1, 'Goalkeeper', 1, '2024-01-01', 'Active'),
(2, 'Defender', 2, '2024-01-01', 'Active'),
(3, 'Defender', 3, '2024-01-01', 'Active'),
(4, 'Defender', 4, '2024-01-01', 'Active'),
(5, 'Defender', 5, '2024-01-01', 'Active'),
(6, 'Midfielder', 6, '2024-01-01', 'Active'),
(7, 'Midfielder', 7, '2024-01-01', 'Active'),
(8, 'Midfielder', 8, '2024-01-01', 'Active'),
(9, 'Forward', 9, '2024-01-01', 'Active'),
(10, 'Forward', 10, '2024-01-01', 'Active'),
(11, 'Forward', 11, '2024-01-01', 'Active'); 