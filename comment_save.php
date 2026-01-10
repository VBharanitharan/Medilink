<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: feed.php');
    exit;
}

$postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$comment = trim($_POST['comment'] ?? '');
$userId = (int)$_SESSION['user_id'];

if ($postId <= 0 || $comment === '') {
    header('Location: feed.php');
    exit;
}

$stmt = $conn->prepare('INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $userId, $postId, $comment);
$stmt->execute();
$stmt->close();

header('Location: feed.php');
exit;
?>