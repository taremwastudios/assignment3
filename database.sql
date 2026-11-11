-- ============================================
-- Simple To-Do Manager - Database Setup
-- Run this once on your MariaDB / MySQL server
-- ============================================

CREATE DATABASE IF NOT EXISTS todo_app
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE todo_app;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data (optional)
INSERT INTO tasks (title, is_completed) VALUES
('Buy groceries', 0),
('Finish assignment proposal', 0),
('Call the lecturer', 1);
