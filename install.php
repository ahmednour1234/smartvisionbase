<?php
// Smart Vision CRM V6.3 - Installer
// WARNING: Delete this file after setup.

require_once __DIR__ . '/config.php';

$title = "Install | " . APP_NAME;
$msg = '';
$pdo = null;

try {
    $pdo = db();
} catch (Throwable $e) {
    $msg = '<div class="alert alert-danger">DB connection failed. Please update <code>config.php</code> first.</div>';
}

function run_sql_file(PDO $pdo, string $path): void {
    $sql = file_get_contents($path);
    if ($sql === false) return;

    // naive split (works because our SQL files don't contain delimiter changes)
    $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
    foreach ($stmts as $s) {
        if ($s === '' || str_starts_with(ltrim($s), '--')) continue;
        $pdo->exec($s);
    }
}

if ($pdo && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'run_sql') {
        try {
            run_sql_file($pdo, __DIR__ . '/schema.sql');
            // schema.sql already includes countries insert, but we keep seed file call for safety (it is idempotent if written that way).
            if (file_exists(__DIR__ . '/seed_countries.sql')) {
                run_sql_file($pdo, __DIR__ . '/seed_countries.sql');
            }
            $msg = '<div class="alert alert-success">Database schema executed successfully.</div>';
        } catch (Throwable $e) {
            $msg = '<div class="alert alert-danger">Error running SQL: ' . e($e->getMessage()) . '</div>';
        }
    }

    if ($action === 'create_admin') {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $pass = (string)($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $pass === '') {
            $msg = '<div class="alert alert-danger">Name, email, and password are required.</div>';
        } elseif (strlen($pass) < 10) {
            $msg = '<div class="alert alert-danger">Password must be at least 10 characters.</div>';
        } else {
            try {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,is_active,must_change_password) VALUES (?,?,?,?,1,0)");
                $stmt->execute([$name, $email, $hash, 'admin']);
                $msg = '<div class="alert alert-success">Admin created. You can login now. IMPORTANT: delete <code>install.php</code>.</div>';
            } catch (PDOException $e) {
                if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                    $msg = '<div class="alert alert-warning">This email already exists.</div>';
                } else {
                    $msg = '<div class="alert alert-danger">Error: ' . e($e->getMessage()) . '</div>';
                }
            }
        }
    }
}

include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card p-4">
      <h4 class="mb-2">Installer (V6.3)</h4>
      <p class="text-muted small">Run once, then delete <code>install.php</code>.</p>
      <?php echo $msg; ?>

      <div class="alert alert-warning">
        <b>Before you start:</b>
        <ol class="mb-0">
          <li>Update <code>config.php</code> with correct DB credentials.</li>
          <li>Ensure MySQL user has permission to create tables.</li>
        </ol>
      </div>

      <form method="post" class="mb-4">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="action" value="run_sql">
        <button class="btn btn-primary">1) Create DB Tables + Seed Countries</button>
      </form>

      <hr>

      <h5 class="mb-2">2) Create First Admin</h5>
      <form method="post">
        <input type="hidden" name="<?= CSRF_KEY ?>" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="action" value="create_admin">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" placeholder="Admin Smart Vision" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required>
          </div>
          <div class="col-12">
            <label class="form-label">Password (min 10 chars)</label>
            <input class="form-control" type="password" name="password" required>
          </div>
        </div>
        <button class="btn btn-success mt-3">Create Admin</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
