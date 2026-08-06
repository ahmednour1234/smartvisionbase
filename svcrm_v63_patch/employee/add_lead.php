<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

require_login();
$u = current_user();
if ($u['role'] !== 'staff' && $u['role'] !== 'admin') { http_response_code(403); exit('Access denied'); }

$pdo = db();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();

  $company = trim((string)($_POST['company_name'] ?? ''));
  $contact = trim((string)($_POST['contact_person'] ?? ''));
  $mobile  = trim((string)($_POST['contact_mobile'] ?? ''));
  $email   = trim((string)($_POST['contact_email'] ?? ''));
  $linkedin= trim((string)($_POST['contact_linkedin'] ?? ''));
  $website = trim((string)($_POST['company_website'] ?? ''));

  $event_id = (int)($_POST['event_id'] ?? 0);
  $event_id = $event_id > 0 ? $event_id : null;

  $expected_value = trim((string)($_POST['expected_value'] ?? ''));
  $expected_value = ($expected_value !== '' && is_numeric($expected_value)) ? (float)$expected_value : null;

  $currency = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));
  if ($currency === '') { $currency = 'USD'; }
  $currency = substr($currency, 0, 3);

  $probability = trim((string)($_POST['probability'] ?? ''));
  $probability = ($probability !== '' && is_numeric($probability)) ? (int)$probability : null;
  if ($probability !== null) { $probability = max(0, min(100, $probability)); }

  $expected_close = $_POST['expected_close_date'] ?? null;
  $lead_notes = trim((string)($_POST['lead_notes'] ?? ''));

  $pkg_id  = (int)($_POST['package_id'] ?? 0);
  $pkg_id  = $pkg_id > 0 ? $pkg_id : null;

  $next_follow = $_POST['next_followup'] ?? null;
  $last_meet   = $_POST['last_meeting'] ?? null;

  $country_ids = $_POST['country_ids'] ?? [];
  if (!is_array($country_ids)) $country_ids = [];

  // Strict policy:
  // - Staff can add lead either assigned to himself or Free (no assigning to colleagues).
  $make_free = isset($_POST['make_free']) ? 1 : 0;
  $owner_id = null;
  if ($u['role'] === 'admin') {
    // Admin can choose owner from dropdown (optional)
    $owner_id = (int)($_POST['sales_rep_id'] ?? 0);
    $owner_id = $owner_id > 0 ? $owner_id : null;
  } else {
    $owner_id = $make_free ? null : (int)$u['id'];
  }

  if ($company === '') {
    $msg = 'Company name is required.';
  } else {
    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("INSERT INTO leads
        (company_name, contact_person, contact_mobile, contact_email, contact_linkedin, company_website,
         event_id, interested_package_id, expected_value, currency, probability, expected_close_date, lead_notes,
         status, sales_rep_id, last_meeting, next_followup, created_by, updated_by)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->execute([
        $company,
        ($contact ?: null),
        ($mobile ?: null),
        ($email ?: null),
        ($linkedin ?: null),
        ($website ?: null),
        $event_id,
        $pkg_id,
        $expected_value,
        $currency,
        $probability,
        ($expected_close ?: null),
        ($lead_notes ?: null),
        'new',
        $owner_id,
        ($last_meet ?: null),
        ($next_follow ?: null),
        (int)$u['id'],
        (int)$u['id'],
      ]);

      $lead_id = (int)$pdo->lastInsertId();

      // Countries relation
      if (count($country_ids)) {
        $ins = $pdo->prepare("INSERT IGNORE INTO lead_countries (lead_id, country_id) VALUES (?,?)");
        foreach ($country_ids as $cid) {
          $cid = (int)$cid;
          if ($cid > 0) $ins->execute([$lead_id, $cid]);
        }
      }

      audit('lead_create', 'lead', $lead_id, ['company' => $company, 'owner_id' => $owner_id]);
      $pdo->commit();
      header('Location: ' . url('employee/dashboard.php?m=lead_added'));
      exit;

    } catch (Exception $e) {
      $pdo->rollBack();

      // Duplicate company (unique key)
      if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'uq_company') !== false) {
        $msg = 'This company already exists in the database.';
      } else {
        $msg = 'Error: ' . $e->getMessage();
      }
    }
  }
}

