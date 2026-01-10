<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$userId = $_SESSION['user_id'];

$specialization = trim($_POST['specialization'] ?? '');
$hospital = trim($_POST['hospital'] ?? '');
$experience = (int)($_POST['experience_years'] ?? 0);
$license_no = trim($_POST['license_no'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Check if profile exists
$stmt = $conn->prepare('SELECT user_id FROM profiles WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $update = $conn->prepare('UPDATE profiles SET specialization = ?, hospital = ?, experience_years = ?, bio = ?, license_no = ? WHERE user_id = ?');
    $update->bind_param('ssissi', $specialization, $hospital, $experience, $bio, $license_no, $userId);
    $update->execute();
    $update->close();
} else {
    $stmt->close();
    $insert = $conn->prepare('INSERT INTO profiles (user_id, specialization, hospital, experience_years, bio, license_no) VALUES (?, ?, ?, ?, ?, ?)');
    $insert->bind_param('ississ', $userId, $specialization, $hospital, $experience, $bio, $license_no);
    $insert->execute();
    $insert->close();
}

header('Location: profile.php?success=' . urlencode('Profile updated.'));
exit;
?>