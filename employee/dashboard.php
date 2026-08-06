<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();

$pdo = db();
$user = current_user();
if ($user['role'] !== 'staff' && $user['role'] !== 'admin') { http_response_code(403); exit('Access denied'); }

$meId = (int)$user['id'];

// Flash messages
$flash = '';
$m = $_GET['m'] ?? '';
if ($m === 'lead_added') $flash = 'Lead saved successfully.';
if ($m === 'meeting_added') $flash = 'Meeting logged successfully.';

// KPI windows
$today = new DateTimeImmutable('today');
$weekStart = $today->modify('monday this week')->format('Y-m-d');
$weekEnd   = $today->modify('sunday this week')->format('Y-m-d');
$monthStart= $today->modify('first day of this month')->format('Y-m-d');
$monthEnd  = $today->modify('last day of this month')->format('Y-m-d');

$st = $pdo->prepare("SELECT
  SUM(CASE WHEN status IN ('new','contacted','meeting','negotiation') THEN 1 ELSE 0 END) AS open_cnt,
  SUM(CASE WHEN status='won' THEN 1 ELSE 0 END) AS won_cnt,
  SUM(CASE WHEN status='lost' THEN 1 ELSE 0 END) AS lost_cnt,
  COUNT(*) AS total_cnt
  FROM leads WHERE sales_rep_id=:me");
$st->execute([':me' => $meId]);
$cnt = $st->fetch() ?: ['open_cnt'=>0,'won_cnt'=>0,'lost_cnt'=>0,'total_cnt'=>0];

$st = $pdo->prepare("SELECT COUNT(*) c FROM meetings WHERE user_id=:me AND meeting_date BETWEEN :a AND :b");
$st->execute([':me'=>$meId, ':a'=>$weekStart, ':b'=>$weekEnd]);
$meet_week = (int)$st->fetchColumn();

$st->execute([':me'=>$meId, ':a'=>$monthStart, ':b'=>$monthEnd]);
$meet_month = (int)$st->fetchColumn();

// KPI status
$kpi_min = 12;
$kpi_max = 15;
$kpi_status = 'Behind';
$kpi_class = 'danger';
if ($meet_week >= $kpi_min && $meet_week <= $kpi_max) { $kpi_status='On Track'; $kpi_class='success'; }
if ($meet_week > $kpi_max) { $kpi_status='Above Target'; $kpi_class='success'; }

// Lost alerts (last 5)
$lost = $pdo->prepare("SELECT id, company_name, lost_reason, lost_at
                       FROM leads
                       WHERE sales_rep_id=:me AND status='lost'
                       ORDER BY COALESCE(lost_at, updated_at, created_at) DESC
                       LIMIT 5");
$lost->execute([':me'=>$meId]);
$lost_rows = $lost->fetchAll();

// My leads list (filter)
$filter_status = trim((string)($_GET['status'] ?? ''));
$filter_pkg = trim((string)($_GET['package_id'] ?? ''));
$filter_country = trim((string)($_GET['country_id'] ?? ''));

$packages = $pdo->query("SELECT id, name FROM packages WHERE is_active=1 ORDER BY sort_order, name")->fetchAll();
$countries = fetch_active_countries($pdo);

$where = "WHERE l.sales_rep_id = :me";
$params = [':me' => $meId];

if ($filter_status !== '') { $where .= " AND l.status = :st"; $params[':st'] = $filter_status; }
if ($filter_pkg !== '') { $where .= " AND l.interested_package_id = :pkg"; $params[':pkg'] = (int)$filter_pkg; }
if ($filter_country !== '') {
  $where .= " AND EXISTS (SELECT 1 FROM lead_countries lc WHERE lc.lead_id=l.id AND lc.country_id=:cid)";
  $params[':cid'] = (int)$filter_country;
}

$stmt = $pdo->prepare("SELECT l.*, p.name AS package_name
                       FROM leads l
                       LEFT JOIN packages p ON l.interested_package_id=p.id
                       $where
                       ORDER BY COALESCE(l.next_followup,'2999-12-31') ASC, l.company_name ASC
                       LIMIT 200");
$stmt->execute($params);
$leads = $stmt->fetchAll();

// For meeting form dropdown
$myLeadsMini = $pdo->prepare("SELECT id, company_name FROM leads WHERE sales_rep_id=:me ORDER BY company_name ASC LIMIT 500");
$myLeadsMini->execute([':me'=>$meId]);
$myLeadsMini = $myLeadsMini->fetchAll();

$title = APP_NAME . ' | Employee Dashboard';
include __DIR__ . '/../partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Employee Dashboard</h3>
  <div class="d-flex gap-2 sv-toolbar">
    <a class="btn btn-outline-primary btn-sm" href="<?= url('employee/add_lead.php') ?>">+ Add Lead</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('employee/import.php') ?>">Import CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('employee/search.php') ?>">Search</a>
  </div>
</div>

<?php if ($flash): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>

<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card p-3">
      <div class="text-muted">My Leads</div>
      <div class="fs-4 fw-bold"><?= (int)$cnt['total_cnt'] ?></div>
      <div class="small text-muted">Open: <?= (int)$cnt['open_cnt'] ?> | Won: <?= (int)$cnt['won_cnt'] ?> | Lost: <?= (int)$cnt['lost_cnt'] ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <div class="text-muted">Meetings (This Week)</div>
      <div class="fs-4 fw-bold"><?= $meet_week ?></div>
      <div class="small">
        Target: <?= $kpi_min ?>–<?= $kpi_max ?> |
        <span class="badge bg-<?= $kpi_class ?>"><?= e($kpi_status) ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <div class="text-muted">Meetings (This Month)</div>
      <div class="fs-4 fw-bold"><?= $meet_month ?></div>
      <div class="small text-muted"><?= e($monthStart) ?> to <?= e($monthEnd) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <div class="text-muted">Lost Alerts</div>
      <div class="small text-muted">Last 5 lost leads</div>
      <div class="mt-2">
        <?php if (!$lost_rows): ?>
          <span class="text-muted small">No lost leads.</span>
        <?php else: ?>
          <?php foreach($lost_rows as $lr): ?>
            <div class="small">
              <a href="<?= url('employee/lead.php?id='.(int)$lr['id']) ?>"><?= e($lr['company_name']) ?></a>
              <?php if ($lr['lost_reason']): ?>
                <span class="text-muted">— <?= e($lr['lost_reason']) ?></span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3">Quick Meeting Log</h5>
      <form method="post" action="<?= url('employee/add_meeting.php') ?>" class="row g-2">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?= csrf_token() ?>">
        <div class="col-md-4">
          <label class="form-label small">Date</label>
          <input type="date" name="meeting_date" class="form-control" value="<?= e(date('Y-m-d')) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Type</label>
          <select name="meeting_type" class="form-select">
            <option value="call">Call</option>
            <option value="online">Online</option>
            <option value="in_person">In-person</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Duration (min)</label>
          <input type="number" name="duration_minutes" class="form-control" min="0" max="600" value="10">
        </div>
        <div class="col-12">
          <label class="form-label small">Lead (optional)</label>
          <select name="lead_id" class="form-select">
            <option value="">-- Not linked to a lead --</option>
            <?php foreach($myLeadsMini as $ml): ?>
              <option value="<?= (int)$ml['id'] ?>"><?= e($ml['company_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label small">Notes (optional)</label>
          <input name="notes" class="form-control" maxlength="255" placeholder="Short note...">
        </div>
        <div class="col-12">
          <button class="btn btn-primary">Save Meeting</button>
        </div>
      </form>
      <div class="form-text mt-2">Weekly KPI target: 12–15 meetings.</div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3">My Leads (Filters)</h5>
      <form class="row g-2">
        <div class="col-md-4">
          <select name="status" class="form-select">
            <option value="">All Status</option>
            <?php foreach(['new','contacted','meeting','negotiation','won','lost'] as $stt): ?>
              <option value="<?= e($stt) ?>" <?= $filter_status===$stt?'selected':'' ?>><?= strtoupper($stt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <select name="package_id" class="form-select">
            <option value="">All Packages</option>
            <?php foreach($packages as $p): ?>
              <option value="<?= (int)$p['id'] ?>" <?= $filter_pkg==(string)$p['id']?'selected':'' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <select name="country_id" class="form-select">
            <option value="">All Countries</option>
            <?php foreach($countries as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= $filter_country==(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <button class="btn btn-outline-secondary btn-sm">Apply</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="card p-3">
  <h5 class="mb-3">My Leads</h5>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Company</th>
          <th>Status</th>
          <th>Package</th>
          <th>Next Follow-up</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$leads): ?>
          <tr><td colspan="5" class="text-muted">No leads found.</td></tr>
        <?php else: ?>
          <?php foreach($leads as $l): ?>
            <tr>
              <td class="fw-semibold"><?= e($l['company_name']) ?></td>
              <td><span class="badge bg-secondary"><?= e(strtoupper($l['status'])) ?></span></td>
              <td><?= e($l['package_name'] ?? '') ?></td>
              <td><?= e($l['next_followup'] ?: '') ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= url('employee/lead.php?id='.(int)$l['id']) ?>">Open</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
