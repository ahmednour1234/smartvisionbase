<?php
require_once __DIR__ . '/lib/auth.php';
$u = current_user();
if ($u) {
  if (($u['role'] ?? '') === 'admin') {
    header('Location: ' . url('admin/index.php'));
  } else {
    header('Location: ' . url('employee/dashboard.php'));
  }
} else {
  header('Location: ' . url('login.php'));
}
exit;
