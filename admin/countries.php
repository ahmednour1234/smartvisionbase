<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();
$pdo = db();

$q = trim((string)($_GET['q'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE countries SET is_active=IF(is_active=1,0,1) WHERE id=:id")->execute([':id'=>$id]);
        audit('country_toggle','country',$id);
        flash_set('ok','Country status updated.');
        header('Location: ' . url('/countries.php')); exit;
    }

    if ($action === 'add') {
        $name = trim((string)($_POST['name'] ?? ''));
        $iso2 = strtoupper(trim((string)($_POST['iso2'] ?? '')));
        if ($name==='' || !preg_match('/^[A-Z]{2}$/', $iso2)) {
            flash_set('err','Country name and ISO2 (2 letters) are required.');
            header('Location: ' . url('/countries.php')); exit;
        }
        $stmt = $pdo->prepare("INSERT INTO countries (iso2,name,is_active,sort_order) VALUES (:i,:n,1,9999)
                               ON DUPLICATE KEY UPDATE name=VALUES(name), is_active=1");
        $stmt->execute([':i'=>$iso2, ':n'=>$name]);
        audit('country_add','country',null,['iso2'=>$iso2,'name'=>$name]);
        flash_set('ok','Country added/updated.');
        header('Location: ' . url('/countries.php')); exit;
    }
}

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM countries WHERE name LIKE :s OR iso2 LIKE :s ORDER BY sort_order ASC, name ASC LIMIT 400");
    $stmt->execute([':s'=>'%'.$q.'%']);
    $rows = $stmt->fetchAll();
} else {
    $rows = $pdo->query("SELECT * FROM countries ORDER BY sort_order ASC, name ASC LIMIT 400")->fetchAll();
}

$title = APP_NAME . ' | Admin - Countries';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="m-0">Countries</h4>
  <a class="btn btn-outline-dark" href="<?= e(url('/index.php')) ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card p-4">
      <h6 class="mb-3">Search</h6>
      <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Name or ISO2">
        <button class="btn btn-dark">Go</button>
      </form>

      <hr>
      <h6 class="mb-2">Add Country (optional)</h6>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add">
        <div class="mb-2">
          <label class="form-label">ISO2</label>
          <input class="form-control" name="iso2" maxlength="2" placeholder="EG" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" placeholder="Egypt" required>
        </div>
        <button class="btn btn-primary w-100">Save</button>
      </form>
      <div class="small text-muted mt-3">All countries are already seeded; this is only for edge cases.</div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card p-4">
      <h6 class="mb-3">List (<?= count($rows) ?> shown)</h6>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>ISO2</th>
              <th>Name</th>
              <th>Status</th>
              <th style="width:120px;">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td class="fw-bold"><?= e($r['iso2']) ?></td>
              <td><?= e($r['name']) ?></td>
              <td>
                <?= (int)$r['is_active']===1 ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-secondary'>Disabled</span>" ?>
              </td>
              <td>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger"><?= (int)$r['is_active']===1 ? 'Disable' : 'Enable' ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
