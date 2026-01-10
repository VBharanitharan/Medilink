<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/app.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MediLink - Medical Professional Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">MediLink</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMedilink" aria-controls="navbarMedilink" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMedilink">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/feed.php">Feed</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/create_post.php">New Post</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="jobsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Jobs
              </a>
              <ul class="dropdown-menu" aria-labelledby="jobsDropdown">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/jobs.php">Browse Jobs</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/job_post.php">Post a Job</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/my_jobs.php">My Posted Jobs</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/my_applications.php">My Applications</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/messages.php">Messages</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/profile.php">Profile</a>
            </li>
            <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/admin.php">Admin</a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/register.php">Register</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
