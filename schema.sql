CREATE DATABASE IF NOT EXISTS unilocker_v1;
USE unilocker_v1;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255), -- VULNERABLE: Storing plaintext or weak MD5
    role VARCHAR(20) DEFAULT 'student'
);

CREATE TABLE lockers (
    locker_id INT AUTO_INCREMENT PRIMARY KEY,
    locker_number VARCHAR(10),
    status VARCHAR(20) DEFAULT 'Available'
);

CREATE TABLE deliveries (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    receiver_id INT,
    locker_id INT,
    pin VARCHAR(10), -- VULNERABLE: Storing plaintext PIN
    photo_path VARCHAR(255),
    deposit_type VARCHAR(20),
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    ip_address VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dummy Data (Passwords are plaintext "password123" for vulnerable testing)
INSERT INTO users (student_id, name, email, password, role) VALUES
('ADMIN001', 'System Admin', 'admin@uni.edu', 'password123', 'admin'),
('A22CS1234', 'John Doe', 'john@uni.edu', 'password123', ' student'),
('A22CS5678', 'Jane Smith', 'jane@uni.edu', 'password123', 'student');

INSERT INTO lockers (locker_number, status) VALUES
('A-01', 'Available'), ('A-02', 'Available'), ('A-03', 'Occupied');