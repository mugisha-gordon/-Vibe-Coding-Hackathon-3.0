-- Create database
CREATE DATABASE IF NOT EXISTS org_management;
USE org_management;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$8K1p/a0dL1LXMIgZ5n0pXe0.5q5.5q5.5q5.5q5.5q5.5q5.5q5', 'admin@organization.com', 'admin');

-- Create children table
CREATE TABLE IF NOT EXISTS children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    admission_date DATE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create football_team table
CREATE TABLE IF NOT EXISTS football_team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    position VARCHAR(50),
    jersey_number INT,
    join_date DATE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    FOREIGN KEY (child_id) REFERENCES children(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create staff_assignments table
CREATE TABLE IF NOT EXISTS staff_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    child_id INT NOT NULL,
    assignment_type ENUM('Care', 'Football') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    FOREIGN KEY (staff_id) REFERENCES users(id),
    FOREIGN KEY (child_id) REFERENCES children(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 