<?php
// Backward-compat helper (V6.1)
// Some legacy utility pages used lib/db.php. In V6.1 we unify everything through root config.php.

require_once __DIR__ . '/../config.php';

if (!function_exists('is_post')) {
  function is_post(): bool { return (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'); }
}

if (!function_exists('redirect')) {
  function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
  }
}
