-- Create children table
CREATE TABLE IF NOT EXISTS `children` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `date_of_birth` date NOT NULL,
    `gender` enum('Male','Female','Other') NOT NULL,
    `admission_date` date NOT NULL,
    `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample children data
INSERT INTO `children` (`first_name`, `last_name`, `date_of_birth`, `gender`, `admission_date`, `status`) VALUES
('John', 'Doe', '2010-05-15', 'Male', '2024-01-01', 'Active'),
('Jane', 'Smith', '2011-03-20', 'Female', '2024-01-01', 'Active'),
('Michael', 'Johnson', '2012-07-10', 'Male', '2024-01-01', 'Active'),
('Sarah', 'Williams', '2010-11-25', 'Female', '2024-01-01', 'Active'),
('David', 'Brown', '2011-09-05', 'Male', '2024-01-01', 'Active'),
('Emily', 'Davis', '2012-01-30', 'Female', '2024-01-01', 'Active'),
('James', 'Miller', '2010-08-12', 'Male', '2024-01-01', 'Active'),
('Emma', 'Wilson', '2011-12-18', 'Female', '2024-01-01', 'Active'),
('Daniel', 'Moore', '2012-04-22', 'Male', '2024-01-01', 'Active'),
('Olivia', 'Taylor', '2010-06-08', 'Female', '2024-01-01', 'Active'),
('William', 'Anderson', '2011-10-14', 'Male', '2024-01-01', 'Active'); 