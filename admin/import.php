<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { http_response_code(403); include __DIR__ . '/../403.php'; exit; }

$pdo = db();

// Download skipped/failed reports
if (($_GET['download'] ?? '') === 'skipped' || ($_GET['download'] ?? '') === 'failed') {
  $type = $_GET['download'];
  $key = 'admin_import' . '_' . $type;
  $rows = $_SESSION[$key] ?? [];
  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename="' . $type . '_rows.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['row_number','company_name','reason']);
  foreach ($rows as $r) {
    fputcsv($out, [$r['row'] ?? '', $r['company_name'] ?? '', $r['reason'] ?? '']);
  }
  fclose($out);
  exit;
}

$msg = '';
$added = 0;
$skipped = 0;
$failed = 0;
$skippedDetails = [];
$failedDetails = [];

// clear last session reports
unset($_SESSION['admin_import_skipped'], $_SESSION['admin_import_failed']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify();

  if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    $msg = 'Please upload a CSV file (Excel must be saved as CSV).';
  } else {
    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'] ?? '';
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if ($ext !== 'csv') {
      $msg = 'Invalid file type. Please upload a CSV file.';
    } else {
      $fh = fopen($tmp, 'r');
      if (!$fh) {
        $msg = 'Unable to read the uploaded file.';
      } else {
        // Prepare statements
        $insert = $pdo->prepare("INSERT INTO leads (company_name, status, sales_rep_id, created_by, updated_by) VALUES (?,?,?,?,?)");
        // Logical duplicate check: same "bucket" (My Leads or Free Leads)
        $checkDup = $pdo->prepare("SELECT id FROM leads WHERE company_name = ? AND ((sales_rep_id IS NULL AND ? IS NULL) OR sales_rep_id = ?) LIMIT 1");

        $sales_rep_id = NULL;
        $status = 'new';
        $created_by = (int)$u['id'];
        $updated_by = (int)$u['id'];

        $pdo->beginTransaction();
        try {
          // detect header row
          $first = fgetcsv($fh);
          $rows_to_process = [];

          $looks_header = false;
          if ($first && isset($first[0])) {
            $c0 = strtolower(trim((string)$first[0]));
            if (in_array($c0, ['company', 'company_name', 'company name', 'name', 'companyname'])) {
              $looks_header = true;
            }
          }
          if (!$looks_header && $first !== false) {
            $rows_to_process[] = $first;
          }

          while (($row = fgetcsv($fh)) !== false) {
            $rows_to_process[] = $row;
          }

          $rowNumber = $looks_header ? 2 : 1;
          foreach ($rows_to_process as $row) {
            $company = trim((string)($row[0] ?? ''));
            $company = preg_replace('/\s+/', ' ', $company);

            if ($company === '') {
              $skipped++;
              $skippedDetails[] = ['row' => $rowNumber, 'company_name' => '', 'reason' => 'Empty company name'];
              $rowNumber++;
              continue;
            }

            $checkDup->execute([$company, $sales_rep_id, $sales_rep_id]);
            if ($checkDup->fetch()) {
              $skipped++;
              $skippedDetails[] = ['row' => $rowNumber, 'company_name' => $company, 'reason' => 'Duplicate (already exists in this list)'];
              $rowNumber++;
              continue;
            }

            try {
              $insert->execute([$company, $status, $sales_rep_id, $created_by, $updated_by]);
              $added++;
            } catch (Throwable $e) {
              $failed++;
              $failedDetails[] = ['row' => $rowNumber, 'company_name' => $company, 'reason' => 'DB error: ' . $e->getMessage()];
              // Continue, do not abort the entire import
            }

            $rowNumber++;
          }

          $pdo->commit();

          $_SESSION['admin_import_skipped'] = $skippedDetails;
          $_SESSION['admin_import_failed'] = $failedDetails;

          $msg = "Import complete. Added: {$added}, Skipped: {$skipped}, Failed: {$failed}.";
        } catch (Throwable $e) {
          $pdo->rollBack();
          $msg = "Import aborted: " . $e->getMessage();
        }
        fclose($fh);
      }
    }
  }
}

include __DIR__ . '/../partials/header.php';
?>
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="m-0">Admin Import (Free Leads)</h3>
    <a class="btn btn-outline-secondary" href="index.php">Back</a>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <p class="text-muted mb-3">Upload a CSV file with <b>one column only</b>: <code>company_name</code>. Excel files must be saved as CSV.</p>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <?php echo csrf_field(); ?>
        <div class="col-md-8">
          <input type="file" name="file" class="form-control" accept=".csv" required>
        </div>
        <div class="col-md-4">
          <button class="btn btn-primary w-100" type="submit">Import</button>
        </div>
      </form>

      <?php if ($added || $skipped || $failed): ?>
        <hr>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="fw-bold">Added</div>
              <div class="display-6"><?php echo (int)$added; ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="fw-bold">Skipped</div>
              <div class="display-6"><?php echo (int)$skipped; ?></div>
              <?php if ($skipped): ?>
                <a class="btn btn-sm btn-outline-secondary mt-2" href="?download=skipped">Download skipped.csv</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 border rounded bg-light">
              <div class="fw-bold">Failed</div>
              <div class="display-6"><?php echo (int)$failed; ?></div>
              <?php if ($failed): ?>
                <a class="btn btn-sm btn-outline-danger mt-2" href="?download=failed">Download failed.csv</a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php if (!empty($skippedDetails)): ?>
          <div class="mt-4">
            <h6 class="text-secondary">Skipped (sample)</h6>
            <div class="table-responsive">
              <table class="table table-sm table-striped">
                <thead><tr><th>#</th><th>Company</th><th>Reason</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($skippedDetails, 0, 20) as $r): ?>
                  <tr>
                    <td><?php echo (int)$r['row']; ?></td>
                    <td><?php echo htmlspecialchars($r['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['reason']); ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($failedDetails)): ?>
          <div class="mt-4">
            <h6 class="text-danger">Failed (sample)</h6>
            <div class="table-responsive">
              <table class="table table-sm table-striped">
                <thead><tr><th>#</th><th>Company</th><th>Error</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($failedDetails, 0, 20) as $r): ?>
                  <tr>
                    <td><?php echo (int)$r['row']; ?></td>
                    <td><?php echo htmlspecialchars($r['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['reason']); ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>

    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
