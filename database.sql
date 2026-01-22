-- Database Schema for Hospital Management System

CREATE DATABASE IF NOT EXISTS hospital_system;
USE hospital_system;

-- Users Table (Authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Doctor', 'Receptionist', 'Patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender VARCHAR(20) NOT NULL,
    occupation VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_rel VARCHAR(50),
    chronic_conditions TEXT,
    allergies TEXT,
    current_medications TEXT,
    smoking_status VARCHAR(20),
    alcohol_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Doctors Table
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    status ENUM('Available', 'In Surgery', 'Off Duty') DEFAULT 'Available',
    schedule TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Billing Table
CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Paid', 'Overdue') DEFAULT 'Pending',
    invoice_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- SEED DATA --

-- 1. Admin
INSERT INTO users (email, password, role) VALUES 
('admin@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'); 
-- Password: password

-- 2. Doctor User + Profile
INSERT INTO users (email, password, role) VALUES 
('doctor@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Doctor');
-- Password: password
SET @doc_user_id = LAST_INSERT_ID();

INSERT INTO doctors (user_id, full_name, specialization, status) VALUES 
(@doc_user_id, 'Dr. Sarah Jenkins', 'Cardiology', 'Available');
SET @doc_id = LAST_INSERT_ID();

-- 3. Patient User + Profile
INSERT INTO users (email, password, role) VALUES 
('patient@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patient');
-- Password: password
SET @pat_user_id = LAST_INSERT_ID();

INSERT INTO patients (user_id, full_name, date_of_birth, gender, phone, email, address, allergies) VALUES 
(@pat_user_id, 'John Doe', '1985-06-15', 'Male', '555-0101', 'patient@medicare.com', '123 Main St, New York, NY', 'Peanuts');
SET @pat_id = LAST_INSERT_ID();

-- 4. Appointment
INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, reason) VALUES 
(@pat_id, @doc_id, DATE_ADD(NOW(), INTERVAL 2 HOUR), 'Scheduled', 'Regular Checkup');
