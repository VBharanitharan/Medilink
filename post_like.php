<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = (int)$_SESSION['user_id'];

if ($postId <= 0) {
    header('Location: feed.php');
    exit;
}

// Check if already liked
$stmt = $conn->prepare('SELECT id FROM likes WHERE user_id = ? AND post_id = ?');
$stmt->bind_param('ii', $userId, $postId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $del = $conn->prepare('DELETE FROM likes WHERE user_id = ? AND post_id = ?');
    $del->bind_param('ii', $userId, $postId);
    $del->execute();
    $del->close();
} else {
    $stmt->close();
    $ins = $conn->prepare('INSERT INTO likes (user_id, post_id) VALUES (?, ?)');
    $ins->bind_param('ii', $userId, $postId);
    $ins->execute();
    $ins->close();
}

header('Location: feed.php');
exit;
?>