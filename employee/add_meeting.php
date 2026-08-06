<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();
$u = current_user();
if ($u['role'] !== 'staff') { http_response_code(403); exit('Access denied'); }

csrf_verify();
$pdo = db();

$meeting_date = $_POST['meeting_date'] ?? date('Y-m-d');
$duration = (int)($_POST['duration_minutes'] ?? 0);
$type = $_POST['meeting_type'] ?? 'call';
$notes = trim((string)($_POST['notes'] ?? ''));
$lead_id = (int)($_POST['lead_id'] ?? 0);
$lead_id = $lead_id > 0 ? $lead_id : null;

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $meeting_date)) { http_response_code(400); exit('Invalid date'); }
if ($duration < 0 || $duration > 600) $duration = 0;
if (!in_array($type, ['call','online','in_person'], true)) $type='call';

// If lead selected, ensure it belongs to user
if ($lead_id !== null) {
  $st = $pdo->prepare("SELECT sales_rep_id FROM leads WHERE id=?");
  $st->execute([$lead_id]);
  $row = $st->fetch();
  if (!$row) { http_response_code(404); exit('Lead not found'); }
  if ((int)$row['sales_rep_id'] !== (int)$u['id']) { http_response_code(403); exit('You can only log meetings for your leads'); }
}

$ins = $pdo->prepare("INSERT INTO meetings (user_id, lead_id, meeting_date, duration_minutes, meeting_type, notes) VALUES (?,?,?,?,?,?)");
$ins->execute([(int)$u['id'], $lead_id, $meeting_date, $duration, $type, ($notes ?: null)]);
audit('meeting_create', 'meeting', (int)$pdo->lastInsertId(), ['lead_id' => $lead_id]);

header('Location: ' . url('employee/dashboard.php?m=meeting_added'));
exit;
