-- CREATE DATABASE booking_system_db2;
USE booking_system_db2;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    email VARCHAR(30) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    role ENUM('client' , 'admin') DEFAULT 'client' NOT NULL,
    password VARCHAR(100) NOT NULL
);
CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    date DATE NOT NULL,
    type ENUM('appointment' , 'consultation'),
    status ENUM('pending' , 'approved' , 'rejected') DEFAULT 'pending' NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    date DATE NOT NULL UNIQUE
);