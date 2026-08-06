<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

$user  = current_user();
$title = $title ?? APP_NAME;

// Home link (root-absolute)
$home = url('/login.php');

if ($user) {
  $home = is_admin()
    ? url('/index.php')
    : url('/employee/dashboard.php');
}
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(url('/assets/sv.css')) ?>" rel="stylesheet">
</head>
<body>

<div class="sv-topbar py-3 mb-4">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <a class="sv-brand" href="<?= e($home) ?>" aria-label="Go to home">
        <img class="sv-logo"
             src="https://www.smartvisioneg.com/vision/data/logos/68deaecd14.png"
             alt="Smart Vision"
             loading="lazy">
        <span class="sv-brand-text"><?= e(APP_NAME) ?></span>
      </a>

      <?php if ($user): ?>
        <div class="small opacity-75">
          Signed in as: <?= e($user['name']) ?> (<?= e($user['role']) ?>)
        </div>
      <?php endif; ?>
    </div>

    <div class="d-flex gap-3 align-items-center">
      <?php if ($user): ?>
        <a class="sv-navlink" href="<?= e(url('/employee/search.php')) ?>">Employee Portal</a>

        <?php if (is_admin()): ?>
          <a class="sv-navlink" href="<?= e(url('/index.php')) ?>">Admin Panel</a>
        <?php endif; ?>

        <a class="btn btn-light btn-sm" href="<?= e(url('/logout.php')) ?>">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="container">
<?php
$ok  = flash_get('ok');
$err = flash_get('err');

if ($ok)  echo "<div class='alert alert-success'>" . e($ok) . "</div>";
if ($err) echo "<div class='alert alert-danger'>" . e($err) . "</div>";
?>
