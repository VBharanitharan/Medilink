<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

// Only hospitals, sellers, admin can post jobs (you can adjust this rule)
$role = $_SESSION['user_role'] ?? '';
$canPost = in_array($role, ['hospital', 'seller', 'admin', 'doctor']);
if (!$canPost) {
    echo '<div class="alert alert-warning">Only hospitals, recruiters, sellers or doctors can post jobs.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Post a Job</h3>
    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="job_save.php">
      <div class="mb-3">
        <label class="form-label">Job Title</label>
        <input type="text" name="title" class="form-control" required maxlength="200">
      </div>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" required maxlength="200" placeholder="Chennai, Tamil Nadu">
      </div>
      <div class="mb-3">
        <label class="form-label">Job Type</label>
        <select name="job_type" class="form-select" required>
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Contract">Contract</option>
          <option value="Internship">Internship</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Salary Range (optional)</label>
        <input type="text" name="salary_range" class="form-control" placeholder="₹50,000 – ₹80,000 / month">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="5" required placeholder="Role, department, shifts, responsibilities..."></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Requirements (optional)</label>
        <textarea name="requirements" class="form-control" rows="4" placeholder="Qualification, experience, licences..."></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Publish Job</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
