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
    $pdo->prepare("UPDATE events SET is_active=IF(is_active=1,0,1) WHERE id=?")->execute([$id]);
    audit('event_toggle','event',$id);
    flash_set('ok','Event status updated.');
    header('Location: ' . url('/events.php')); exit;
  }

  if ($action === 'add') {
    $name = trim((string)($_POST['name'] ?? ''));
    $from = $_POST['date_from'] ?? null;
    $to   = $_POST['date_to'] ?? null;
    $loc  = trim((string)($_POST['location'] ?? ''));
    if ($name==='') {
      flash_set('err','Event name is required.');
    } else {
      $st = $pdo->prepare("INSERT INTO events (name,event_date_from,event_date_to,location,is_active,sort_order) VALUES (?,?,?,?,1,0)");
      $st->execute([$name, ($from?:null), ($to?:null), ($loc?:null)]);
      audit('event_add','event',(int)$pdo->lastInsertId(),['name'=>$name]);
      flash_set('ok','Event added.');
    }

    header('Location: ' . url('/events.php')); exit;
  }

  // Full admin edit (no DB changes)
  if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $from = $_POST['date_from'] ?? null;
    $to   = $_POST['date_to'] ?? null;
    $loc  = trim((string)($_POST['location'] ?? ''));
    if ($id > 0 && $name !== '') {
      $stmt = $pdo->prepare("UPDATE events SET name=?, event_date_from=?, event_date_to=?, location=? WHERE id=?");
      $stmt->execute([$name, ($from?:null), ($to?:null), ($loc?:null), $id]);
      audit('event_update','event',$id,['name'=>$name]);
      flash_set('ok','Event updated.');
    } else {
      flash_set('err','Missing required fields.');
    }
    header('Location: ' . url('/events.php')); exit;
  }
}

$sql = "SELECT * FROM events";
$params = [];
if ($q !== '') {
  $sql .= " WHERE name LIKE :q";
  $params[':q'] = '%' . $q . '%';
}
$sql .= " ORDER BY sort_order, name";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

$title = APP_NAME . ' | Events';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Events</h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= url('/index.php') ?>">Back</a>
</div>

<?php if ($m = flash_get('ok')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash_get('err')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>

<div class="card p-3 mb-3">
  <form method="get" class="row g-2 align-items-end">
    <div class="col-md-8">
      <label class="form-label small">Search</label>
      <input name="q" class="form-control" value="<?= e($q) ?>" placeholder="Event name">
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary w-100">Search</button>
    </div>
  </form>
</div>

<div class="card p-3 mb-3">
  <h6 class="mb-2">Add Event</h6>
  <form method="post" class="row g-2">
    <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
    <input type="hidden" name="action" value="add">
    <div class="col-md-4"><input name="name" class="form-control" placeholder="Event name" required></div>
    <div class="col-md-2"><input type="date" name="date_from" class="form-control" placeholder="From"></div>
    <div class="col-md-2"><input type="date" name="date_to" class="form-control" placeholder="To"></div>
    <div class="col-md-3"><input name="location" class="form-control" placeholder="Location"></div>
    <div class="col-md-1"><button class="btn btn-outline-primary w-100">Add</button></div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Dates</th>
          <th>Location</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <form method="post">
              <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <td>
                <input class="form-control form-control-sm" name="name" value="<?= e($r['name']) ?>" required>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <input class="form-control form-control-sm" type="date" name="start_date" value="<?= e($r['event_date_from'] ?? '') ?>" style="max-width:160px">
                  <input class="form-control form-control-sm" type="date" name="end_date" value="<?= e($r['event_date_to'] ?? '') ?>" style="max-width:160px">
                </div>
              </td>
              <td><input class="form-control form-control-sm" name="location" value="<?= e($r['location'] ?? '') ?>"></td>
              <td><?= ((int)$r['is_active']===1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
              <td class="text-end">
                <button class="btn btn-primary btn-sm" name="action" value="update">Save</button>
                <button class="btn btn-outline-secondary btn-sm" name="action" value="toggle">Toggle</button>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5" class="text-muted">No events found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
