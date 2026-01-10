<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUser = (int)$_SESSION['user_id'];

$sql = "SELECT ja.*, j.title, j.location, j.job_type
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.id
        WHERE ja.applicant_id = ?
        ORDER BY ja.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $currentUser);
$stmt->execute();
$res = $stmt->get_result();
?>
<h3>My Applications</h3>
<?php if ($res && $res->num_rows > 0): ?>
  <div class="list-group">
    <?php while ($row = $res->fetch_assoc()): ?>
      <div class="list-group-item">
        <div class="d-flex w-100 justify-content-between">
          <h5 class="mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
          <small><?php echo htmlspecialchars($row['created_at']); ?></small>
        </div>
        <p class="mb-1"><?php echo htmlspecialchars($row['location']); ?> • <?php echo htmlspecialchars($row['job_type']); ?></p>
        <p class="mb-1"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
        <p class="mb-1"><strong>Your message:</strong> <?php echo nl2br(htmlspecialchars($row['cover_letter'])); ?></p>
      </div>
    <?php endwhile; ?>
  </div>
<?php else: ?>
  <p class="text-muted">You have not applied to any jobs yet.</p>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
