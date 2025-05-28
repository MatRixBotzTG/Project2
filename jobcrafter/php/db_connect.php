-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS jobcrafter;

-- Use the newly created database
USE jobcrafter;

-- Table for Workers
CREATE TABLE IF NOT EXISTS workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    mobile VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Hashed password
    country VARCHAR(100),
    state VARCHAR(100),
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Companies
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20) UNIQUE NOT NULL,
    country VARCHAR(100),
    state VARCHAR(100),
    location_lat DECIMAL(10, 8), -- Latitude
    location_lon DECIMAL(11, 8), -- Longitude
    location_address TEXT,
    password VARCHAR(255) NOT NULL, -- Hashed password
    status ENUM('pending', 'approved', 'banned') DEFAULT 'pending', -- Status for admin approval
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Admins (optional, if you want a separate admin table instead of var.php)
-- CREATE TABLE IF NOT EXISTS admins (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(50) UNIQUE NOT NULL,
--     password VARCHAR(255) NOT NULL, -- Hashed password
--     email VARCHAR(255) UNIQUE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
```
