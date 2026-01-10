<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: job_post.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$location = trim($_POST['location'] ?? '');
$job_type = trim($_POST['job_type'] ?? '');
$salary_range = trim($_POST['salary_range'] ?? '');
$description = trim($_POST['description'] ?? '');
$requirements = trim($_POST['requirements'] ?? '');
$posted_by = (int)$_SESSION['user_id'];

if ($title === '' || $location === '' || $job_type === '' || $description === '') {
    header('Location: job_post.php?error=' . urlencode('Please fill in all required fields.'));
    exit;
}

$stmt = $conn->prepare('INSERT INTO jobs (posted_by, title, location, job_type, salary_range, description, requirements, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)');
$stmt->bind_param('issssss', $posted_by, $title, $location, $job_type, $salary_range, $description, $requirements);
$stmt->execute();
$stmt->close();

header('Location: job_post.php?success=' . urlencode('Job posted successfully.'));
exit;
?>