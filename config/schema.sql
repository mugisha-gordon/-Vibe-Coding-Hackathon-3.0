-- Add profile_picture column to children table
ALTER TABLE children ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- Add profile_picture column to users table (for staff and board members)
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- Add profile_picture column to volunteers table
ALTER TABLE volunteers ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- Create donations table if it doesn't exist
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_name VARCHAR(255) NOT NULL,
    donor_email VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    payment_reference VARCHAR(255),
    message TEXT,
    child_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE SET NULL
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_donation_date ON donations(created_at);
CREATE INDEX IF NOT EXISTS idx_donation_status ON donations(status);
CREATE INDEX IF NOT EXISTS idx_donor_email ON donations(donor_email);
CREATE INDEX IF NOT EXISTS idx_child_id ON donations(child_id); 