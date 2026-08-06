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
const DB_NAME = 'u552947370_crm2';
const DB_USER = 'u552947370_crm2';
const DB_PASS = 'EjvGJjb&$!1k';

/* ====== APP ====== */
const APP_NAME = 'Smart Vision CRM';
const BASE_URL = ''; // example: '/crm' if hosted in subfolder, else keep ''.

// Email identity
const MAIL_FROM = 'info@smartvisioneg.com';
const MAIL_FROM_NAME = 'Smart Vision CRM';

/* ====== EMAIL (Daily Follow-ups) ======
   No DB changes required.
   Best practice: set SMTP credentials via environment variables on the server.
   Required env vars:
     SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS
   Optional:
     SMTP_SECURE (ssl|tls), SMTP_FROM_EMAIL, SMTP_FROM_NAME, DAILY_FOLLOWUP_HOUR
*/
function env(string $k, ?string $default=null): ?string {
    $v = getenv($k);
    if ($v === false || $v === '') return $default;
    return $v;
}

function smtp_config(): array {
    return [
        'host' => env('SMTP_HOST', 'mail.smartvisioneg.com'),
        'port' => (int)(env('SMTP_PORT', '465') ?? 465),
        'user' => env('SMTP_USER', 'info@smartvisioneg.com'),
        'pass' => env('SMTP_PASS', ''),
        'secure' => env('SMTP_SECURE', 'ssl'),
        'from_email' => env('SMTP_FROM_EMAIL', 'info@smartvisioneg.com'),
        'from_name' => env('SMTP_FROM_NAME', APP_NAME),
    ];
}

function cairo_today(): string {
    $tz = new DateTimeZone('Africa/Cairo');
    return (new DateTime('now', $tz))->format('Y-m-d');
}

function whatsapp_chat_url(?string $mobile): ?string {
    $m = trim((string)$mobile);
    if ($m === '') return null;
    // Keep digits only, allow leading +
    $m = preg_replace('/[^0-9+]/', '', $m);
    if ($m === '') return null;
    // Convert 00 prefix to +
    if (str_starts_with($m, '00')) $m = '+' . substr($m, 2);
    // If no country code provided, assume Egypt (+20)
    if ($m[0] !== '+') {
        $m = ltrim($m, '0');
        $m = '+20' . $m;
    }
    $digits = preg_replace('/\D/', '', $m);
    if (strlen($digits) < 10) return null;
    return 'https://wa.me/' . $digits;
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
    // If hosted in a subfolder, set BASE_URL to '/subfolder'.
    // If BASE_URL is empty, auto-detect a reasonable base from SCRIPT_NAME.
    $base = BASE_URL;
    if ($base === '') {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));
        $dir = rtrim($dir, '/');
        // dirname('/') returns '/', which we normalize to ''.
        if ($dir === '/' || $dir === '.') {
            $dir = '';
        }
        $base = $dir;
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
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
        include __DIR__ . '/403.php';
        exit;
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
