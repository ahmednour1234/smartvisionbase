<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/auth.php';

if (current_user()) {
    $u = current_user();
    if (($u['role'] ?? '') === 'admin') {
        header('Location: ' . url('admin/index.php'));
    } else {
        header('Location: ' . url('employee/dashboard.php'));
    }
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = trim((string)($_POST['email'] ?? ''));
    $pass  = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare("SELECT * FROM users WHERE email=:email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch();

    if ($u && (int)$u['is_active'] === 1 && password_verify($pass, (string)$u['password_hash'])) {
        login_user($u);
        audit('login', 'user', (int)$u['id']);
        if (($u['role'] ?? '') === 'admin') {
            header('Location: ' . url('admin/index.php'));
        } else {
            header('Location: ' . url('employee/dashboard.php'));
        }
        exit;
    }

    audit('login_failed', 'user', null, ['email' => $email]);
    $msg = 'Invalid credentials.';
}
$title = APP_NAME . ' | Login';
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card p-4">
      <h4 class="mb-3">Sign in</h4>
      <?php if ($msg): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
      <div class="text-muted small mt-3">
        Admin creates users from Admin Panel.
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
