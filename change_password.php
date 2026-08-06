<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/auth.php';

require_login();

$title = "Change Password | " . APP_NAME;
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $p1 = (string)($_POST['password1'] ?? '');
    $p2 = (string)($_POST['password2'] ?? '');

    if (strlen($p1) < 10) {
        $msg = '<div class="alert alert-danger">Password must be at least 10 characters.</div>';
    } elseif ($p1 !== $p2) {
        $msg = '<div class="alert alert-danger">Passwords do not match.</div>';
    } else {
        $hash = password_hash($p1, PASSWORD_DEFAULT);
        $uid = (int)(current_user()['id'] ?? 0);

        $stmt = db()->prepare("UPDATE users SET password_hash=?, must_change_password=0 WHERE id=?");
        $stmt->execute([$hash, $uid]);

        // Update session snapshot
        $_SESSION['user']['must_change_password'] = 0;

        audit('change_password', 'user', $uid);

        if ((current_user()['role'] ?? '') === 'admin') {
            header('Location: ' . url('admin/index.php'));
        } else {
            header('Location: ' . url('employee/dashboard.php'));
        }
        exit;
    }
}

include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card p-4">
      <h4 class="mb-2">Change Password</h4>
      <p class="text-muted small mb-3">Update your password.</p>
      <?php echo $msg; ?>
      <form method="post">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= e(csrf_token()); ?>">
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input class="form-control" type="password" name="password1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input class="form-control" type="password" name="password2" required>
        </div>
        <button class="btn btn-primary w-100">Update</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
