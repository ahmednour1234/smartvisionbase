<?php
declare(strict_types=1);

/**
 * Daily Follow-up Emails (10:00 Cairo)
 * - Sends to each staff user: today's follow-ups (next_followup = today) for their My Leads
 * - BCC all admins on each email
 *
 * No DB changes.
 *
 * Cron example (server time set to Africa/Cairo):
 *   0 10 * * * /usr/bin/php /path/to/crm/cron/daily_followups.php >/dev/null 2>&1
 *
 * If server timezone is not Cairo:
 *   0 8 * * * /usr/bin/php ... (adjust accordingly)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/smtp_mailer.php';
$pdo = db();
$jobId = null;

try {
    $jr = $pdo->prepare("INSERT INTO job_runs (job_name, started_at, status) VALUES (:name, NOW(), 'running')");
    $jr->execute([':name' => 'daily_followups']);
    $jobId = (int)$pdo->lastInsertId();
} catch (Exception $e) {
    // If job_runs does not exist yet, cron must still work.
}

try {

date_default_timezone_set('Africa/Cairo');

$pdo = db();
$today = cairo_today();
$smtp = smtp_config();

// Admin emails
$admins = $pdo->query("SELECT email FROM users WHERE is_active=1 AND role='admin' AND email IS NOT NULL AND email<>''")->fetchAll(PDO::FETCH_COLUMN);
$admins = array_values(array_unique(array_map('trim', $admins)));

$staff = $pdo->query("SELECT id, name, email FROM users WHERE is_active=1 AND role='staff' AND email IS NOT NULL AND email<>'' ORDER BY name")->fetchAll();

$leadStmt = $pdo->prepare("SELECT id, company_name, contact_person, contact_mobile, contact_email, contact_linkedin, company_website, status
                           FROM leads
                           WHERE sales_rep_id = ? AND DATE(next_followup) = ? AND status NOT IN ('won','lost')
                           ORDER BY company_name");

$sent = 0;
foreach ($staff as $u) {
    $leadStmt->execute([(int)$u['id'], $today]);
    $leads = $leadStmt->fetchAll();
    if (!$leads) continue;

    $rows = '';
    foreach ($leads as $l) {
        $wa = whatsapp_chat_url($l['contact_mobile'] ?? '');
        $waBtn = $wa ? "<a href=\"".e($wa)."\" style=\"text-decoration:none;\">WhatsApp</a>" : '-';
        $rows .= "<tr>"
              .  "<td style='padding:8px;border:1px solid #ddd;'><b>".e($l['company_name'])."</b></td>"
              .  "<td style='padding:8px;border:1px solid #ddd;'>".e($l['contact_person'] ?? '')."</td>"
              .  "<td style='padding:8px;border:1px solid #ddd;'>".e($l['contact_mobile'] ?? '')."</td>"
              .  "<td style='padding:8px;border:1px solid #ddd;'>".$waBtn."</td>"
              .  "<td style='padding:8px;border:1px solid #ddd;'>".e($l['contact_email'] ?? '')."</td>"
              .  "</tr>";
    }

    $subject = "Today's Follow-ups (" . $today . ") - " . APP_NAME;
    $html = "<div style='font-family:Arial,sans-serif;font-size:14px;'>"
          . "<p>Hi " . e($u['name']) . ",</p>"
          . "<p>Here are your follow-ups scheduled for <b>" . e($today) . "</b>.</p>"
          . "<table style='border-collapse:collapse;width:100%;'>"
          . "<thead><tr>"
          . "<th style='padding:8px;border:1px solid #ddd;background:#f5f5f5;text-align:left;'>Company</th>"
          . "<th style='padding:8px;border:1px solid #ddd;background:#f5f5f5;text-align:left;'>Contact</th>"
          . "<th style='padding:8px;border:1px solid #ddd;background:#f5f5f5;text-align:left;'>Mobile</th>"
          . "<th style='padding:8px;border:1px solid #ddd;background:#f5f5f5;text-align:left;'>WhatsApp</th>"
          . "<th style='padding:8px;border:1px solid #ddd;background:#f5f5f5;text-align:left;'>Email</th>"
          . "</tr></thead><tbody>" . $rows . "</tbody></table>"
          . "<p style='margin-top:14px;color:#666;'>Sent automatically by " . e(APP_NAME) . ".</p>"
          . "</div>";

    $ok = smtp_send((string)$u['email'], $subject, $html, [], $admins, [
        'host' => $smtp['host'],
        'port' => $smtp['port'],
        'user' => $smtp['user'],
        'pass' => $smtp['pass'],
        'secure' => $smtp['secure'],
        'from_email' => $smtp['from_email'] ?? MAIL_FROM,
        'from_name' => $smtp['from_name'] ?? MAIL_FROM_NAME,
    ]);
    if ($ok) $sent++;
}

// Optional: exit code

  if ($jobId) {
      $msg = 'Sent ' . (int)$sent . ' email(s).';
      $upd = $pdo->prepare("UPDATE job_runs SET finished_at=NOW(), status='success', message=:m WHERE id=:id");
      $upd->execute([':m' => substr($msg, 0, 255), ':id' => $jobId]);
  }
} catch (Exception $e) {
  if (isset($pdo) && $jobId) {
      try {
          $upd = $pdo->prepare("UPDATE job_runs SET finished_at=NOW(), status='failed', message=:m WHERE id=:id");
          $upd->execute([':m' => substr($e->getMessage(), 0, 255), ':id' => $jobId]);
      } catch (Exception $ignore) {}
  }
  throw $e;
}