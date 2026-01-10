<?php
require_once __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Create your MediLink account</h3>
    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <form method="post" action="register_process.php">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required maxlength="100">
      </div>
      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" required maxlength="200">
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="doctor">Doctor</option>
          <option value="nurse">Nurse</option>
          <option value="pharmacist">Pharmacist</option>
          <option value="hospital">Hospital / Recruiter</option>
          <option value="seller">Medical Product Seller</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Password (min 8 characters)</label>
        <input type="password" name="password" class="form-control" required minlength="8">
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required minlength="8">
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
