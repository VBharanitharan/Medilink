<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($token)) {
    header('Location: login.php?error=' . urlencode('Invalid verification link.'));
    exit;
}

$stmt = $conn->prepare('SELECT id, is_verified FROM users WHERE email = ? AND verification_token = ? LIMIT 1');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ((int)$row['is_verified'] === 1) {
        header('Location: login.php?success=' . urlencode('Your email is already verified. Please log in.'));
        exit;
    }

    $update = $conn->prepare('UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?');
    $update->bind_param('i', $row['id']);
    $update->execute();
    $update->close();

    header('Location: login.php?success=' . urlencode('Email verified successfully. You can now log in.'));
    exit;
} else {
    header('Location: login.php?error=' . urlencode('Invalid or expired verification link.'));
    exit;
}
?>