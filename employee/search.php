<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();

$title = APP_NAME . ' | Quick Search';

$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = (int)($_GET['per_page'] ?? 50);
if ($perPage < 10) $perPage = 10;
if ($perPage > 200) $perPage = 200;

$results = [];
$total = 0;
$totalPages = 0;

if ($q !== '') {
    $pdo = db();
    $like = '%' . $q . '%';

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE company_name LIKE :s");
    $countStmt->execute([':s' => $like]);
    $total = (int)$countStmt->fetchColumn();

    $totalPages = (int)ceil($total / $perPage);
    if ($totalPages < 1) $totalPages = 1;
    if ($page > $totalPages) $page = $totalPages;

    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->prepare("SELECT l.*, u.name AS owner_name, u.email AS owner_email, p.name AS package_name
                           FROM leads l
                           LEFT JOIN users u ON l.sales_rep_id = u.id
                           LEFT JOIN packages p ON l.interested_package_id = p.id
                           WHERE l.company_name LIKE :s
                           ORDER BY l.company_name ASC
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':s', $like, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
}

include __DIR__ . '/../partials/header.php';

$qsBase = ['q' => $q, 'per_page' => $perPage];
$prevPage = max(1, $page - 1);
$nextPage = $page + 1;
?>
<div class="card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="m-0">Quick Search</h5>
    <div class="d-flex gap-2 sv-toolbar">
      <a class="btn btn-outline-secondary btn-sm" href="<?= e(url('employee/import.php')) ?>">Import CSV</a>
      <a class="btn btn-primary btn-sm" href="<?= e(url('employee/add_lead.php')) ?>">Add Lead</a>
    </div>
  </div>

  <form class="row g-2" method="get">
    <div class="col-md-9">
      <input class="form-control form-control-lg" name="q" value="<?= e($q) ?>" placeholder="Type company name..." required>
    </div>
    <div class="col-md-2">
      <select class="form-select form-select-lg" name="per_page">
        <?php foreach ([25,50,100,200] as $n): ?>
          <option value="<?= (int)$n ?>" <?= $perPage === (int)$n ? 'selected' : '' ?>><?= (int)$n ?>/page</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-1">
      <button class="btn btn-dark btn-lg w-100">Go</button>
    </div>
  </form>

  <?php if ($q !== ''): ?>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <div class="small text-muted">
        Total: <b><?= (int)$total ?></b> results — Page <b><?= (int)$page ?></b> of <b><?= (int)$totalPages ?></b>
      </div>

      <?php if ($total > 0): ?>
        <div class="btn-group">
          <a class="btn btn-outline-secondary btn-sm <?= $page <= 1 ? 'disabled' : '' ?>"
             href="?<?= http_build_query($qsBase + ['page' => $prevPage]) ?>">Prev</a>
          <a class="btn btn-outline-secondary btn-sm <?= $page >= $totalPages ? 'disabled' : '' ?>"
             href="?<?= http_build_query($qsBase + ['page' => min($totalPages, $nextPage)]) ?>">Next</a>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($results)): ?>
    <div class="table-responsive mt-4">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Company</th>
            <th>Package</th>
            <th>Next Follow-up</th>
            <th>Availability</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $r): ?>
          <?php
            $ownerId = $r['sales_rep_id'];
            $meId = (int)(current_user()['id'] ?? 0);
            if (!$ownerId) {
                $status = "<span class='badge bg-success'>Available (Free)</span>";
            } elseif ((int)$ownerId === $meId) {
                $status = "<span class='badge bg-primary'>Owned by you</span>";
            } else {
                if (is_admin()) {
                    $status = "<span class='badge bg-warning text-dark'>Owned by: " . e($r['owner_name'] ?? '') . "</span>";
                } else {
                    $status = "<span class='badge badge-locked'>Locked</span>";
                }
            }
          ?>
          <tr>
            <td class="fw-bold"><?= e($r['company_name']) ?></td>
            <td><?= e($r['package_name'] ?? '') ?></td>
            <td><?= e($r['next_followup'] ?: 'No date') ?></td>
            <td><?= $status ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php elseif ($q !== ''): ?>
    <div class="alert alert-warning mt-4">No results found.</div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
