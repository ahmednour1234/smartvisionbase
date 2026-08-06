<?php
// Backward-compatible entry point.
// V6.2.2+ uses role-based portals only.
require_once __DIR__ . '/lib/auth.php';

$u = current_user();
if (!$u) {
    header('Location: ' . url('login.php'));
    exit;
}

if (($u['role'] ?? '') === 'admin') {
    header('Location: ' . url('admin/leads.php'));
} else {
    header('Location: ' . url('employee/add_lead.php'));
}
exit;
