-- JOBS MODULE SQL (ADD-ON FOR MEDILINK)
USE medilink;

CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    target_role VARCHAR(150) NOT NULL,
    location VARCHAR(150) NOT NULL,
    employment_type VARCHAR(50) NOT NULL,
    experience_required VARCHAR(100),
    salary_range VARCHAR(100),
    description TEXT NOT NULL,
    application_link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Applied',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Optional: one sample job (if hospital user with id=1 exists)
INSERT INTO jobs (hospital_id, title, target_role, location, employment_type, experience_required, salary_range, description, application_link)
SELECT 1, 'Sample Duty Doctor Opening', 'Duty Doctor', 'Chennai, Tamil Nadu', 'Full-time', '0–2 years MBBS', '₹60,000 – ₹80,000 / month',
       'Sample job for demo. Please create your own real job posts from the Jobs section.', NULL
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE title = 'Sample Duty Doctor Opening');
