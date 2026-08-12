-- Create database
CREATE DATABASE IF NOT EXISTS course_registration;
USE course_registration;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT DEFAULT 3,
    capacity INT DEFAULT 30,
    enrolled INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'dropped') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (user_id, course_id)
);

-- Insert sample data
INSERT INTO users (username, password, name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@university.com', 'admin'),
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'john@student.com', 'student'),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'jane@student.com', 'student');

INSERT INTO courses (code, name, description, credits, capacity) VALUES
('CS101', 'Introduction to Programming', 'Learn programming fundamentals with Python', 3, 30),
('CS201', 'Data Structures', 'Advanced data structures and algorithms', 4, 25),
('CS301', 'Database Systems', 'SQL and database design principles', 3, 20),
('MATH101', 'Calculus I', 'Differential and integral calculus', 4, 35),
('PHY101', 'Physics I', 'Mechanics and thermodynamics', 4, 30),
('ENG101', 'English Composition', 'Academic writing and research skills', 3, 25);

-- Note: Passwords are 'password123' (hashed)