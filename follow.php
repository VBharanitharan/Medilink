<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUser = (int)$_SESSION['user_id'];
$targetId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$action = $_GET['action'] ?? '';

if ($targetId <= 0 || $targetId === $currentUser) {
    header('Location: feed.php');
    exit;
}

if ($action === 'follow') {
    $stmt = $conn->prepare('INSERT IGNORE INTO follows (follower_id, followed_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $currentUser, $targetId);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'unfollow') {
    $stmt = $conn->prepare('DELETE FROM follows WHERE follower_id = ? AND followed_id = ?');
    $stmt->bind_param('ii', $currentUser, $targetId);
    $stmt->execute();
    $stmt->close();
}

header('Location: feed.php');
exit;
?>