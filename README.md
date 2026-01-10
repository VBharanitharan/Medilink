📌 Project Overview
MediLink is a web-based medical networking platform designed to connect healthcare professionals such as doctors, nurses, pharmacists, hospitals, and sellers.
The platform enables users to share medical cases, follow other professionals, like posts, communicate via messages, and explore job opportunities.

This project is developed as a prototype for academic and demonstration purposes.

🎯 Objectives
Create a professional social network for healthcare users

Enable interaction through posts, likes, and follows

Provide email verification for user authenticity

Demonstrate AI-based summarization (prototype/illusion)

Design a scalable system that can later integrate real AI services

🛠️ Technologies Used
Frontend
HTML5

CSS3

JavaScript (Fetch API, AJAX)

Backend
PHP 8.x

MySQL (InnoDB, Foreign Keys)

Server
XAMPP (Apache + MySQL)

AI (Prototype)
Rule-based PHP summarizer (AI illusion for demo)

🗄️ Database Design
The database includes the following core tables:

users

profiles

posts

likes

comments

follows

messages

jobs

job_applications

ads

All relationships are enforced using foreign key constraints for data integrity.

✨ Key Features
👤 User Management
User registration and login

Email verification using token

Role-based users (doctor, nurse, pharmacist, etc.)

📝 Posts
Create medical cases, guidelines, or product posts

View posts in a feed format

👍 Like System
Like and unlike posts

Live like count

One like per user per post

🤝 Follow System
Follow and unfollow users

View followers and following counts

Toggle Follow / Following button

💬 Messaging
One-to-one messaging between users

💼 Jobs
Post healthcare-related job openings

Apply for jobs with cover letter

View application status

🤖 AI Summarization (Prototype)
Summarizes long posts into short points

Implemented using PHP logic (no external API)

Used only for demonstration purposes

⚠️ Note: AI functionality is simulated.
In production, this can be replaced with Gemini, OpenAI, or Ollama.

📧 Email Verification
Verification token generated during registration

User must verify email before full access

Improves trust and authenticity

🚀 Installation & Setup
1️⃣ Prerequisites
XAMPP (Apache + MySQL)

PHP 8.x

Web browser

2️⃣ Setup Steps
Copy project folder into:

makefile
Copy code
C:\xampp\htdocs\
Start Apache and MySQL from XAMPP Control Panel

Open phpMyAdmin

Import database.sql into MySQL

Update database credentials in config/db.php

Open browser and visit:

arduino
Copy code
http://localhost/medilink/
🔐 Default Test Users
(For demo only)

Doctor: demo.doctor@example.com

Nurse: demo.nurse@example.com

Real users should register via the UI.

📈 Future Enhancements
Real AI integration using LLM APIs

Real-time notifications

Advanced profile search

Mobile application

Role-based dashboards

Analytics and reporting

