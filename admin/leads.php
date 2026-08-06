<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';

require_admin();
$title = "Leads | Admin";
$pdo = db();
$msg = '';

if (is_post() && ($_POST['action'] ?? '') === 'reassign') {
  csrf_check();
  $lead_id = (int)($_POST['lead_id'] ?? 0);
  $owner = $_POST['owner_id'] ?? '';
  $owner_id = ($owner === '') ? null : (int)$owner;

  $stmt = $pdo->prepare("UPDATE leads SET sales_rep_id=?, updated_by=? WHERE id=?");
  $stmt->execute([$owner_id, (int)current_user()['id'], $lead_id]);
  audit('reassign','lead',$lead_id,['new_owner'=>$owner_id]);
  $msg = '<div class="alert alert-success">Lead reassigned.</div>';
}

$q = trim((string)($_GET['q'] ?? ''));

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = (int)($_GET['per_page'] ?? 60);
if ($perPage < 10) $perPage = 10;
if ($perPage > 200) $perPage = 200;

$where = '';
$params = [];
if ($q !== '') {
  $where = "WHERE l.company_name LIKE :s";
  $params[':s'] = '%' . $q . '%';
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads l $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$totalPages = (int)ceil(($total ?: 1) / $perPage);
if ($totalPages < 1) $totalPages = 1;
if ($page > $totalPages) $page = $totalPages;

$offset = ($page - 1) * $perPage;

$sql = "SELECT l.id,l.company_name,l.status,l.next_followup,u.name AS owner_name,l.sales_rep_id
        FROM leads l
        LEFT JOIN users u ON u.id=l.sales_rep_id
        $where
        ORDER BY l.created_at DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
  $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

$users = $pdo->query("SELECT id,name FROM users WHERE is_active=1 ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../partials/header.php';

$qsBase = ['q' => $q, 'per_page' => $perPage];
$prevPage = max(1, $page - 1);
$nextPage = $page + 1;
?>
<div class="card p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="m-0">Leads Management</h5>
    <a class="btn btn-sm btn-outline-secondary" href="index.php">Back</a>
  </div>
  <?php echo $msg; ?>
  <form method="get" class="row g-2 mt-2">
    <div class="col-md-8">
      <input class="form-control" name="q" value="<?php echo e($q); ?>" placeholder="Search company name...">
    </div>
    <div class="col-md-2">
      <select class="form-select" name="per_page">
        <?php foreach ([30,60,100,200] as $n): ?>
          <option value="<?php echo (int)$n; ?>" <?php echo $perPage === (int)$n ? 'selected' : ''; ?>><?php echo (int)$n; ?>/page</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <div class="d-flex justify-content-between align-items-center mt-2">
    <div class="small text-muted">
      Total: <b><?php echo (int)$total; ?></b> — Page <b><?php echo (int)$page; ?></b> of <b><?php echo (int)$totalPages; ?></b>
    </div>
    <?php if ($total > 0): ?>
      <div class="btn-group">
        <a class="btn btn-outline-secondary btn-sm <?php echo $page <= 1 ? 'disabled' : ''; ?>"
           href="?<?php echo http_build_query($qsBase + ['page' => $prevPage]); ?>">Prev</a>
        <a class="btn btn-outline-secondary btn-sm <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
           href="?<?php echo http_build_query($qsBase + ['page' => min($totalPages, $nextPage)]); ?>">Next</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Company</th>
          <th>Status</th>
          <th>Follow-up</th>
          <th>Owner</th>
          <th>Reassign</th>
          <th>Open</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="fw-semibold"><?php echo e($r['company_name']); ?></td>
            <td><?php echo e($r['status']); ?></td>
            <td><?php echo e($r['next_followup'] ?? '-'); ?></td>
            <td><?php echo e($r['owner_name'] ?? 'Free'); ?></td>
            <td>
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="_csrf" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="action" value="reassign">
                <input type="hidden" name="lead_id" value="<?php echo (int)$r['id']; ?>">
                <select class="form-select form-select-sm" name="owner_id">
                  <option value="">-- Free (Available) --</option>
                  <?php foreach ($users as $u): ?>
                    <option value="<?php echo (int)$u['id']; ?>" <?php echo ((int)$r['sales_rep_id'] === (int)$u['id']) ? 'selected' : ''; ?>>
                      <?php echo e($u['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-warning">Save</button>
              </form>
            </td>
            <td>
<a class="btn btn-sm btn-outline-primary"
   href="/employee/lead.php?id=<?= (int)$r['id'] ?>">Open</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
