<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUser = (int)$_SESSION['user_id'];

$stmt = $conn->prepare('SELECT j.*, (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id) AS applications_count FROM jobs j WHERE j.posted_by = ? ORDER BY j.created_at DESC');
$stmt->bind_param('i', $currentUser);
$stmt->execute();
$res = $stmt->get_result();
?>
<h3>My Posted Jobs</h3>
<?php if ($res && $res->num_rows > 0): ?>
  <div class="list-group">
    <?php while ($job = $res->fetch_assoc()): ?>
      <a href="job_view.php?id=<?php echo (int)$job['id']; ?>" class="list-group-item list-group-item-action">
        <div class="d-flex w-100 justify-content-between">
          <h5 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
          <small><?php echo htmlspecialchars($job['created_at']); ?></small>
        </div>
        <p class="mb-1"><?php echo htmlspecialchars($job['location']); ?> • <?php echo htmlspecialchars($job['job_type']); ?></p>
        <small><?php echo (int)$job['applications_count']; ?> application(s)</small>
      </a>
    <?php endwhile; ?>
  </div>
<?php else: ?>
  <p class="text-muted">You haven't posted any jobs yet.</p>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
