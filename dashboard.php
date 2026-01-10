<?php
require_once __DIR__ . '/partials/header.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}
?>
<div class="row">
  <div class="col-md-8">
    <h3>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h3>
    <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>
    <p>Use the navigation bar to go to your Feed, create a new post, chat via Messages, or edit your Profile.</p>
  </div>
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-header">
        Email Verification Info
      </div>
      <div class="card-body">
        <p>If email sending did not work on localhost, copy your verification link from here (if available):</p>
        <?php if (!empty($_SESSION['last_verification_link'])): ?>
          <code style="font-size: 0.8rem;"><?php echo htmlspecialchars($_SESSION['last_verification_link']); ?></code>
        <?php else: ?>
          <p><em>No recent verification link stored.</em></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
