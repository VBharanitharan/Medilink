<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: login.php?error=' . urlencode('Invalid email.'));
    exit;
}

$stmt = $conn->prepare('SELECT id, name, password_hash, is_verified, role FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!password_verify($password, $row['password_hash'])) {
        header('Location: login.php?error=' . urlencode('Incorrect password.'));
        exit;
    }
    if ((int)$row['is_verified'] !== 1) {
        header('Location: login.php?error=' . urlencode('Please verify your email before logging in.'));
        exit;
    }

    $_SESSION['user_id'] = $row['id'];
    $_SESSION['user_name'] = $row['name'];
    $_SESSION['user_role'] = $row['role'];

    header('Location: ' . BASE_URL . '/feed.php');
    exit;
} else {
    header('Location: login.php?error=' . urlencode('No account found with that email.'));
    exit;
}
?>