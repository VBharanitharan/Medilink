<?php
require_once __DIR__ . '/partials/header.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/feed.php');
    exit;
}
?>
<div class="row">
  <div class="col-md-10 mx-auto">
    <h2>Welcome to MediLink</h2>
    <p class="lead">A professional networking platform for doctors, nurses, pharmacists, hospitals, and medical product sellers.</p>
    <p>In this demo you can:</p>
    <ul>
      <li>Register as a medical professional, hospital, or seller</li>
      <li>Create posts about cases, guidelines, or medical products for sale</li>
      <li>View the community feed, like &amp; comment on posts</li>
      <li>Follow other users and send them direct messages</li>
    </ul>
    <a class="btn btn-primary" href="register.php">Get started</a>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
