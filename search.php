<?php
// Backward-compatible entry point.
// Redirects to the unified portals to avoid duplicate UIs.
require_once __DIR__ . '/lib/auth.php';

$u = current_user();
if (!$u) {
    header('Location: ' . url('login.php'));
    exit;
}

$q = $_GET['q'] ?? '';
$q = is_string($q) ? $q : '';
$qs = $q !== '' ? ('?q=' . urlencode($q)) : '';

if (($u['role'] ?? '') === 'admin') {
    header('Location: ' . url('admin/leads.php') . $qs);
} else {
    header('Location: ' . url('employee/search.php') . $qs);
}
exit;
