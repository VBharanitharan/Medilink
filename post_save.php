<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_post.php');
    exit;
}

$userId = $_SESSION['user_id'];
$post_type = $_POST['post_type'] ?? 'case';
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($title === '' || $content === '') {
    header('Location: create_post.php?error=' . urlencode('Title and content are required.'));
    exit;
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/posts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $safeName = 'post_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
    $target = $uploadDir . $safeName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Store relative path from project root
        $imagePath = 'uploads/posts/' . $safeName;
    }
}

$stmt = $conn->prepare('INSERT INTO posts (user_id, post_type, title, content, image_path) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('issss', $userId, $post_type, $title, $content, $imagePath);
$stmt->execute();
$stmt->close();

header('Location: create_post.php?success=' . urlencode('Post published successfully.'));
exit;
?>