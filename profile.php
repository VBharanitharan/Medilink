<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user basic info
$stmt = $conn->prepare('SELECT name, email, role FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

// Fetch profile info if exists
$stmt = $conn->prepare('SELECT specialization, hospital, experience_years, bio, license_no FROM profiles WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profileResult = $stmt->get_result();
$profile = $profileResult->fetch_assoc();
$stmt->close();

// Fetch follower & following counts
$followersCount = 0;
$followingCount = 0;

$cntStmt = $conn->prepare('SELECT COUNT(*) AS c FROM follows WHERE followed_id = ?');
$cntStmt->bind_param('i', $userId);
$cntStmt->execute();
$cntRes = $cntStmt->get_result();
if ($rowCnt = $cntRes->fetch_assoc()) {
    $followersCount = (int)$rowCnt['c'];
}
$cntStmt->close();

$cntStmt = $conn->prepare('SELECT COUNT(*) AS c FROM follows WHERE follower_id = ?');
$cntStmt->bind_param('i', $userId);
$cntStmt->execute();
$cntRes = $cntStmt->get_result();
if ($rowCnt = $cntRes->fetch_assoc()) {
    $followingCount = (int)$rowCnt['c'];
}
$cntStmt->close();

$stmt->close();

?>
<div class="row">
  <div class="col-md-7">
    <h3>Your Profile</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p>
      <strong>Followers:</strong> <?php echo $followersCount; ?>
      &nbsp;|&nbsp;
      <strong>Following:</strong> <?php echo $followingCount; ?>
    </p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <hr>
    <p><strong>Specialization / Department:</strong> <?php echo htmlspecialchars($profile['specialization'] ?? 'Not set'); ?></p>
    <p><strong>Hospital / Organization:</strong> <?php echo htmlspecialchars($profile['hospital'] ?? 'Not set'); ?></p>
    <p><strong>Experience (years):</strong> <?php echo htmlspecialchars($profile['experience_years'] ?? '0'); ?></p>
    <p><strong>License No:</strong> <?php echo htmlspecialchars($profile['license_no'] ?? 'Not set'); ?></p>
    <p><strong>Bio:</strong><br><?php echo nl2br(htmlspecialchars($profile['bio'] ?? 'No bio yet.')); ?></p>
  </div>
  <div class="col-md-5">
    <h4>Edit Profile</h4>
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="profile_save.php">
      <div class="mb-3">
        <label class="form-label">Specialization / Department</label>
        <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($profile['specialization'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Hospital / Organization</label>
        <input type="text" name="hospital" class="form-control" value="<?php echo htmlspecialchars($profile['hospital'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Experience (years)</label>
        <input type="number" name="experience_years" class="form-control" value="<?php echo htmlspecialchars($profile['experience_years'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">License No (for doctors)</label>
        <input type="text" name="license_no" class="form-control" value="<?php echo htmlspecialchars($profile['license_no'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Short Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
