-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    donor_email VARCHAR(100),
    donor_phone VARCHAR(20),
    amount DECIMAL(10, 2) NOT NULL,
    donation_type ENUM('Money', 'Goods', 'Services') NOT NULL,
    donation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(50),
    donation_purpose VARCHAR(255),
    notes TEXT,
    receipt_sent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create donation_items table for goods donations
CREATE TABLE IF NOT EXISTS donation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    item_description TEXT,
    quantity INT NOT NULL,
    estimated_value DECIMAL(10, 2),
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add sample donations for testing
INSERT INTO donations (donor_name, donor_email, donor_phone, amount, donation_type, payment_method, donation_purpose, notes)
VALUES 
('John Smith', 'john@example.com', '555-123-4567', 1000.00, 'Money', 'Credit Card', 'General Fund', 'Monthly donor'),
('Sarah Johnson', 'sarah@example.com', '555-987-6543', 500.00, 'Money', 'Bank Transfer', 'Education Program', 'First time donor'),
('ABC Corporation', 'contact@abc.com', '555-555-5555', 5000.00, 'Money', 'Check', 'Sports Equipment', 'Corporate sponsor'),
('Michael Brown', 'michael@example.com', '555-765-4321', 0.00, 'Goods', NULL, 'Clothing Drive', 'Donated winter clothes');

-- Add sample goods donation items
INSERT INTO donation_items (donation_id, item_name, item_description, quantity, estimated_value)
VALUES 
(4, 'Winter Jackets', 'New children\'s winter jackets, various sizes', 15, 750.00),
(4, 'Boots', 'Winter boots, sizes 1-5', 10, 350.00),
(4, 'Gloves', 'Waterproof gloves, children\'s sizes', 20, 200.00); 