<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();
$pdo = db();

$q = trim((string)($_GET['q'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $action = (string)($_POST['action'] ?? '');

  
if ($action === 'update') {
  $id = (int)($_POST['id'] ?? 0);
  $name = trim((string)($_POST['name'] ?? ''));
  $date_from = trim((string)($_POST['event_date_from'] ?? ''));
  $date_to = trim((string)($_POST['event_date_to'] ?? ''));
  $location = trim((string)($_POST['location'] ?? ''));
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  $is_active = (int)($_POST['is_active'] ?? 1) === 1 ? 1 : 0;

  if ($id<=0 || $name==='') {
    flash_set('err','Invalid event data.');
    header('Location: ' . url('/events.php')); exit;
  }

  $stmt = $pdo->prepare("UPDATE events SET name=?, event_date_from=?, event_date_to=?, location=?, is_active=?, sort_order=? WHERE id=?");
  $stmt->execute([$name, $date_from ?: null, $date_to ?: null, $location ?: null, $is_active, $sort_order, $id]);
  audit('event_update','event',$id);
  flash_set('ok','Event updated.');
  header('Location: ' . url('/events.php')); exit;
}

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
            <td class="fw-semibold"><?= e($r['name']) ?></td>
            <td class="small text-muted"><?= e($r['event_date_from'] ?? '') ?> <?= ($r['event_date_to'] ? '→ ' . e($r['event_date_to']) : '') ?></td>
            <td><?= e($r['location'] ?? '') ?></td>
            <td><?= ((int)$r['is_active']===1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
	            <td class="text-end">
	              <button type="button" class="btn btn-outline-primary btn-sm me-1 js-edit-event"
	                      data-id="<?= (int)$r['id'] ?>"
	                      data-name="<?= e($r['name']) ?>"
	                      data-from="<?= e($r['event_date_from'] ?? '') ?>"
	                      data-to="<?= e($r['event_date_to'] ?? '') ?>"
	                      data-location="<?= e($r['location'] ?? '') ?>"
	              >Edit</button>
              <form method="post" style="display:inline-block">
                <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-outline-secondary btn-sm">Toggle</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5" class="text-muted">No events found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="ev_id" value="0">
        <div class="modal-header">
          <h5 class="modal-title">Edit Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Event name</label>
            <input class="form-control" name="name" id="ev_name" required>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">From</label>
              <input type="date" class="form-control" name="event_date_from" id="ev_from">
            </div>
            <div class="col-6">
              <label class="form-label">To</label>
              <input type="date" class="form-control" name="event_date_to" id="ev_to">
            </div>
          </div>
          <div class="mb-2 mt-2">
            <label class="form-label">Location</label>
            <input class="form-control" name="location" id="ev_location">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  (function(){
    const modalEl = document.getElementById('editEventModal');
    const modal = new bootstrap.Modal(modalEl);
    document.querySelectorAll('.js-edit-event').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        document.getElementById('ev_id').value = btn.dataset.id || 0;
        document.getElementById('ev_name').value = btn.dataset.name || '';
        document.getElementById('ev_from').value = btn.dataset.from || '';
        document.getElementById('ev_to').value = btn.dataset.to || '';
        document.getElementById('ev_location').value = btn.dataset.location || '';
        modal.show();
      });
    });
  })();
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
