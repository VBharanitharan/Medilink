<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-danger">Access denied. Admins only.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}

// Handle toggle active
if (isset($_GET['toggle']) && ctype_digit($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query('UPDATE ads SET is_active = 1 - is_active WHERE id = ' . $id);
    header('Location: ads_manage.php');
    exit;
}

// Handle create new ad
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $link_url = trim($_POST['link_url'] ?? '');
    $placement = trim($_POST['placement'] ?? 'feed');
    if ($title !== '') {
        $stmt = $conn->prepare('INSERT INTO ads (title, link_url, placement, is_active) VALUES (?, ?, ?, 1)');
        $stmt->bind_param('sss', $title, $link_url, $placement);
        $stmt->execute();
        $stmt->close();
    }
}

$ads = $conn->query('SELECT * FROM ads ORDER BY id DESC');
?>
<h3>Manage Ads</h3>
<div class="row">
  <div class="col-md-5">
    <h5>Create New Ad</h5>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Ad Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Target URL (optional)</label>
        <input type="url" name="link_url" class="form-control" placeholder="https://...">
      </div>
      <div class="mb-3">
        <label class="form-label">Placement</label>
        <select name="placement" class="form-select">
          <option value="feed">Feed</option>
          <option value="sidebar">Sidebar</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Create Ad</button>
    </form>
  </div>
  <div class="col-md-7">
    <h5>Existing Ads</h5>
    <table class="table table-sm">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Placement</th>
          <th>Status</th>
          <th>Toggle</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($ad = $ads->fetch_assoc()): ?>
          <tr>
            <td><?php echo (int)$ad['id']; ?></td>
            <td><?php echo htmlspecialchars($ad['title']); ?></td>
            <td><?php echo htmlspecialchars($ad['placement']); ?></td>
            <td><?php echo $ad['is_active'] ? 'Active' : 'Inactive'; ?></td>
            <td><a href="ads_manage.php?toggle=<?php echo (int)$ad['id']; ?>" class="btn btn-sm btn-outline-secondary">Toggle</a></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
