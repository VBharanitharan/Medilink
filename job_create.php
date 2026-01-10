<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$currentUserRole = $_SESSION['user_role'] ?? '';

if ($currentUserRole !== 'hospital') {
    echo '<div class="alert alert-danger">Only Hospital / Recruiter accounts can create job posts.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Create a Job Post</h3>
    <p class="text-muted">Share doctor, nurse, pharmacist, or technician openings with the MediLink community.</p>
    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="job_save.php">
      <div class="mb-3">
        <label class="form-label">Job Title</label>
        <input type="text" name="title" class="form-control" required maxlength="200" placeholder="Example: Senior Cardiologist">
      </div>
      <div class="mb-3">
        <label class="form-label">Target Role</label>
        <input type="text" name="target_role" class="form-control" required maxlength="100" placeholder="Example: Cardiologist / Duty Doctor / Staff Nurse">
      </div>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" required maxlength="150" placeholder="City, State (e.g., Chennai, Tamil Nadu)">
      </div>
      <div class="mb-3">
        <label class="form-label">Employment Type</label>
        <select name="employment_type" class="form-select" required>
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Contract">Contract</option>
          <option value="On-call / Locum">On-call / Locum</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Experience Required</label>
        <input type="text" name="experience_required" class="form-control" maxlength="100" placeholder="Example: 3+ years in cardiology">
      </div>
      <div class="mb-3">
        <label class="form-label">Salary Range</label>
        <input type="text" name="salary_range" class="form-control" maxlength="100" placeholder="Example: ₹1,20,000 – ₹1,80,000 / month">
      </div>
      <div class="mb-3">
        <label class="form-label">Job Description</label>
        <textarea name="description" class="form-control" rows="5" required placeholder="Describe responsibilities, required skills, and shift details."></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">External Application Link (optional)</label>
        <input type="url" name="application_link" class="form-control" placeholder="If you have careers page or Google Form, paste link here">
      </div>
      <button type="submit" class="btn btn-primary">Publish Job</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
