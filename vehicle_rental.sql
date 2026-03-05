-- Vehicle Rental System Database
CREATE DATABASE IF NOT EXISTS vehicle_rental;
USE vehicle_rental;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    license_number VARCHAR(50),
    role ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Vehicles Table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    license_plate VARCHAR(20) UNIQUE NOT NULL,
    daily_rate DECIMAL(10, 2) NOT NULL,
    status ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_license_plate (license_plate)
);

-- Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_customer_id (customer_id),
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date)
);

-- Payments Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Maintenance Records Table
CREATE TABLE maintenance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    issue_description TEXT NOT NULL,
    maintenance_date DATE NOT NULL,
    completion_date DATE,
    cost DECIMAL(10, 2),
    status ENUM('pending', 'in-progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_status (status)
);

-- Insert Sample Data

-- Admin User
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@vehiclerental.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Staff Users
INSERT INTO users (name, email, password, role) VALUES
('John Staff', 'staff1@vehiclerental.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff'),
('Jane Staff', 'staff2@vehiclerental.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');

-- Customer Users
INSERT INTO users (name, email, password, phone, license_number, role) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0101', 'DL123456', 'customer'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0102', 'DL123457', 'customer'),
('Bob Johnson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0103', 'DL123458', 'customer');

-- Vehicles
INSERT INTO vehicles (make, model, year, license_plate, daily_rate, status) VALUES
('Toyota', 'Corolla', 2023, 'ABC123', 50.00, 'available'),
('Honda', 'Civic', 2022, 'XYZ789', 55.00, 'available'),
('Ford', 'F-150', 2021, 'DEF456', 75.00, 'available'),
('Chevrolet', 'Malibu', 2023, 'GHI123', 60.00, 'available'),
('Toyota', 'Camry', 2022, 'JKL789', 65.00, 'rented'),
('BMW', '3 Series', 2023, 'MNO456', 85.00, 'available'),
('Mercedes', 'C-Class', 2022, 'PQR123', 95.00, 'available'),
('Volkswagen', 'Jetta', 2021, 'STU789', 45.00, 'available');

-- Sample Bookings
INSERT INTO bookings (customer_id, vehicle_id, start_date, end_date, total_cost, status) VALUES
(4, 1, '2026-03-05', '2026-03-07', 100.00, 'pending'),
(5, 2, '2026-03-06', '2026-03-10', 220.00, 'confirmed'),
(6, 3, '2026-03-08', '2026-03-12', 300.00, 'completed');

-- Sample Payments
INSERT INTO payments (booking_id, amount, payment_method, status) VALUES
(2, 220.00, 'credit_card', 'completed'),
(3, 300.00, 'debit_card', 'completed');

-- Create Indexes for Performance
CREATE INDEX idx_booking_date ON bookings (start_date, end_date);
CREATE INDEX idx_vehicle_rental ON bookings (vehicle_id, status);
CREATE INDEX idx_payment_date ON payments (created_at);