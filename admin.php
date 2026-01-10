<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-danger">Access denied. Admins only.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}

// Basic stats
$usersCount = $conn->query('SELECT COUNT(*) AS c FROM users')->fetch_assoc()['c'] ?? 0;
$postsCount = $conn->query('SELECT COUNT(*) AS c FROM posts')->fetch_assoc()['c'] ?? 0;
$jobsCount = $conn->query('SELECT COUNT(*) AS c FROM jobs')->fetch_assoc()['c'] ?? 0;
$adsCount  = $conn->query('SELECT COUNT(*) AS c FROM ads')->fetch_assoc()['c'] ?? 0;

$recentUsers = $conn->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5');
$recentJobs = $conn->query('SELECT id, title, location, created_at FROM jobs ORDER BY created_at DESC LIMIT 5');
$recentAds  = $conn->query('SELECT id, title, placement, is_active FROM ads ORDER BY id DESC LIMIT 5');
?>
<h3>Admin Dashboard</h3>
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h5 class="card-title">Users</h5>
        <p class="display-6"><?php echo (int)$usersCount; ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h5 class="card-title">Posts</h5>
        <p class="display-6"><?php echo (int)$postsCount; ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h5 class="card-title">Jobs</h5>
        <p class="display-6"><?php echo (int)$jobsCount; ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <h5 class="card-title">Ads</h5>
        <p class="display-6"><?php echo (int)$adsCount; ?></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <h5>Recent Users</h5>
    <ul class="list-group">
      <?php while ($u = $recentUsers->fetch_assoc()): ?>
        <li class="list-group-item">
          <?php echo htmlspecialchars($u['name']); ?>
          <br><small><?php echo htmlspecialchars($u['email']); ?> • <?php echo htmlspecialchars($u['role']); ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
  <div class="col-md-4">
    <h5>Recent Jobs</h5>
    <ul class="list-group">
      <?php while ($j = $recentJobs->fetch_assoc()): ?>
        <li class="list-group-item">
          <?php echo htmlspecialchars($j['title']); ?>
          <br><small><?php echo htmlspecialchars($j['location']); ?> • <?php echo htmlspecialchars($j['created_at']); ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
  <div class="col-md-4">
    <h5>Recent Ads</h5>
    <a href="ads_manage.php" class="btn btn-sm btn-primary mb-2">Manage Ads</a>
    <ul class="list-group">
      <?php while ($a = $recentAds->fetch_assoc()): ?>
        <li class="list-group-item">
          <?php echo htmlspecialchars($a['title']); ?>
          <br><small><?php echo htmlspecialchars($a['placement']); ?> • <?php echo $a['is_active'] ? 'Active' : 'Inactive'; ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
