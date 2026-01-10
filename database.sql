-- CLEAN MEDILINK DB WITH FOLLOW + MESSAGES
CREATE DATABASE IF NOT EXISTS medilink;
USE medilink;

DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS follows;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    role ENUM('doctor','nurse','pharmacist','hospital','seller','admin') NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    verification_token VARCHAR(100) DEFAULT NULL,
    is_verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profiles (
    user_id INT PRIMARY KEY,
    specialization VARCHAR(255),
    hospital VARCHAR(255),
    experience_years INT DEFAULT 0,
    bio TEXT,
    license_no VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_type ENUM('case','guideline','product') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    followed_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_follow (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Dummy users (you should register through UI for real passwords)
INSERT INTO users (name, email, role, password_hash, is_verified) VALUES
('Demo Doctor', 'demo.doctor@example.com', 'doctor', '$2y$10$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuv12', 1),
('Demo Nurse', 'demo.nurse@example.com', 'nurse', '$2y$10$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuv12', 1);

-- Dummy posts just for initial view (these accounts won't be used for login)
INSERT INTO posts (user_id, post_type, title, content) VALUES
(1, 'case', 'Sample heart failure case', 'Example case description for demo feed.'),
(2, 'product', 'Sample ECG Machine for sale', 'Demo ECG machine post. Replace with your own posts.');


-- Jobs and applications
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posted_by INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    job_type VARCHAR(100) NOT NULL,
    salary_range VARCHAR(255),
    description TEXT NOT NULL,
    requirements TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,
    cover_letter TEXT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ads table
CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_path VARCHAR(255),
    link_url VARCHAR(255),
    placement ENUM('feed','sidebar') NOT NULL DEFAULT 'feed',
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

-- Sample ads
INSERT INTO ads (title, image_path, link_url, placement, is_active) VALUES
('Sponsored: Portable ECG Machine – Launch Offer', NULL, 'https://example.com/portable-ecg', 'feed', 1),
('Sponsored: Nursing Courses for Critical Care', NULL, 'https://example.com/nursing-course', 'sidebar', 1);
