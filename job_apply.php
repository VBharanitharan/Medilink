<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: jobs.php');
    exit;
}

$jobId = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
$cover = trim($_POST['cover_letter'] ?? '');
$applicant = (int)$_SESSION['user_id'];

if ($jobId <= 0 || $cover === '') {
    header('Location: jobs.php');
    exit;
}

// Prevent duplicate applications
$check = $conn->prepare('SELECT id FROM job_applications WHERE job_id = ? AND applicant_id = ?');
$check->bind_param('ii', $jobId, $applicant);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    header('Location: job_view.php?id=' . $jobId);
    exit;
}
$check->close();

$stmt = $conn->prepare('INSERT INTO job_applications (job_id, applicant_id, cover_letter, status) VALUES (?, ?, ?, "Pending")');
$stmt->bind_param('iis', $jobId, $applicant, $cover);
$stmt->execute();
$stmt->close();

header('Location: job_view.php?id=' . $jobId);
exit;
?>