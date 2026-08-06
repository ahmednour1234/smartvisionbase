<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/auth.php';

$title = APP_NAME . ' | Access Denied';
include __DIR__ . '/partials/header.php';
?>
<div class="container py-4">
  <div class="card p-4">
    <h4 class="mb-2">403 - Access Denied</h4>
    <p class="text-muted mb-3">ليس لديك صلاحية للوصول لهذه الصفحة.</p>
    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="<?= e(url(is_admin() ? 'admin/index.php' : 'employee/dashboard.php')) ?>">Go to Dashboard</a>
      <a class="btn btn-outline-secondary" href="<?= e(url('logout.php')) ?>">Logout</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
