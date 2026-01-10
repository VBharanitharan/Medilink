<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUser = (int)$_SESSION['user_id'];
$jobId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jobId <= 0) {
    header('Location: jobs.php');
    exit;
}

$stmt = $conn->prepare('SELECT j.*, u.name AS poster_name, u.role AS poster_role FROM jobs j JOIN users u ON j.posted_by = u.id WHERE j.id = ?');
$stmt->bind_param('i', $jobId);
$stmt->execute();
$res = $stmt->get_result();
$job = $res->fetch_assoc();
$stmt->close();

if (!$job) {
    echo '<div class="alert alert-warning">Job not found.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}

// Build simple profile context for fake AI job matching
$userRole = $_SESSION['user_role'] ?? '';
$profileSpecialization = '';
$profileExperience = 0;
$profStmt = $conn->prepare('SELECT specialization, experience_years FROM profiles WHERE user_id = ?');
$profStmt->bind_param('i', $currentUser);
$profStmt->execute();
$profRes = $profStmt->get_result();
if ($prof = $profRes->fetch_assoc()) {
    $profileSpecialization = $prof['specialization'] ?? '';
    $profileExperience = (int)($prof['experience_years'] ?? 0);
}
$profStmt->close();


if (!$job) {
    echo '<div class="alert alert-warning">Job not found.</div>';
    require_once __DIR__ . '/partials/footer.php';
    exit;
}

// Check if current user already applied
$appStmt = $conn->prepare('SELECT id, status FROM job_applications WHERE job_id = ? AND applicant_id = ?');
$appStmt->bind_param('ii', $jobId, $currentUser);
$appStmt->execute();
$appRes = $appStmt->get_result();
$existingApp = $appRes->fetch_assoc();
$appStmt->close();

// If current user is job owner, fetch applications list
$applications = [];
if ($currentUser === (int)$job['posted_by']) {
    $appsQ = $conn->prepare('SELECT ja.*, u.name AS applicant_name, u.email AS applicant_email FROM job_applications ja JOIN users u ON ja.applicant_id = u.id WHERE ja.job_id = ? ORDER BY ja.created_at DESC');
    $appsQ->bind_param('i', $jobId);
    $appsQ->execute();
    $appsRes = $appsQ->get_result();
    while ($row = $appsRes->fetch_assoc()) {
        $applications[] = $row;
    }
    $appsQ->close();
}
?>
<div class="row">
  <div class="col-md-8">
    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
    <p class="text-muted">
      <?php echo htmlspecialchars($job['location']); ?> •
      <?php echo htmlspecialchars($job['job_type']); ?> •
      Posted by <?php echo htmlspecialchars($job['poster_name']); ?> (<?php echo htmlspecialchars($job['poster_role']); ?>)
    </p>
    <?php if (!empty($job['salary_range'])): ?>
      <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
    <?php endif; ?>
    <h5>Description</h5>
    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
    <?php if (!empty($job['requirements'])): ?>
      <h5>Requirements</h5>
      <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
    <?php endif; ?>
  </div>
  <div class="col-md-4">
    <?php if ($currentUser !== (int)$job['posted_by']): ?>
      <div class="card mb-3">
        <div class="card-header">Apply for this job</div>
        <div class="card-body">
          <?php if ($existingApp): ?>
            <p class="text-success">You have already applied.</p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($existingApp['status']); ?></p>
          <?php else: ?>
            <form method="post" action="job_apply.php">
              <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">
              <div class="mb-3">
                <label class="form-label">Short message / cover letter</label>
                <textarea name="cover_letter" class="form-control" rows="4" required placeholder="Introduce yourself, experience &amp; interest."></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-sm">Submit Application</button>
            </form>
          <?php endif; ?>

          <hr>
          <h6>AI Job Match (for you)</h6>
          <p class="small text-muted" id="job_ai_<?php echo $jobId; ?>">
            Click the button below to see how well this job matches your profile (role: <?php echo htmlspecialchars($userRole); ?>).
          </p>
          <button type="button"
                  class="btn btn-sm btn-outline-info"
                  id="job_ai_btn_<?php echo $jobId; ?>"
                  data-job-id="<?php echo $jobId; ?>"
                  data-job-text="<?php echo htmlspecialchars($job['description'] . "\nRequirements: " . ($job['requirements'] ?? ''), ENT_QUOTES); ?>">
            AI: Check job fit for me
          </button>

          <a href="messages.php?user_id=<?php echo (int)$job['posted_by']; ?>" class="btn btn-sm btn-outline-success mt-2">
            Message recruiter
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="card mb-3">
        <div class="card-header">Applications</div>
        <div class="card-body">
          <?php if ($applications): ?>
            <?php foreach ($applications as $app): ?>
              <div class="mb-2 border-bottom pb-2">
                <strong><?php echo htmlspecialchars($app['applicant_name']); ?></strong>
                <br><small><?php echo htmlspecialchars($app['applicant_email']); ?></small>
                <p class="mb-1"><?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?></p>
                <small class="text-muted">Applied on <?php echo htmlspecialchars($app['created_at']); ?> • Status: <?php echo htmlspecialchars($app['status']); ?></small>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">No applications yet.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('job_ai_btn_<?php echo $jobId; ?>');
  const infoP = document.getElementById('job_ai_<?php echo $jobId; ?>');
  if (!btn || !infoP) return;

  btn.addEventListener('click', async () => {
    const jobText = btn.getAttribute('data-job-text');
    infoP.textContent = 'AI is analysing this job against your profile...';

    try {
      const res = await fetch('ai_helper.php?action=job_match', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          job_text: jobText,
          user_role: '<?php echo addslashes($userRole); ?>',
          specialization: '<?php echo addslashes($profileSpecialization); ?>',
          experience_years: '<?php echo (int)$profileExperience; ?>'
        })
      });
      const data = await res.json();
      if (data.error) {
        infoP.textContent = 'AI error: ' + data.error;
      } else {
        infoP.textContent = data.summary;
      }
    } catch (e) {
      infoP.textContent = 'Network error while contacting AI.';
    }
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
