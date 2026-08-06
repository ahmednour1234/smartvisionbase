<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'add') {
        $name = trim((string)($_POST['name'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($name==='') {
            flash_set('err','Package name is required.');
            header('Location: ' . url('/packages.php')); exit;
        }
        $stmt = $pdo->prepare("INSERT INTO packages (name,is_active,sort_order) VALUES (:n,1,:s)
                               ON DUPLICATE KEY UPDATE sort_order=VALUES(sort_order), is_active=1");
        $stmt->execute([':n'=>$name, ':s'=>$sort]);
        audit('package_add','package',null,['name'=>$name]);
        flash_set('ok','Package saved.');
        header('Location: ' . url('/packages.php')); exit;
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE packages SET is_active=IF(is_active=1,0,1) WHERE id=:id")->execute([':id'=>$id]);
        audit('package_toggle','package',$id);
        flash_set('ok','Package status updated.');
        header('Location: ' . url('/packages.php')); exit;
    }

    if ($action === 'rename') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($name==='') {
            flash_set('err','Name is required.');
            header('Location: ' . url('/packages.php')); exit;
        }
        $stmt = $pdo->prepare("UPDATE packages SET name=:n, sort_order=:s WHERE id=:id");
        $stmt->execute([':n'=>$name, ':s'=>$sort, ':id'=>$id]);
        audit('package_update','package',$id,['name'=>$name]);
        flash_set('ok','Package updated.');
        header('Location: ' . url('/packages.php')); exit;
    }
}

$rows = $pdo->query("SELECT * FROM packages ORDER BY sort_order ASC, name ASC")->fetchAll();

$title = APP_NAME . ' | Admin - Packages';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="m-0">Packages</h4>
  <a class="btn btn-outline-dark" href="<?= e(url('/index.php')) ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card p-4">
      <h6 class="mb-3">Add Package</h6>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add">
        <div class="mb-2">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" placeholder="Diamond Sponsor" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Sort Order</label>
          <input class="form-control" name="sort_order" type="number" value="0">
        </div>
        <button class="btn btn-primary w-100">Save</button>
      </form>
      <div class="small text-muted mt-3">These packages appear as dropdown in Employee Add Lead.</div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card p-4">
      <h6 class="mb-3">All Packages</h6>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Sort</th>
              <th>Status</th>
              <th style="width:260px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td class="fw-bold">
                <form method="post" class="d-flex gap-2">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="rename">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input class="form-control form-control-sm" name="name" value="<?= e($r['name']) ?>">
              </td>
              <td style="width:120px;">
                  <input class="form-control form-control-sm" name="sort_order" type="number" value="<?= (int)$r['sort_order'] ?>">
              </td>
              <td>
                <?= (int)$r['is_active']===1 ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-secondary'>Disabled</span>" ?>
              </td>
              <td>
                  <button class="btn btn-sm btn-outline-primary">Update</button>
                </form>
                <form method="post" class="d-inline">
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
