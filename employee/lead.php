<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();
$u = current_user();
if ($u['role'] !== 'staff' && $u['role'] !== 'admin') { http_response_code(403); exit('Access denied'); }

$pdo = db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(404); exit('Not found'); }

$st = $pdo->prepare("SELECT l.*, p.name AS package_name, rep.name AS rep_name, ev.name AS event_name, lc.name AS lost_category_name
                     FROM leads l
                     LEFT JOIN packages p ON l.interested_package_id=p.id
                     LEFT JOIN users rep ON l.sales_rep_id=rep.id
                     LEFT JOIN events ev ON l.event_id=ev.id
                     LEFT JOIN lost_categories lc ON l.lost_category_id=lc.id
                     WHERE l.id=? LIMIT 1");
$st->execute([$id]);
$lead = $st->fetch();
if (!$lead) { http_response_code(404); exit('Not found'); }


$events = $pdo->query("SELECT id, name FROM events WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$packages = $pdo->query("SELECT id, name FROM packages WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$lost_categories = $pdo->query("SELECT id, name FROM lost_categories WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$all_countries = $pdo->query("SELECT id, name FROM countries WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$all_users = $pdo->query("SELECT id, name FROM users WHERE is_active=1 ORDER BY name")->fetchAll();


$is_owner = ((int)$lead['sales_rep_id'] === (int)$u['id']);
$is_admin = ($u['role'] === 'admin');

if (!$is_admin && !$is_owner) { http_response_code(403); exit('Access denied'); }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();
  $action = $_POST['action'] ?? '';

  // Update company + contact fields (Owner/Admin only)
  if ($action === 'contact') {
    $company_name = trim((string)($_POST['company_name'] ?? ''));
    if ($company_name === '') {
      flash_set('err', 'Company name is required.');
      header('Location: ' . url('employee/lead.php?id=' . $id));
      exit;
    }

    // Prevent duplicates when renaming
    $dup = $pdo->prepare("SELECT id FROM leads WHERE company_name = ? AND id <> ? LIMIT 1");
    $dup->execute([$company_name, $id]);
    if ($dup->fetch()) {
      flash_set('err', 'This company already exists in the database.');
      header('Location: ' . url('employee/lead.php?id=' . $id));
      exit;
    }

    $contact_person = trim((string)($_POST['contact_person'] ?? ''));
    $contact_mobile = trim((string)($_POST['contact_mobile'] ?? ''));
    $contact_email = trim((string)($_POST['contact_email'] ?? ''));
    $contact_linkedin = trim((string)($_POST['contact_linkedin'] ?? ''));
    $company_website = trim((string)($_POST['company_website'] ?? ''));

    $stmt = $pdo->prepare("UPDATE leads SET company_name=?, contact_person=?, contact_mobile=?, contact_email=?, contact_linkedin=?, company_website=?, updated_by=? WHERE id=?");
    $stmt->execute([
      $company_name,
      ($contact_person !== '' ? $contact_person : null),
      ($contact_mobile !== '' ? $contact_mobile : null),
      ($contact_email !== '' ? $contact_email : null),
      ($contact_linkedin !== '' ? $contact_linkedin : null),
      ($company_website !== '' ? $company_website : null),
      (int)$u['id'],
      $id
    ]);
    audit('lead_update','lead',$id,['fields'=>'contact']);
    try {
      $pdo->prepare("INSERT INTO lead_activities (lead_id,user_id,activity_type,message) VALUES (?,?, 'update', ?)")
          ->execute([$id,(int)$u['id'],'Updated company/contact details']);
    } catch (Exception $e) {}

    flash_set('ok', 'Saved.');
    header('Location: ' . url('employee/lead.php?id=' . $id));
    exit;
  }

  // Update selected countries (Owner/Admin only)
  if ($action === 'countries') {
    $country_ids = $_POST['country_ids'] ?? [];
    if (!is_array($country_ids)) $country_ids = [];
    $clean = [];
    foreach ($country_ids as $cid) {
      $cid = (int)$cid;
      if ($cid > 0) $clean[] = $cid;
    }
    $clean = array_values(array_unique($clean));

    $pdo->beginTransaction();
    try {
      $pdo->prepare("DELETE FROM lead_countries WHERE lead_id=?")->execute([$id]);
      if ($clean) {
        $ins = $pdo->prepare("INSERT IGNORE INTO lead_countries (lead_id, country_id) VALUES (?,?)");
        foreach ($clean as $cid) $ins->execute([$id,$cid]);
      }
      $pdo->commit();
      audit('lead_update','lead',$id,['fields'=>'countries']);
      try {
        $pdo->prepare("INSERT INTO lead_activities (lead_id,user_id,activity_type,message) VALUES (?,?, 'update', ?)")
            ->execute([$id,(int)$u['id'],'Updated countries']);
      } catch (Exception $e) {}
      flash_set('ok', 'Saved.');
    } catch (Exception $e) {
      $pdo->rollBack();
      flash_set('err', 'Failed to save countries.');
    }
    header('Location: ' . url('employee/lead.php?id=' . $id));
    exit;
  }

  // Admin-only: change owner
  if ($action === 'owner' && $is_admin) {
    $owner_id = $_POST['owner_id'] ?? '';
    $owner_id = ($owner_id === '') ? null : (int)$owner_id;
    $pdo->prepare("UPDATE leads SET sales_rep_id=?, updated_by=? WHERE id=?")
        ->execute([$owner_id,(int)$u['id'],$id]);
    audit('reassign','lead',$id,['new_owner'=>$owner_id]);
    flash_set('ok', 'Owner updated.');
    header('Location: ' . url('employee/lead.php?id=' . $id));
    exit;
  }

  
  if ($action === 'details') {
    $event_id = (int)($_POST['event_id'] ?? 0); $event_id = $event_id>0 ? $event_id : null;
    $pkg_id = (int)($_POST['package_id'] ?? 0); $pkg_id = $pkg_id>0 ? $pkg_id : null;

    $expected_value = trim((string)($_POST['expected_value'] ?? ''));
    $expected_value = ($expected_value!=='' && is_numeric($expected_value)) ? (float)$expected_value : null;

    $currency = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));
    if ($currency==='') $currency='USD';
    $currency = substr($currency,0,3);

    $probability = trim((string)($_POST['probability'] ?? ''));
    $probability = ($probability!=='' && is_numeric($probability)) ? (int)$probability : null;
    if ($probability!==null) $probability = max(0,min(100,$probability));

    $expected_close = $_POST['expected_close_date'] ?? null;
    $next_followup = $_POST['next_followup'] ?? null;
    $lead_notes = trim((string)($_POST['lead_notes'] ?? ''));

    $stmt = $pdo->prepare("UPDATE leads SET event_id=?, interested_package_id=?, expected_value=?, currency=?, probability=?, expected_close_date=?, next_followup=?, lead_notes=?, updated_by=? WHERE id=?");
    $stmt->execute([$event_id,$pkg_id,$expected_value,$currency,$probability,($expected_close?:null),($next_followup?:null),($lead_notes?:null),(int)$u['id'],$id]);
    audit('lead_update','lead',$id,['fields'=>'details']);
    // optional activity
    try {
      $pdo->prepare("INSERT INTO lead_activities (lead_id,user_id,activity_type,message) VALUES (?,?, 'update', ?)")->execute([$id,(int)$u['id'],'Updated lead details']);
    } catch (Exception $e) {}
    header('Location: ' . url('employee/lead.php?id=' . $id . '&m=updated'));
    exit;
  }

if ($action === 'status') {
    $status = $_POST['status'] ?? 'new';
    $allowed = ['new','contacted','meeting','negotiation','won','lost'];
    if (!in_array($status, $allowed, true)) $status='new';

	    $lost_category_id = (int)($_POST['lost_category_id'] ?? 0);
	    $lost_category_id = $lost_category_id > 0 ? $lost_category_id : null;
	    $lost_reason = trim((string)($_POST['lost_reason'] ?? ''));
	    $lost_at = null;
	    if ($status === 'lost') {
	        $lost_at = date('Y-m-d H:i:s');
	    } else {
	        // Clear lost fields when status is not lost (avoids stale data)
	        $lost_category_id = null;
	        $lost_reason = '';
	    }

	    $upd = $pdo->prepare("UPDATE leads SET status=?, lost_category_id=?, lost_reason=?, lost_at=?, updated_by=? WHERE id=?");
	    $upd->execute([$status, $lost_category_id, ($lost_reason !== '' ? $lost_reason : null), $lost_at, (int)$u['id'], $id]);
    audit('lead_status', 'lead', $id, ['status'=>$status]);
    header('Location: ' . url('employee/lead.php?id='.$id.'&m=updated'));
    exit;
  }
}

