<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}

$currentUser = (int)$_SESSION['user_id'];
$chatWith = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Get list of users you have conversations with
$usersRes = $conn->query("
    SELECT DISTINCT
        CASE
            WHEN sender_id = $currentUser THEN receiver_id
            ELSE sender_id
        END AS other_id
    FROM messages
    WHERE sender_id = $currentUser OR receiver_id = $currentUser
");

$knownUsers = [];
while ($row = $usersRes->fetch_assoc()) {
    $knownUsers[] = (int)$row['other_id'];
}
if ($chatWith && !in_array($chatWith, $knownUsers)) {
    $knownUsers[] = $chatWith;
}

$usersMap = [];
if ($knownUsers) {
    $ids = implode(',', $knownUsers);
    $uRes = $conn->query("SELECT id, name FROM users WHERE id IN ($ids)");
    while ($u = $uRes->fetch_assoc()) {
        $usersMap[$u['id']] = $u['name'];
    }
}

// Fetch messages with selected user
$messages = [];
if ($chatWith && isset($usersMap[$chatWith])) {
    $stmt = $conn->prepare("
        SELECT m.*, u.name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $stmt->bind_param('iiii', $currentUser, $chatWith, $chatWith, $currentUser);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($m = $res->fetch_assoc()) {
        $messages[] = $m;
    }
    $stmt->close();
}
?>
<div class="row">
  <div class="col-md-4">
    <h5>Messages</h5>
    <ul class="list-group">
      <?php if ($usersMap): ?>
        <?php foreach ($usersMap as $uid => $uname): ?>
          <li class="list-group-item <?php echo ($uid == $chatWith) ? 'active' : ''; ?>">
            <a href="messages.php?user_id=<?php echo $uid; ?>" class="<?php echo ($uid == $chatWith) ? 'text-white' : ''; ?>">
              <?php echo htmlspecialchars($uname); ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="list-group-item text-muted">No conversations yet.</li>
      <?php endif; ?>
    </ul>
  </div>
  <div class="col-md-8">
    <?php if ($chatWith && isset($usersMap[$chatWith])): ?>
      <h5>Chat with <?php echo htmlspecialchars($usersMap[$chatWith]); ?></h5>
      <div class="border rounded p-2 mb-3 bg-white" style="height:300px; overflow-y:auto;">
        <?php if ($messages): ?>
          <?php foreach ($messages as $msg): ?>
            <div class="mb-2">
              <strong><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong>
              <?php echo nl2br(htmlspecialchars($msg['body'])); ?>
              <br><small class="text-muted"><?php echo htmlspecialchars($msg['created_at']); ?></small>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No messages yet. Start the conversation.</p>
        <?php endif; ?>
      </div>
      <form method="post" action="send_message.php">
        <input type="hidden" name="receiver_id" value="<?php echo $chatWith; ?>">
        <div class="mb-2">
          <textarea name="body" class="form-control" rows="2" required placeholder="Type your message..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Send</button>
      </form>
    <?php else: ?>
      <p class="text-muted">Select a user from the left to start chatting.</p>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