// Dropdown lists
$events = $pdo->query("SELECT id, name FROM events WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$packages = $pdo->query("SELECT id, name FROM packages WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$allowedIso2 = allowed_country_iso2s();
if (!empty($allowedIso2)) {
  $ph = implode(',', array_fill(0, count($allowedIso2), '?'));
  $stC = $pdo->prepare("SELECT id, name FROM countries WHERE is_active=1 AND iso2 IN ($ph) ORDER BY sort_order, name");
  $stC->execute($allowedIso2);
  $countries = $stC->fetchAll();
} else {
  $countries = $pdo->query("SELECT id, name FROM countries WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
}

// Admin can assign; staff cannot
$staff = [];
if ($u['role'] === 'admin') {
  $staff = $pdo->query("SELECT id, name FROM users WHERE role='staff' AND is_active=1 ORDER BY name")->fetchAll();
}

$title = APP_NAME . ' | Add Lead';
include __DIR__ . '/../partials/header.php';
?>
<div class="row">
  <div class="col-lg-8">
    <div class="card p-4">
      <h4 class="mb-3">Add Company</h4>
      <?php if ($msg): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

      <form method="post" class="row g-3">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">

        <div class="col-md-12">
          <label class="form-label">Company Name *</label>
          <input name="company_name" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Contact Person (optional)</label>
          <input name="contact_person" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Mobile (optional)</label>
          <input name="contact_mobile" class="form-control">
        </div>

        <div class="col-md-6">
          <label class="form-label">Email (optional)</label>
          <input name="contact_email" type="email" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">LinkedIn (optional)</label>
          <input name="contact_linkedin" class="form-control" placeholder="https://linkedin.com/...">
        </div>

        <div class="col-md-12">
          <label class="form-label">Company Website (optional)</label>
          <input name="company_website" class="form-control" placeholder="https://...">
        </div>

        
        <div class="col-md-6">
          <label class="form-label">Event</label>
          <select name="event_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach($events as $ev): ?>
              <option value="<?= (int)$ev['id'] ?>"><?= e($ev['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>


        <div class="col-md-4">
          <label class="form-label">Expected Value</label>
          <input name="expected_value" class="form-control" placeholder="e.g., 5000">
        </div>

        <div class="col-md-2">
          <label class="form-label">Currency</label>
          <input name="currency" class="form-control" value="USD" placeholder="USD">
        </div>

        <div class="col-md-2">
          <label class="form-label">Probability %</label>
          <input name="probability" class="form-control" placeholder="0-100">
        </div>

        <div class="col-md-4">
          <label class="form-label">Expected Close Date</label>
          <input type="date" name="expected_close_date" class="form-control">
        </div>

        <div class="col-md-12">
          <label class="form-label">Lead Notes (optional)</label>
          <textarea name="lead_notes" class="form-control" rows="3" placeholder="Notes..."></textarea>
        </div>


<div class="col-md-6">
          <label class="form-label">Package</label>
          <select name="package_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach($packages as $p): ?>
              <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Countries (multi-select)</label>
          <select name="country_ids[]" class="form-select" multiple size="6">
            <?php foreach($countries as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Hold Ctrl (Windows) / Cmd (Mac) to select multiple.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Last Meeting</label>
          <input type="date" name="last_meeting" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Next Follow-up</label>
          <input type="date" name="next_followup" class="form-control">
        </div>

        <?php if ($u['role'] === 'admin'): ?>
          <div class="col-md-12">
            <label class="form-label">Assign To (optional)</label>
            <select name="sales_rep_id" class="form-select">
              <option value="">-- Free Lead (available to all) --</option>
              <?php if ($u['role'] === 'admin'): ?>
                <option value="<?= (int)$u['id'] ?>">-- Assign to me (Admin) --</option>
              <?php endif; ?>
              <?php foreach($staff as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="make_free" name="make_free">
              <label class="form-check-label" for="make_free">
                Keep as Free Lead (otherwise it will be added to My Leads)
              </label>
            </div>
            <div class="form-text">Strict policy: You cannot assign leads to colleagues. Admin only.</div>
          </div>
        <?php endif; ?>

        <div class="col-12">
          <button class="btn btn-primary">Save</button>
          <a class="btn btn-outline-secondary" href="<?= url('employee/dashboard.php') ?>">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
