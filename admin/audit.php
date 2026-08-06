<?php
require_once __DIR__ . '/../config.php';
require_admin();

$pdo = db();
$q = trim((string)($_GET['q'] ?? ''));

$params = [];
$sql = "SELECT a.created_at, a.action, a.entity, a.entity_id, a.ip, u.name AS user_name, a.meta
        FROM audit_log a
        LEFT JOIN users u ON a.user_id = u.id";

if ($q !== '') {
    $sql .= " WHERE a.action LIKE :q OR a.entity LIKE :q OR u.name LIKE :q";
    $params[':q'] = '%' . $q . '%';
}

$sql .= " ORDER BY a.id DESC LIMIT 300";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$page_title = APP_NAME . ' | Admin | Audit Log';
require_once __DIR__ . '/../partials/header.php';
?>
<div class="card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="m-0">Audit Log</h5>
      <div class="text-muted small">Last 300 actions (login, create user, assign lead, etc.).</div>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="<?= e(BASE_PATH) ?>/index.php">Back</a>
  </div>

  <form method="get" class="row g-2 mt-3">
    <div class="col-md-10">
      <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search action/entity/user...">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <div class="table-responsive mt-3">
    <table class="table align-middle">
      <thead class="table-light">
        <tr>
          <th>Time</th>
          <th>User</th>
          <th>Action</th>
          <th>Entity</th>
          <th>Entity ID</th>
          <th>IP</th>
          <th>Meta</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="small"><?= e($r['created_at']) ?></td>
          <td><?= e($r['user_name'] ?? '-') ?></td>
          <td><span class="badge bg-secondary"><?= e($r['action']) ?></span></td>
          <td><?= e($r['entity']) ?></td>
          <td><?= e($r['entity_id'] !== null ? (string)$r['entity_id'] : '-') ?></td>
          <td class="small"><?= e($r['ip'] ?? '-') ?></td>
          <td class="small text-muted"><?= e($r['meta'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="7" class="text-muted">No audit records.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
