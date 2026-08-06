<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();
$pdo = db();

$tot = $pdo->query("SELECT
  SUM(CASE WHEN status='won' THEN 1 ELSE 0 END) AS won_cnt,
  SUM(CASE WHEN status='lost' THEN 1 ELSE 0 END) AS lost_cnt,
  SUM(CASE WHEN status IN ('new','contacted','meeting','negotiation') THEN 1 ELSE 0 END) AS open_cnt,
  COUNT(*) AS total_cnt
  FROM leads")->fetch();

$per = $pdo->query("SELECT u.name,
  SUM(CASE WHEN l.status='won' THEN 1 ELSE 0 END) AS won_cnt,
  SUM(CASE WHEN l.status='lost' THEN 1 ELSE 0 END) AS lost_cnt
  FROM users u
  LEFT JOIN leads l ON l.sales_rep_id=u.id
  WHERE u.role='staff'
  GROUP BY u.id
  ORDER BY won_cnt DESC, lost_cnt ASC, u.name ASC")->fetchAll();

$latest_lost = $pdo->query("SELECT l.id, l.company_name, u.name AS owner_name, l.lost_reason, l.lost_at
  FROM leads l
  LEFT JOIN users u ON u.id=l.sales_rep_id
  WHERE l.status='lost'
  ORDER BY COALESCE(l.lost_at, l.updated_at, l.created_at) DESC
  LIMIT 20")->fetchAll();

$title = APP_NAME . ' | Analytics';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Lost vs Won Analytics</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/export.php?type=analytics') ?>">Export CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/index.php') ?>">Back</a>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card p-3"><div class="text-muted">Total Leads</div><div class="fs-4 fw-bold"><?= (int)$tot['total_cnt'] ?></div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted">Won</div><div class="fs-4 fw-bold"><?= (int)$tot['won_cnt'] ?></div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted">Lost</div><div class="fs-4 fw-bold"><?= (int)$tot['lost_cnt'] ?></div></div></div>
  <div class="col-md-3"><div class="card p-3"><div class="text-muted">Open</div><div class="fs-4 fw-bold"><?= (int)$tot['open_cnt'] ?></div></div></div>
</div>

<div class="card p-3 mb-3">
  <h5 class="mb-3">Per Employee</h5>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light"><tr><th>Employee</th><th>Won</th><th>Lost</th></tr></thead>
      <tbody>
        <?php foreach($per as $r): ?>
          <tr>
            <td><?= e($r['name']) ?></td>
            <td><?= (int)$r['won_cnt'] ?></td>
            <td><?= (int)$r['lost_cnt'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Latest Lost Leads</h5>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/export.php?type=lost_latest') ?>">Export</a>
  </div>
  <div class="table-responsive mt-3">
    <table class="table table-sm align-middle">
      <thead class="table-light"><tr><th>Company</th><th>Owner</th><th>Reason</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach($latest_lost as $l): ?>
          <tr>
            <td><?= e($l['company_name']) ?></td>
            <td><?= e($l['owner_name'] ?? 'Free') ?></td>
            <td><?= e($l['lost_reason'] ?? '') ?></td>
            <td><?= e($l['lost_at'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
