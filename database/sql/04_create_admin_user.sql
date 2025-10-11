-- Create new admin user with proper ID
-- This user will have admin privileges via email matching

-- First, check if admin user already exists
DELETE FROM users WHERE username = 'admin' OR email = 'admin@gmail.com';

-- Insert new admin user (columns: id, name, email, username, password)
INSERT INTO users (id, name, email, username, password) 
VALUES (1, 'Administrator', 'admin@gmail.com', 'admin', 'admin');

-- Display the created user
SELECT id, username, name, email FROM users WHERE username = 'admin';
