<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$pdo = db();

$today = new DateTimeImmutable('today');
$weekStart = $today->modify('monday this week')->format('Y-m-d');
$weekEnd   = $today->modify('sunday this week')->format('Y-m-d');
$monthStart= $today->modify('first day of this month')->format('Y-m-d');
$monthEnd  = $today->modify('last day of this month')->format('Y-m-d');

$users = $pdo->query("SELECT id, name, email, is_active FROM users WHERE role='staff' ORDER BY name")->fetchAll();

$rows = [];
foreach ($users as $u) {
  $uid = (int)$u['id'];

  $st = $pdo->prepare("SELECT
    SUM(CASE WHEN status IN ('new','contacted','meeting','negotiation') THEN 1 ELSE 0 END) AS open_cnt,
    SUM(CASE WHEN status='won' THEN 1 ELSE 0 END) AS won_cnt,
    SUM(CASE WHEN status='lost' THEN 1 ELSE 0 END) AS lost_cnt,
    COUNT(*) AS total_cnt
    FROM leads WHERE sales_rep_id=?");
  $st->execute([$uid]);
  $cnt = $st->fetch() ?: ['open_cnt'=>0,'won_cnt'=>0,'lost_cnt'=>0,'total_cnt'=>0];

  $st = $pdo->prepare("SELECT COUNT(*) FROM meetings WHERE user_id=? AND meeting_date BETWEEN ? AND ?");
  $st->execute([$uid, $weekStart, $weekEnd]);
  $mw = (int)$st->fetchColumn();

  $st->execute([$uid, $monthStart, $monthEnd]);
  $mm = (int)$st->fetchColumn();

  $kpi_min = 12; $kpi_max = 15;
  $kpi = 'Behind';
  if ($mw >= $kpi_min && $mw <= $kpi_max) $kpi = 'On Track';
  if ($mw > $kpi_max) $kpi = 'Above Target';

  $rows[] = [
    'id'=>$uid,'name'=>$u['name'],'email'=>$u['email'],'is_active'=>$u['is_active'],
    'total'=>$cnt['total_cnt'],'open'=>$cnt['open_cnt'],'won'=>$cnt['won_cnt'],'lost'=>$cnt['lost_cnt'],
    'meet_week'=>$mw,'meet_month'=>$mm,'kpi'=>$kpi
  ];
}

$title = APP_NAME . ' | Performance Report';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Performance Report</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/export.php?type=performance') ?>">Export CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/index.php') ?>">Back</a>
  </div>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Employee</th>
          <th>Leads</th>
          <th>Won</th>
          <th>Lost</th>
          <th>Open</th>
          <th>Meetings (Week)</th>
          <th>KPI</th>
          <th>Meetings (Month)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td>
              <?= e($r['name']) ?>
              <?php if (!(int)$r['is_active']): ?><span class="badge bg-secondary ms-1">Inactive</span><?php endif; ?>
              <div class="small text-muted"><?= e($r['email']) ?></div>
            </td>
            <td><?= (int)$r['total'] ?></td>
            <td><?= (int)$r['won'] ?></td>
            <td><?= (int)$r['lost'] ?></td>
            <td><?= (int)$r['open'] ?></td>
            <td><?= (int)$r['meet_week'] ?></td>
            <td>
              <?php
                $cls = 'secondary';
                if ($r['kpi']==='On Track' || $r['kpi']==='Above Target') $cls='success';
                if ($r['kpi']==='Behind') $cls='danger';
              ?>
              <span class="badge bg-<?= $cls ?>"><?= e($r['kpi']) ?></span>
            </td>
            <td><?= (int)$r['meet_month'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="small text-muted mt-2">Weekly KPI target: 12–15 meetings per salesperson.</div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
