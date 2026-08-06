<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

function login_user(array $user): void {
    unset($user['password_hash'], $user['is_active'], $user['must_change_password']);
    $_SESSION['user'] = $user;
}

function logout_user(): void {
    session_destroy();
}

function is_admin(): bool {
    return (current_user()['role'] ?? '') === 'admin';
}

function is_staff(): bool {
    return (current_user()['role'] ?? '') === 'staff';
}
