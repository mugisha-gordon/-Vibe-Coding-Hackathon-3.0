-- Create database if not exists
CREATE DATABASE IF NOT EXISTS org_management;
USE org_management;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    lock_until DATETIME,
    reset_token VARCHAR(100),
    reset_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create children table
CREATE TABLE IF NOT EXISTS children (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    admission_date DATE NOT NULL,
    status ENUM('Active', 'Inactive', 'Graduated') NOT NULL DEFAULT 'Active',
    medical_conditions TEXT,
    allergies TEXT,
    emergency_contact VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create programs table
CREATE TABLE IF NOT EXISTS programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('Active', 'Inactive', 'Completed') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create staff table
CREATE TABLE IF NOT EXISTS staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    position VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    hire_date DATE,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create success_stories table
CREATE TABLE IF NOT EXISTS success_stories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    child_id INT,
    program_id INT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id),
    FOREIGN KEY (program_id) REFERENCES programs(id)
);

-- Create child_programs table (for many-to-many relationship)
CREATE TABLE IF NOT EXISTS child_programs (
    child_id INT,
    program_id INT,
    enrollment_date DATE NOT NULL,
    status ENUM('Active', 'Completed', 'Dropped') NOT NULL DEFAULT 'Active',
    PRIMARY KEY (child_id, program_id),
    FOREIGN KEY (child_id) REFERENCES children(id),
    FOREIGN KEY (program_id) REFERENCES programs(id)
);

-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(10,2) NOT NULL,
    donor_name VARCHAR(100) NOT NULL,
    donor_email VARCHAR(100) NOT NULL,
    donor_phone VARCHAR(20),
    donor_address TEXT,
    donor_city VARCHAR(100),
    donor_country VARCHAR(100),
    message TEXT,
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create volunteers table
CREATE TABLE IF NOT EXISTS volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    skills TEXT,
    availability TEXT,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create newsletter_subscribers table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create activity_log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data for programs
INSERT INTO programs (name, description, start_date, status) VALUES
('Education Support', 'Providing quality education, school supplies, and tutoring to ensure academic success.', '2023-01-01', 'Active'),
('Health & Nutrition', 'Ensuring proper healthcare, nutrition, and wellness programs for all children.', '2023-01-01', 'Active'),
('Skills Development', 'Training in life skills, vocational skills, and career development programs.', '2023-01-01', 'Active');

-- Insert sample data for success stories
INSERT INTO success_stories (title, content, program_id) VALUES
('From Struggling to Success', 'Sarah went from struggling in school to graduating with honors thanks to our education support program.', 1),
('Healthy and Happy', 'John improved his health and nutrition through our dedicated health program.', 2),
('Skills for Life', 'Emily developed valuable life skills that helped her secure a job after graduation.', 3);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$8K1p/a0dL1LXMIgZ5n0pXe3QKz1QKz1QKz1QKz1QKz1QKz1QKz1QKz1', 'admin@organization.com', 'admin'); 