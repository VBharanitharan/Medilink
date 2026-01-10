<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: messages.php');
    exit;
}

$sender = (int)$_SESSION['user_id'];
$receiver = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$body = trim($_POST['body'] ?? '');

if ($receiver <= 0 || $body === '') {
    header('Location: messages.php');
    exit;
}

$stmt = $conn->prepare('INSERT INTO messages (sender_id, receiver_id, body) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $sender, $receiver, $body);
$stmt->execute();
$stmt->close();

header('Location: messages.php?user_id=' . $receiver);
exit;
?>