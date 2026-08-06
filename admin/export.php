<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$pdo = db();
$type = $_GET['type'] ?? 'performance';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="export_'.$type.'_'.date('Ymd_His').'.csv"');

$out = fopen('php://output', 'w');

$today = new DateTimeImmutable('today');
$weekStart = $today->modify('monday this week')->format('Y-m-d');
$weekEnd   = $today->modify('sunday this week')->format('Y-m-d');
$monthStart= $today->modify('first day of this month')->format('Y-m-d');
$monthEnd  = $today->modify('last day of this month')->format('Y-m-d');

if ($type === 'performance') {
  fputcsv($out, ['Employee','Email','Leads','Open','Won','Lost','Meetings_Week','Meetings_Month','KPI']);
  $users = $pdo->query("SELECT id, name, email FROM users WHERE role='staff' ORDER BY name")->fetchAll();
  foreach($users as $u) {
    $uid=(int)$u['id'];
    $st=$pdo->prepare("SELECT
      SUM(CASE WHEN status IN ('new','contacted','meeting','negotiation') THEN 1 ELSE 0 END) open_cnt,
      SUM(CASE WHEN status='won' THEN 1 ELSE 0 END) won_cnt,
      SUM(CASE WHEN status='lost' THEN 1 ELSE 0 END) lost_cnt,
      COUNT(*) total_cnt
      FROM leads WHERE sales_rep_id=?");
    $st->execute([$uid]);
    $c=$st->fetch();
    $mt=$pdo->prepare("SELECT COUNT(*) FROM meetings WHERE user_id=? AND meeting_date BETWEEN ? AND ?");
    $mt->execute([$uid,$weekStart,$weekEnd]);
    $mw=(int)$mt->fetchColumn();
    $mt->execute([$uid,$monthStart,$monthEnd]);
    $mm=(int)$mt->fetchColumn();
    $kpi='Behind'; if ($mw>=12 && $mw<=15) $kpi='On Track'; if ($mw>15) $kpi='Above Target';
    fputcsv($out, [$u['name'],$u['email'],(int)$c['total_cnt'],(int)$c['open_cnt'],(int)$c['won_cnt'],(int)$c['lost_cnt'],$mw,$mm,$kpi]);
  }
  exit;
}

if ($type === 'analytics') {
  fputcsv($out, ['Employee','Won','Lost']);
  $rows = $pdo->query("SELECT u.name,
    SUM(CASE WHEN l.status='won' THEN 1 ELSE 0 END) AS won_cnt,
    SUM(CASE WHEN l.status='lost' THEN 1 ELSE 0 END) AS lost_cnt
    FROM users u
    LEFT JOIN leads l ON l.sales_rep_id=u.id
    WHERE u.role='staff'
    GROUP BY u.id
    ORDER BY u.name")->fetchAll();
  foreach($rows as $r) fputcsv($out, [$r['name'], (int)$r['won_cnt'], (int)$r['lost_cnt']]);
  exit;
}

if ($type === 'lost_latest') {
  fputcsv($out, ['Company','Owner','Reason','Date']);
  $rows = $pdo->query("SELECT l.company_name, u.name AS owner_name, l.lost_reason, l.lost_at
    FROM leads l
    LEFT JOIN users u ON u.id=l.sales_rep_id
    WHERE l.status='lost'
    ORDER BY COALESCE(l.lost_at, l.updated_at, l.created_at) DESC
    LIMIT 500")->fetchAll();
  foreach($rows as $r) fputcsv($out, [$r['company_name'], ($r['owner_name']??'Free'), ($r['lost_reason']??''), ($r['lost_at']??'')]);
  exit;
}

fputcsv($out, ['Unsupported export type']);
exit;
