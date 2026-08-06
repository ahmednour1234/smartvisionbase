<?php
/**
 * CSRF helper.
 *
 * IMPORTANT:
 * This CRM already defines CSRF helpers in config.php (loaded through lib/db.php).
 * Some pages include both config.php and this file.
 * To prevent fatal "Cannot redeclare" errors (white pages), all declarations are guarded.
 */

require_once __DIR__ . '/db.php';

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_check')) {
    /**
     * Legacy CSRF validator for forms that post _csrf.
     * Prefer csrf_verify() from config.php when available.
     */
    function csrf_check(): void
    {
        if (!function_exists('is_post') || !is_post()) {
            return;
        }
        $token = $_POST['_csrf'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }
    }
}
