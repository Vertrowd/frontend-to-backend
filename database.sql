CREATE DATABASE IF NOT EXISTS profilledb;
USE profilledb;

CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at DATETIME NOT NULL
);