<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/auth.php';
if (current_user()) {
    audit('logout', 'user', (int)(current_user()['id'] ?? 0));
}
logout_user();
header('Location: ' . url('login.php'));
exit;
