<?php
require_once __DIR__ . '/config.php';

function login_user_by_email_password(string $email, string $password): bool {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role, is_active
                           FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch();

    if ($u && (int)$u['is_active'] === 1 && password_verify($password, $u['password_hash'])) {
        unset($u['password_hash'], $u['is_active']);
        $_SESSION['user'] = $u;
        audit('login', 'user', (int)$u['id']);
        return true;
    }
    audit('login_failed', 'user', null, ['email' => $email]);
    return false;
}
