<?php
declare(strict_types=1);

session_start();

/**
 * Smart Vision CRM V6.3 (Upload Ready)
 * - Admin Panel: Users, Countries, Packages
 * - Employee Portal: Search, Add Lead, Dashboard
 * - Strict policy: Staff can add lead as "My Lead" or "Free" only; assignment to others is Admin-only.
 */

/* ====== CONFIG: EDIT DB CREDENTIALS ====== */
const DB_HOST = 'localhost';
const DB_NAME = 'smartvision_crm';
const DB_USER = 'root';
const DB_PASS = '';

/* ====== APP ====== */
const APP_NAME = 'Smart Vision CRM';
const BASE_URL = ''; // example: '/crm' if hosted in subfolder, else keep ''.

/* ====== COUNTRIES DROPDOWN MODE (CODE-ONLY, NO DB CHANGES) ======
 * Goal: show Middle East countries only in dropdowns (Add/Edit Lead + Filters)
 * without touching MySQL data.
 */
const COUNTRIES_MODE = 'MIDDLE_EAST'; // 'MIDDLE_EAST' | 'ALL'

/**
 * Returns allowed country ISO2 codes for dropdowns.
 * You can safely edit this list anytime (code-only).
 */
function allowed_country_iso2s(): array {
    if (defined('COUNTRIES_MODE') && COUNTRIES_MODE === 'ALL') {
        return [];
    }

    // Middle East (GCC + Levant + Iraq + Iran + Turkey + Yemen + Palestine)
    return [
        'AE','SA','QA','KW','BH','OM',
        'JO','LB','SY','IQ','YE','PS',
        'TR','IR','IL'
    ];
}

/**
 * Fetch active countries applying the allow-list (if any).
 * Returns rows: iso2, name
 */
function fetch_active_countries(PDO $pdo): array {
    $allow = allowed_country_iso2s();
    if (empty($allow)) {
        $st = $pdo->query("SELECT iso2, name FROM countries WHERE is_active = 1 ORDER BY name");
        return $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    $placeholders = implode(',', array_fill(0, count($allow), '?'));
    $sql = "SELECT iso2, name FROM countries WHERE is_active = 1 AND iso2 IN ($placeholders) ORDER BY name";
    $st = $pdo->prepare($sql);
    $st->execute($allow);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}


/* ====== SECURITY ====== */
const CSRF_KEY = 'csrf_token_v3';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function csrf_token(): string {
    if (empty($_SESSION[CSRF_KEY])) {
        $_SESSION[CSRF_KEY] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION[CSRF_KEY];
}

function csrf_verify(): void {
    // Accept multiple field names for backward/forward compatibility.
    // - Preferred: CSRF_KEY (e.g., csrf_token_v3)
    // - Legacy: 'csrf'
    // - Installer/older forms: '_csrf'
    $token = (string)($_POST[CSRF_KEY] ?? ($_POST['csrf'] ?? ($_POST['_csrf'] ?? '')));
    if ($token === '' || !hash_equals((string)($_SESSION[CSRF_KEY] ?? ''), $token)) {
        http_response_code(403);
        exit('CSRF verification failed.');
    }
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: ' . url('login.php'));
        exit;
    }
}

function require_admin(): void {
    require_login();
    if ((current_user()['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Forbidden: Admins only.');
    }
}

function audit(string $action, string $entity, ?int $entityId = null, ?array $meta = null): void {
    try {
        $pdo = db();
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, entity, entity_id, meta, ip)
                               VALUES (:uid, :action, :entity, :eid, :meta, :ip)");
        $stmt->execute([
            ':uid' => current_user()['id'] ?? null,
            ':action' => $action,
            ':entity' => $entity,
            ':eid' => $entityId,
            ':meta' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (Throwable $t) {
        // Do not block core flow on audit failures.
    }
}

function normalize_company(string $name): string {
    $name = trim(preg_replace('/\s+/', ' ', $name));
    return $name;
}

function flash_set(string $key, string $val): void {
    $_SESSION['flash'][$key] = $val;
}

function flash_get(string $key): ?string {
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}
