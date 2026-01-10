<?php
require_once __DIR__ . '/partials/header.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php?error=' . urlencode('Please log in first.'));
    exit;
}
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Create a new post</h3>
    <p class="text-muted">
      You can share case studies, guidelines, or <strong>posts for selling medical products</strong>.
      Example: “New portable ECG device available – special price for clinics.”
    </p>
    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="post_save.php" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Post Type</label>
        <select name="post_type" class="form-select" required>
          <option value="case">Case / Clinical Update</option>
          <option value="guideline">Guideline / Education</option>
          <option value="product">Medical Product / Equipment for Sale</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required maxlength="200">
      </div>
      <div class="mb-3">
        <label class="form-label">Details</label>
        <textarea name="content" class="form-control" rows="5" required id="post_content"></textarea>
        <button type="button" class="btn btn-sm btn-outline-info mt-2" id="ai_rewrite_btn">
          AI: Rewrite this more clearly
        </button>
        <div class="form-text">
          Example product post:<br>
          “New portable ECG machine – 12 lead, 3-year warranty, ideal for clinics. Introductory price: ₹45,000.”
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Image (optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        <div class="form-text">You can upload a product photo, report screenshot, or graph.</div>
      </div>
      <button type="submit" class="btn btn-primary">Publish Post</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('ai_rewrite_btn');
  const textarea = document.getElementById('post_content');
  if (!btn || !textarea) return;

  btn.addEventListener('click', async () => {
    const original = textarea.value.trim();
    if (!original) {
      alert('Please type something first, then AI can rewrite it.');
      return;
    }
    btn.disabled = true;
    const oldText = btn.textContent;
    btn.textContent = 'AI is rewriting...';

    try {
      const res = await fetch('ai_helper.php?action=rewrite_post', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content: original })
      });
      const data = await res.json();
      if (data.error) {
        alert('AI error: ' + data.error);
      } else if (data.summary) {
        textarea.value = data.summary;
      }
    } catch (e) {
      alert('Network error while contacting AI.');
    } finally {
      btn.disabled = false;
      btn.textContent = oldText;
    }
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