if (($_GET['m'] ?? '') === 'updated') $msg = 'Saved.';

$title = APP_NAME . ' | Lead';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0"><?= e($lead['company_name']) ?></h3>
  <a class="btn btn-outline-secondary btn-sm" href="<?= url('employee/dashboard.php') ?>">Back</a>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card p-3">
      <h5 class="mb-3">Lead Details</h5>

      <div class="row">
        <div class="col-md-6">
          <div class="small text-muted">Owner</div>
          <div><?= e($lead['rep_name'] ?? 'Free Lead') ?></div>
        </div>
        <div class="col-md-6">
          <div class="small text-muted">Package</div>
          <div><?= e($lead['package_name'] ?? '') ?></div>
        </div>
      </div>

      <hr>
      <h6 class="mb-2">Company & Contact (Visible to Owner/Admin only)</h6>
      <form method="post" id="sv-contact-form" class="row g-2 mb-2">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="contact">

        <div class="col-md-12">
          <label class="form-label small">Company Name</label>
          <input name="company_name" class="form-control" value="<?= e($lead['company_name'] ?? '') ?>" required>
          <div class="form-text">Changing the company name will be blocked if a duplicate already exists.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label small">Contact Person</label>
          <input name="contact_person" class="form-control" value="<?= e($lead['contact_person'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label small">Mobile</label>
          <div class="input-group">
            <input id="contact_mobile" name="contact_mobile" class="form-control" value="<?= e($lead['contact_mobile'] ?? '') ?>" placeholder="e.g. 010... or +20...">
            <a id="wa_btn" class="btn btn-success" target="_blank" rel="noopener" href="<?= e(whatsapp_chat_url($lead['contact_mobile'] ?? '')) ?>" title="Chat on WhatsApp" style="display: <?= whatsapp_chat_url($lead['contact_mobile'] ?? '') ? 'inline-flex' : 'none' ?>; align-items:center; gap:6px;">
              <span aria-hidden="true">WA</span>
            </a>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Email</label>
          <input type="email" name="contact_email" class="form-control" value="<?= e($lead['contact_email'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label small">LinkedIn</label>
          <input name="contact_linkedin" class="form-control" value="<?= e($lead['contact_linkedin'] ?? '') ?>">
        </div>
        <div class="col-md-12">
          <label class="form-label small">Company Website</label>
          <input name="company_website" class="form-control" value="<?= e($lead['company_website'] ?? '') ?>">
        </div>

        <div class="col-12">
          <button class="btn btn-outline-primary btn-sm">Save Company & Contact</button>
        </div>
      </form>

      <?php if ($is_admin): ?>
        <div class="border rounded p-2 bg-light mt-2">
          <div class="small fw-semibold mb-1">Admin Only: Change Owner</div>
          <form method="post" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="owner">
            <select class="form-select form-select-sm" name="owner_id" style="max-width:320px;">
              <option value="">-- Free (Available) --</option>
              <?php foreach($all_users as $usr): ?>
                <option value="<?= (int)$usr['id'] ?>" <?= ((int)($lead['sales_rep_id'] ?? 0) === (int)$usr['id']) ? 'selected' : '' ?>>
                  <?= e($usr['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-warning">Update Owner</button>
          </form>
        </div>
      <?php endif; ?>


      <hr>
      <h6 class="mb-2">Commercial Details</h6>
      <form method="post" id="sv-details-form" class="row g-2 mb-3">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="details">

        <div class="col-md-6">
          <label class="form-label small">Event</label>
          <select name="event_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach($events as $ev): ?>
              <option value="<?= (int)$ev['id'] ?>" <?= ((int)($lead['event_id'] ?? 0) === (int)$ev['id']) ? 'selected' : '' ?>><?= e($ev['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label small">Package</label>
          <select name="package_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach($packages as $p): ?>
              <option value="<?= (int)$p['id'] ?>" <?= ((int)($lead['interested_package_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label small">Expected Value</label>
          <input name="expected_value" class="form-control" value="<?= e($lead['expected_value'] ?? '') ?>">
        </div>

        <div class="col-md-2">
          <label class="form-label small">Currency</label>
          <input name="currency" class="form-control" value="<?= e($lead['currency'] ?? 'USD') ?>" maxlength="3">
        </div>

        <div class="col-md-2">
          <label class="form-label small">Probability %</label>
          <input name="probability" class="form-control" value="<?= e($lead['probability'] ?? '') ?>" placeholder="0-100">
        </div>

        <div class="col-md-4">
          <label class="form-label small">Expected Close Date</label>
          <input type="date" name="expected_close_date" class="form-control" value="<?= e($lead['expected_close_date'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label small">Next Follow-up Date</label>
          <input type="date" name="next_followup" class="form-control" value="<?= e($lead['next_followup'] ?? '') ?>">
        </div>

        <div class="col-md-12">
          <label class="form-label small">Lead Notes</label>
          <textarea name="lead_notes" class="form-control" rows="3"><?= e($lead['lead_notes'] ?? '') ?></textarea>
        </div>

        <div class="col-12">
          <button class="btn btn-outline-primary btn-sm">Save Details</button>
        </div>
      </form>


      <hr>
      <form method="post" id="sv-status-form" class="row g-2">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="status">
        <div class="col-md-4">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select">
            <?php foreach(['new','contacted','meeting','negotiation','won','lost'] as $stt): ?>
              <option value="<?= e($stt) ?>" <?= $lead['status']===$stt?'selected':'' ?>><?= strtoupper($stt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Lost Category (optional)</label>
          <select name="lost_category_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach($lost_categories as $lc): ?>
              <option value="<?= (int)$lc['id'] ?>" <?= ((int)($lead['lost_category_id'] ?? 0) === (int)$lc['id']) ? 'selected' : '' ?>><?= e($lc['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Lost Reason (optional)</label>
          <input name="lost_reason" class="form-control" value="<?= e($lead['lost_reason'] ?? '') ?>" maxlength="255">
        </div>
        <div class="col-12">
          <button class="btn btn-primary">Save</button>
        </div>
      </form>

    </div>
  </div>

  <div class="col-lg-5">
    <div class="card p-3">
      <h5 class="mb-3">Countries</h5>
      <?php
        $sel = $pdo->prepare("SELECT country_id FROM lead_countries WHERE lead_id=?");
        $sel->execute([$id]);
        $selected_ids = array_map(fn($r) => (int)$r['country_id'], $sel->fetchAll());
      ?>
      <form method="post">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="countries">
        <label class="form-label small">Select Countries (multi-select)</label>
        <select name="country_ids[]" class="form-select" size="12" multiple>
          <?php foreach($all_countries as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $selected_ids, true) ? 'selected' : '' ?>>
              <?= e($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">Tip: Hold Ctrl (Windows) / Cmd (Mac) to select multiple countries.</div>
        <button class="btn btn-outline-primary btn-sm mt-2">Save Countries</button>
      </form>
    </div>
  </div>
</div>

<!-- Mobile quick actions -->
<div class="sv-actionbar d-md-none">
  <div class="container">
    <div class="d-flex gap-2 sv-toolbar">
      <a class="btn btn-outline-secondary btn-sm flex-fill" href="<?= url('employee/dashboard.php') ?>">Back</a>
      <a class="btn btn-outline-primary btn-sm flex-fill" href="#sv-contact-form">Contact</a>
      <a class="btn btn-primary btn-sm flex-fill" href="#sv-status-form">Status</a>
    </div>
  </div>
</div>

<script>
  // WhatsApp button auto-link from the mobile field (no DB changes).
  (function(){
    const input = document.getElementById('contact_mobile');
    const btn = document.getElementById('wa_btn');
    if (!input || !btn) return;

    function normalizeToWa(m){
      if (!m) return '';
      m = String(m).trim();
      if (!m) return '';
      m = m.replace(/[^0-9+]/g,'');
      if (m.startsWith('00')) m = '+' + m.substring(2);
      if (!m.startsWith('+')) {
        m = m.replace(/^0+/, '');
        m = '+20' + m;
      }
      const digits = m.replace(/\D/g,'');
      if (digits.length < 10) return '';
      return 'https://wa.me/' + digits;
    }

    function refresh(){
      const url = normalizeToWa(input.value);
      if (url){
        btn.href = url;
        btn.style.display = 'inline-flex';
      } else {
        btn.style.display = 'none';
      }
    }
    input.addEventListener('input', refresh);
    refresh();
  })();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>