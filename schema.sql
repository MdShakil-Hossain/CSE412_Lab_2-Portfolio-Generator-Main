CREATE DATABASE IF NOT EXISTS portfolio_generator;
USE portfolio_generator;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    job_title VARCHAR(255),              -- Added for job title (e.g., "UI & UX Designer")
    contact_phone VARCHAR(50),
    contact_email VARCHAR(255),
    address VARCHAR(255),                -- Added for address
    photo_path VARCHAR(255),
    short_bio TEXT,
    soft_skills TEXT,
    technical_skills TEXT,
    experience TEXT,                     -- Added for experience
    languages TEXT,                      -- Added for languages
    resume_summary TEXT,                 -- Added for resume summary
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS academic_backgrounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    institute VARCHAR(255) NOT NULL,
    degree VARCHAR(255) NOT NULL,
    year VARCHAR(10),
    grade VARCHAR(50),
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS work_experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    job_duration VARCHAR(50),
    job_responsibilities TEXT,
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    project_title VARCHAR(255) NOT NULL,
    project_description TEXT,
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE
);