<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

// Fetch active jobs with poster info
$sql = "SELECT j.*, u.name AS poster_name, u.role AS poster_role
        FROM jobs j
        JOIN users u ON j.posted_by = u.id
        WHERE j.is_active = 1
        ORDER BY j.created_at DESC";
$result = $conn->query($sql);
?>
<div class="row">
  <div class="col-md-9">
    <h3>Browse Jobs</h3>
    <p class="text-muted">Opportunities posted by hospitals, clinics, and medical product companies.</p>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($job = $result->fetch_assoc()): ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">
              <a href="job_view.php?id=<?php echo (int)$job['id']; ?>">
                <?php echo htmlspecialchars($job['title']); ?>
              </a>
            </h5>
            <h6 class="card-subtitle mb-2 text-muted">
              <?php echo htmlspecialchars($job['location']); ?> •
              <?php echo htmlspecialchars($job['job_type']); ?> •
              Posted by <?php echo htmlspecialchars($job['poster_name']); ?> (<?php echo htmlspecialchars($job['poster_role']); ?>)
            </h6>
            <p class="card-text">
              <?php echo nl2br(htmlspecialchars(mb_strimwidth($job['description'], 0, 220, '...'))); ?>
            </p>
            <?php if (!empty($job['salary_range'])): ?>
              <p class="mb-1"><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
            <?php endif; ?>
            <a href="job_view.php?id=<?php echo (int)$job['id']; ?>" class="btn btn-sm btn-primary">View &amp; Apply</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No active jobs yet. Check back later or ask hospitals to post roles here.</p>
    <?php endif; ?>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-header">Tips</div>
      <div class="card-body">
        <ul>
          <li>Use clear job titles: “Junior Resident – Internal Medicine”.</li>
          <li>Include shift details and location.</li>
          <li>Keep your profile complete to stand out.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
