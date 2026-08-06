<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_admin();
$pdo = db();

function gen_password(int $len=12): string {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    $out = '';
    for ($i=0;$i<$len;$i++) $out .= $chars[random_int(0, strlen($chars)-1)];
    return $out;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $role = (string)($_POST['role'] ?? 'staff');
        $pass = (string)($_POST['password'] ?? '');

        if ($name==='' || $email==='') {
            flash_set('err','Name and email are required.');
            header('Location: ' . url('/users.php')); exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash_set('err','Invalid email.');
            header('Location: ' . url('/users.php')); exit;
        }
        if ($role !== 'admin' && $role !== 'staff') $role = 'staff';

        if ($pass==='') $pass = gen_password(12);
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,is_active,must_change_password)
                                   VALUES (:n,:e,:h,:r,1,0)");
            $stmt->execute([':n'=>$name, ':e'=>$email, ':h'=>$hash, ':r'=>$role]);
            $uid = (int)$pdo->lastInsertId();
            audit('user_create','user',$uid,['email'=>$email,'role'=>$role]);
            flash_set('ok',"User created. Temporary password: $pass");
        } catch (Throwable $t) {
            flash_set('err','Error: ' . $t->getMessage());
        }
        header('Location: ' . url('/users.php')); exit;
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        audit('user_toggle','user',$id);
        flash_set('ok','User status updated.');
        header('Location: ' . url('/users.php')); exit;
    }

    if ($action === 'reset_password') {
        $id = (int)($_POST['id'] ?? 0);
        $newPass = gen_password(12);
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash=:h, must_change_password=0 WHERE id=:id");
        $stmt->execute([':h'=>$hash, ':id'=>$id]);
        audit('user_reset_password','user',$id);
        flash_set('ok',"Password reset. New password: $newPass");
        header('Location: ' . url('/users.php')); exit;
    }

    if ($action === 'update_user') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $role = (string)($_POST['role'] ?? 'staff');
        $isActive = (int)($_POST['is_active'] ?? 1) === 1 ? 1 : 0;

        if ($name==='' || $email==='') {
            flash_set('err','Name and email are required.');
            header('Location: ' . url('/users.php')); exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash_set('err','Invalid email.');
            header('Location: ' . url('/users.php')); exit;
        }
        if ($role !== 'admin' && $role !== 'staff') $role='staff';

        // Safety: prevent locking yourself out
        if ($id === (int)(current_user()['id'] ?? 0) && $isActive === 0) {
            flash_set('err','You cannot disable your own account.');
            header('Location: ' . url('/users.php')); exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE users SET name=:n, email=:e, role=:r, is_active=:a WHERE id=:id");
            $stmt->execute([':n'=>$name, ':e'=>$email, ':r'=>$role, ':a'=>$isActive, ':id'=>$id]);
            audit('user_update','user',$id,['email'=>$email,'role'=>$role,'is_active'=>$isActive]);
            flash_set('ok','User updated.');
        } catch (Throwable $t) {
            flash_set('err','Error: ' . $t->getMessage());
        }
        header('Location: ' . url('/users.php')); exit;
    }
}

$users = $pdo->query("SELECT id,name,email,role,is_active,created_at FROM users ORDER BY created_at DESC")->fetchAll();

$title = APP_NAME . ' | Admin - Users';
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="m-0">Users</h4>
  <a class="btn btn-outline-dark" href="<?= e(url('/index.php')) ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card p-4">
      <h6 class="mb-3">Create New User</h6>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="create">
        <div class="mb-2">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input class="form-control" name="email" type="email" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Role</label>
          <select class="form-select" name="role">
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password (optional)</label>
          <input class="form-control" name="password" placeholder="Leave empty to auto-generate">
        </div>
        <button class="btn btn-primary w-100">Create</button>
      </form>
      <div class="small text-muted mt-3">
        The system will show you the temporary password once after creation/reset.
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card p-4">
      <h6 class="mb-3">All Users</h6>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th style="min-width:180px">Name</th>
              <th style="min-width:240px">Email</th>
              <th style="min-width:140px">Role</th>
              <th style="min-width:120px">Status</th>
              <th style="min-width:260px">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <form method="post" style="display:contents">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                <td>
                  <input class="form-control form-control-sm" name="name" value="<?= e($u['name']) ?>" required>
                </td>
                <td>
                  <input class="form-control form-control-sm" name="email" type="email" value="<?= e($u['email']) ?>" required>
                </td>
                <td>
                  <select class="form-select form-select-sm" name="role">
                    <option value="staff" <?= $u['role']==='staff'?'selected':'' ?>>Staff</option>
                    <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                  </select>
                </td>
                <td>
                  <select class="form-select form-select-sm" name="is_active">
                    <option value="1" <?= (int)$u['is_active']===1?'selected':'' ?>>Active</option>
                    <option value="0" <?= (int)$u['is_active']===0?'selected':'' ?>>Disabled</option>
                  </select>
                </td>
                <td>
                  <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary">Save</button>
                  </div>
                </td>
              </form>
            </tr>
            <tr class="border-0">
              <td colspan="5" class="pt-0">
                <div class="d-flex gap-2">
                  <form method="post" class="m-0">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button class="btn btn-sm btn-outline-dark">Reset Password</button>
                  </form>
                  <form method="post" class="m-0">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><?= (int)$u['is_active']===1 ? 'Disable' : 'Enable' ?></button>
                  </form>
                  <div class="small text-muted align-self-center">User ID: <?= (int)$u['id'] ?></div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
