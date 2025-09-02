-- Add new fields to children table
ALTER TABLE children
ADD COLUMN gender ENUM('Male', 'Female', 'Other') DEFAULT 'Other' AFTER age,
ADD COLUMN guardian_email VARCHAR(100) AFTER guardian_contact,
ADD COLUMN guardian_relation VARCHAR(50) DEFAULT 'Parent' AFTER guardian_email,
ADD COLUMN program_interest VARCHAR(50) AFTER guardian_relation,
ADD COLUMN medical_info TEXT AFTER program_interest,
ADD COLUMN status ENUM('Active', 'Inactive') DEFAULT 'Active';

-- Create board_members table if it doesn't exist
CREATE TABLE IF NOT EXISTS board_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    term_start DATE NOT NULL,
    term_end DATE,
    bio TEXT,
    profile_photo VARCHAR(255) DEFAULT 'assets/img/default-profile.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create uploads directory structure for board member photos
-- This is handled in PHP, but including as a reminder

-- Sample board members data
INSERT INTO board_members (name, email, position, phone, term_start, term_end, bio)
VALUES 
('John Smith', 'john.smith@example.com', 'Chairperson', '555-123-4567', '2022-01-01', '2024-12-31', 'John has over 20 years of experience in non-profit leadership and is committed to helping children thrive.'),
('Sarah Johnson', 'sarah.johnson@example.com', 'Vice Chairperson', '555-234-5678', '2022-01-01', '2024-12-31', 'Sarah is a passionate advocate for children\'s rights with a background in education policy.'),
('Michael Williams', 'michael.williams@example.com', 'Treasurer', '555-345-6789', '2022-01-01', '2024-12-31', 'Michael brings 15 years of financial management experience to ensure responsible stewardship of our resources.'),
('Jennifer Davis', 'jennifer.davis@example.com', 'Secretary', '555-456-7890', '2022-01-01', '2024-12-31', 'Jennifer is a former teacher who now works to bridge education and community support systems.');

-- Update existing children with sample data for new fields
UPDATE children SET 
gender = CASE WHEN RAND() < 0.55 THEN 'Male' WHEN RAND() < 0.85 THEN 'Female' ELSE 'Other' END,
guardian_email = CONCAT(LOWER(REPLACE(guardian_name, ' ', '.')), '@example.com'),
guardian_relation = CASE 
    WHEN RAND() < 0.7 THEN 'Parent' 
    WHEN RAND() < 0.8 THEN 'Grandparent' 
    WHEN RAND() < 0.9 THEN 'Aunt/Uncle' 
    ELSE 'Foster Parent' 
END,
program_interest = CASE 
    WHEN RAND() < 0.4 THEN 'Sports' 
    WHEN RAND() < 0.6 THEN 'Education' 
    WHEN RAND() < 0.8 THEN 'Arts' 
    ELSE 'Music' 
END,
medical_info = CASE 
    WHEN RAND() < 0.2 THEN 'Allergies: Peanuts' 
    WHEN RAND() < 0.3 THEN 'Asthma' 
    WHEN RAND() < 0.4 THEN 'ADHD'
    ELSE ''
END;

-- Create uploads directory for children and board_members if needed
-- This is handled in PHP but noted here for documentation purposes 