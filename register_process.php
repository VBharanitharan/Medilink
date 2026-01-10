<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? 'doctor');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    header('Location: register.php?error=' . urlencode('Passwords do not match.'));
    exit;
}

if (strlen($password) < 8) {
    header('Location: register.php?error=' . urlencode('Password must be at least 8 characters long.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=' . urlencode('Invalid email address.'));
    exit;
}

// Check if email already exists
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    header('Location: register.php?error=' . urlencode('Email is already registered.'));
    exit;
}
$stmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$stmt = $conn->prepare('INSERT INTO users (name, email, role, password_hash, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, 0)');
$stmt->bind_param('sssss', $name, $email, $role, $hashedPassword, $token);

if ($stmt->execute()) {
    $stmt->close();
    $mailSent = sendVerificationEmail($email, $name, $token);
    $_SESSION['last_verification_link'] = BASE_URL . '/verify.php?email=' . urlencode($email) . '&token=' . urlencode($token);

    $msg = 'Registration successful. ';
    if ($mailSent) {
        $msg .= 'Please check your email for a verification link.';
    } else {
        $msg .= 'Email sending may not work on localhost. Use the verification link stored in your dashboard.';
    }

    header('Location: register.php?success=' . urlencode($msg));
    exit;
} else {
    $stmt->close();
    header('Location: register.php?error=' . urlencode('Registration failed. Please try again.'));
    exit;
}
?>