CREATE DATABASE portfolio_generator;
USE portfolio_generator;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(255),
    contact_phone VARCHAR(50),
    contact_email VARCHAR(255),
    photo_path VARCHAR(255),
    short_bio TEXT,
    soft_skills TEXT,
    technical_skills TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE academic_backgrounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT,
    institute VARCHAR(255),
    degree VARCHAR(255),
    year VARCHAR(10),
    grade VARCHAR(50),
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id)
);

CREATE TABLE work_experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT,
    company_name VARCHAR(255),
    job_duration VARCHAR(50),
    job_responsibilities TEXT,
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id)
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT,
    project_title VARCHAR(255),
    project_description TEXT,
    FOREIGN KEY (portfolio_id) REFERENCES portfolios(id)
);