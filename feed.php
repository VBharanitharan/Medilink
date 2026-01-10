<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];

// Fetch posts with user info
$sql = 'SELECT p.id, p.user_id, p.post_type, p.title, p.content, p.image_path, p.created_at,
               u.name, u.role
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC';
$result = $conn->query($sql);
?>
<div class="row">
  <div class="col-md-9">
    <h3>Community Feed</h3>
    <p class="text-muted">
      See case updates, guidelines, and <strong>medical product sale posts</strong> from the MediLink community.
    </p>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">
            <?php echo htmlspecialchars($row['title']); ?>
            <span class="badge bg-secondary" style="font-size:0.75rem;">
              <?php echo htmlspecialchars(ucfirst($row['post_type'])); ?>
            </span>
          </h5>
          <h6 class="card-subtitle mb-2 text-muted">
            Posted by <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['role']); ?>)
            on <?php echo htmlspecialchars($row['created_at']); ?>
          </h6>
          <?php
          $postUserId = (int)$row['user_id'];

          // Fetch follower count for this post's author
          $followersCountUser = 0;
          $fcRes = $conn->query('SELECT COUNT(*) AS c FROM follows WHERE followed_id = ' . $postUserId);
          if ($fcRes && $fcRow = $fcRes->fetch_assoc()) {
              $followersCountUser = (int)$fcRow['c'];
          }

          if ($currentUserId && $currentUserId !== $postUserId) {
              // Follow/unfollow button with clear indication
              $fCheck = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND followed_id = ?");
              $fCheck->bind_param("ii", $currentUserId, $postUserId);
              $fCheck->execute();
              $fCheck->store_result();
              $isFollowing = $fCheck->num_rows > 0;
              $fCheck->close();

              if ($isFollowing) {
                  echo '<span class="badge bg-success me-2">Following</span>';
                  echo '<a href="follow.php?action=unfollow&user_id=' . $postUserId . '" class="btn btn-sm btn-outline-secondary me-2">Unfollow</a>';
              } else {
                  echo '<a href="follow.php?action=follow&user_id=' . $postUserId . '" class="btn btn-sm btn-outline-primary me-2">Follow</a>';
              }

              echo '<a href="messages.php?user_id=' . $postUserId . '" class="btn btn-sm btn-outline-success me-2">Message</a>';
          }

          // Show this user's follower count
          echo '<p class="mt-2 mb-0"><small class="text-muted">' . $followersCountUser . ' follower' . ($followersCountUser == 1 ? '' : 's') . '</small></p>';
          ?>
          <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
          <button
            class="btn btn-sm btn-outline-info mt-2 ai-summarize-btn"
            data-post-id="<?php echo (int)$row['id']; ?>"
            data-post-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
            AI: Summarize Case
          </button>
          <div class="mt-2 small text-muted ai-summary" id="summary_<?php echo (int)$row['id']; ?>"></div>
          <?php if (!empty($row['image_path'])): ?>
            <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" class="img-fluid rounded mb-2" alt="Post image">
          <?php endif; ?>
          <a href="post_like.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Like</a>
          <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#comments_<?php echo (int)$row['id']; ?>">
            Comments
          </button>
        </div>
        <div class="card-footer">
          <?php
          $pid = (int)$row['id'];
          // Count likes
          $likesRes = $conn->query('SELECT COUNT(*) AS c FROM likes WHERE post_id = ' . $pid);
          $likesCount = $likesRes ? (int)$likesRes->fetch_assoc()['c'] : 0;
          echo '<small class="text-muted me-3">' . $likesCount . ' likes</small>';

          // Fetch comments
          $commentsRes = $conn->query('SELECT c.comment, c.created_at, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ' . $pid . ' ORDER BY c.created_at ASC');
          ?>
          <div class="collapse mt-2" id="comments_<?php echo (int)$row['id']; ?>">
            <div class="mb-2">
              <?php if ($commentsRes && $commentsRes->num_rows > 0): ?>
                <?php while ($c = $commentsRes->fetch_assoc()): ?>
                  <div class="mb-1">
                    <strong><?php echo htmlspecialchars($c['name']); ?>:</strong>
                    <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                    <br><small class="text-muted"><?php echo htmlspecialchars($c['created_at']); ?></small>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p class="text-muted mb-1">No comments yet.</p>
              <?php endif; ?>
            </div>
            <form method="post" action="comment_save.php" class="d-flex">
              <input type="hidden" name="post_id" value="<?php echo (int)$row['id']; ?>">
              <input type="text" name="comment" class="form-control form-control-sm me-2" placeholder="Write a comment..." required>
              <button class="btn btn-sm btn-primary" type="submit">Post</button>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="col-md-3">
    <div class="card mb-3">
      <div class="card-header">
        Sponsored
      </div>
      <div class="card-body">
        <?php
        $adsRes = $conn->query("SELECT * FROM ads WHERE is_active = 1 AND placement = 'feed' ORDER BY id DESC LIMIT 3");
        if ($adsRes && $adsRes->num_rows > 0):
          while ($ad = $adsRes->fetch_assoc()):
        ?>
            <div class="mb-3">
              <?php if (!empty($ad['link_url'])): ?>
                <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank">
                  <?php echo htmlspecialchars($ad['title']); ?>
                </a>
              <?php else: ?>
                  <?php echo htmlspecialchars($ad['title']); ?>
              <?php endif; ?>
            </div>
        <?php
          endwhile;
        else:
          echo '<p class="text-muted mb-0">No active ads.</p>';
        endif;
        ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        Example use cases
      </div>
      <div class="card-body">
        <ul>
          <li><strong>Doctor</strong> shares a heart failure case.</li>
          <li><strong>Nurse</strong> shares new triage guidelines.</li>
          <li><strong>Seller</strong> posts “Portable ECG for sale – launch offer”.</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.ai-summarize-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const content = btn.getAttribute('data-post-content');
      const postId  = btn.getAttribute('data-post-id');
      const summaryDiv = document.getElementById('summary_' + postId);

      summaryDiv.textContent = 'AI is summarizing...';

      try {
        const res = await fetch('ai_helper.php?action=summarize_case', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ content })
        });
        const data = await res.json();
        if (data.error) {
          summaryDiv.textContent = 'AI error: ' + data.error;
        } else {
          summaryDiv.textContent = data.summary;
        }
      } catch (e) {
        summaryDiv.textContent = 'Network error while contacting AI.';
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
