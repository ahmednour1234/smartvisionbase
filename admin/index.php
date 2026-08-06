<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$title = APP_NAME . ' | Admin Panel';

$pdo = db();
$cron = null;
try {
  $stmt = $pdo->prepare("SELECT status, finished_at, message FROM job_runs WHERE job_name = :n ORDER BY id DESC LIMIT 1");
  $stmt->execute([':n' => 'daily_followups']);
  $cron = $stmt->fetch();
} catch (Exception $e) {
  $cron = null;
}

include __DIR__ . '/../partials/header.php';
?>
<?php
  $cronBadge = "<span class='badge bg-secondary'>Unknown</span>";
  $cronText = "No cron run recorded yet.";
  if ($cron && !empty($cron['status'])) {
    $status = (string)$cron['status'];
    $finished = $cron['finished_at'] ? (string)$cron['finished_at'] : '';
    $msg = (string)($cron['message'] ?? '');
    $cronText = "Last run: " . e($finished ?: '-') . " — " . e($msg);
    if ($status === 'success') {
      $cronBadge = "<span class='badge bg-success'>OK</span>";
      if ($finished) {
        $age = (time() - strtotime($finished)) / 3600;
        if ($age > 24) $cronBadge = "<span class='badge bg-warning text-dark'>Stale</span>";
      }
    } elseif ($status === 'failed') {
      $cronBadge = "<span class='badge bg-danger'>Failed</span>";
    } else {
      $cronBadge = "<span class='badge bg-info text-dark'>Running</span>";
    }
  }
?>
<div class="alert alert-light border d-flex justify-content-between align-items-center">
  <div>
    <b>Daily Follow-ups Cron</b> <?= $cronBadge ?>
    <div class="small text-muted mt-1"><?= $cronText ?></div>
  </div>
  <div class="small text-muted">Job: <code>daily_followups</code></div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Admin Panel</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/import.php') ?>">Import CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/performance.php') ?>">Performance</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/analytics.php') ?>">Analytics</a>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card p-4">
      <h5>Users</h5>
      <div class="text-muted small mb-3">Create staff accounts, disable users, reset passwords.</div>
      <a class="btn btn-primary w-100" href="<?= url('/users.php') ?>">Manage Users</a>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-4">
      <h5>Leads</h5>
      <div class="text-muted small mb-3">View all leads, assign Free leads to staff, update statuses.</div>
      <a class="btn btn-primary w-100" href="<?= url('/leads.php') ?>">Manage Leads</a>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-4">
      <h5>Countries</h5>
      <div class="text-muted small mb-3">Activate/deactivate countries (dropdown list in the system).</div>
      <a class="btn btn-primary w-100" href="<?= url('/countries.php') ?>">Manage Countries</a>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-4">
      <h5>Packages</h5>
      <div class="text-muted small mb-3">Add sponsorship packages to appear as dropdown in employee forms.</div>
      <a class="btn btn-primary w-100" href="<?= url('/packages.php') ?>">Manage Packages</a>
    </div>
  </div>


  <div class="col-md-4">
    <div class="card p-4">
      <h5>Events</h5>
      <div class="text-muted small mb-3">Create events to appear as dropdown when adding leads.</div>
      <a class="btn btn-primary w-100" href="<?= url('/events.php') ?>">Manage Events</a>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-4">
      <h5>Lost Categories</h5>
      <div class="text-muted small mb-3">Standardize lost reasons for management reporting.</div>
      <a class="btn btn-primary w-100" href="<?= url('/lost_categories.php') ?>">Manage Lost Categories</a>
    </div>
  </div>


  <div class="col-md-4">
    <div class="card p-4">
      <h5>Audit Log</h5>
      <div class="text-muted small mb-3">Track logins, imports, lead changes, and meetings.</div>
      <a class="btn btn-primary w-100" href="<?= url('/audit.php') ?>">View Audit</a>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-4">
      <h5>Exports</h5>
      <div class="text-muted small mb-3">Export performance and analytics as CSV for Excel.</div>
      <a class="btn btn-primary w-100" href="<?= url('/export.php?type=performance') ?>">Export Performance CSV</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
