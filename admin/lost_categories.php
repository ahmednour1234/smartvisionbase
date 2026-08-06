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
    $pdo->prepare("UPDATE lost_categories SET is_active=IF(is_active=1,0,1) WHERE id=?")->execute([$id]);
    audit('lostcat_toggle','lost_category',$id);
    flash_set('ok','Lost category status updated.');
    header('Location: ' . url('/lost_categories.php')); exit;
  }

  if ($action === 'add') {
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name==='') {
      flash_set('err','Name is required.');
    } else {
      $pdo->prepare("INSERT INTO lost_categories (name,is_active,sort_order) VALUES (?,1,0)")->execute([$name]);
      audit('lostcat_add','lost_category',(int)$pdo->lastInsertId(),['name'=>$name]);
      flash_set('ok','Lost category added.');
    }
    header('Location: ' . url('/lost_categories.php')); exit;
  }
}

$sql="SELECT * FROM lost_categories";
$params=[];
if ($q!==''){ $sql.=" WHERE name LIKE :q"; $params[':q']='%'.$q.'%'; }
$sql.=" ORDER BY sort_order, name";
$st=$pdo->prepare($sql); $st->execute($params); $rows=$st->fetchAll();

$title = APP_NAME . ' | Lost Categories';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Lost Categories</h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= url('/index.php') ?>">Back</a>
</div>

<?php if ($m = flash_get('ok')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash_get('err')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>

<div class="card p-3 mb-3">
  <form method="get" class="row g-2 align-items-end">
    <div class="col-md-8">
      <label class="form-label small">Search</label>
      <input name="q" class="form-control" value="<?= e($q) ?>" placeholder="Category name">
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary w-100">Search</button>
    </div>
  </form>
</div>

<div class="card p-3 mb-3">
  <h6 class="mb-2">Add Lost Category</h6>
  <form method="post" class="row g-2">
    <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
    <input type="hidden" name="action" value="add">
    <div class="col-md-10"><input name="name" class="form-control" placeholder="e.g., Price" required></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Add</button></div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
      <thead class="table-light"><tr><th>Name</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td class="fw-semibold"><?= e($r['name']) ?></td>
            <td><?= ((int)$r['is_active']===1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
            <td class="text-end">
              <form method="post" style="display:inline-block">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-outline-secondary btn-sm">Toggle</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="3" class="text-muted">No categories found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
