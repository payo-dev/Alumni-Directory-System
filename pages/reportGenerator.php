<?php
// pages/reportGenerator.php
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();

// Fetch available columns from both tables (safe list)
$columns = [];

// fetch columns for alumni
$stmt = $pdo->query("SHOW COLUMNS FROM alumni");
while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $name = $col['Field'];
    $columns["alumni.$name"] = "alumni.$name";
}

// fetch columns for ccs_alumni (prefix to avoid collisions)
$stmt = $pdo->query("SHOW COLUMNS FROM ccs_alumni");
while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $name = $col['Field'];
    $columns["ccs_alumni.$name"] = "ccs_alumni.$name";
}

// Get distinct filter values (course & year_graduated from ccs_alumni, status from alumni)
$courses = $pdo->query("SELECT DISTINCT course FROM ccs_alumni ORDER BY course")->fetchAll(PDO::FETCH_COLUMN);
$years = $pdo->query("SELECT DISTINCT year_graduated FROM ccs_alumni ORDER BY year_graduated DESC")->fetchAll(PDO::FETCH_COLUMN);
$statuses = $pdo->query("SELECT DISTINCT status FROM alumni ORDER BY status")->fetchAll(PDO::FETCH_COLUMN);

// small helper for pretty labels
function prettyLabel($colKey) {
    // colKey like "alumni.student_id" or "ccs_alumni.year_graduated"
    $parts = explode('.', $colKey);
    $label = end($parts);
    $label = str_replace('_', ' ', $label);
    return ucwords($label);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Report Generator — Admin</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* Inline red admin theme */
    body { background:#fff5f5; font-family: Arial, sans-serif; padding:30px; }
    .card { max-width:1100px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
    h1 { color:#b30000; margin-top:0; }
    .grid { display:grid; grid-template-columns: 1fr 380px; gap:18px; }
    .filters, .columns { background:#fff; padding:14px; border-radius:6px; border:1px solid #f2dede; }
    .columns { max-height:420px; overflow:auto; }
    label { display:block; margin-bottom:6px; font-weight:600; }
    .field { margin-bottom:12px; }
    .btn { padding:10px 16px; border-radius:6px; border:none; cursor:pointer; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-outline { background:#fff; border:1px solid #dc3545; color:#b30000; }
    .small { font-size:0.9em; color:#666; }
    .col-checkbox { display:flex; gap:8px; align-items:center; padding:6px 0; border-bottom:1px solid #f6e4e4; }
    .actions { margin-top:12px; display:flex; gap:10px; align-items:center; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Generate Alumni Report</h1>
    <p class="small">Pick filters and the columns you want. Output is a pipe-separated <code>.txt</code> file (you can change to .csv later).</p>

    <div class="grid">
      <!-- Filters -->
      <div class="filters">
        <form method="POST" action="../functions/exportReport.php" id="reportForm">
          <h3 style="color:#b30000;">Filters</h3>

          <div class="field">
            <label for="filter_course">Course (CCS)</label>
            <select name="filter_course" id="filter_course">
              <option value="">— Any —</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="filter_year">Year Graduated (CCS)</label>
            <select name="filter_year" id="filter_year">
              <option value="">— Any —</option>
              <?php foreach ($years as $y): ?>
                <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="filter_status">Status (Alumni)</label>
            <select name="filter_status" id="filter_status">
              <option value="">— Any —</option>
              <?php foreach ($statuses as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars(ucfirst($s)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="query_search">Free-text search (name / email / student id)</label>
            <input type="text" name="query_search" id="query_search" placeholder="e.g. payo, 2023-01619, example@gmail.com" style="width:100%;">
          </div>

          <div class="actions">
            <button type="submit" class="btn btn-danger">Generate & Download (.txt)</button>
            <button type="button" class="btn btn-outline" id="selectAllCols">Select All Columns</button>
          </div>

          <input type="hidden" name="selected_columns[]"> <!-- placeholder so POST always has key -->
        </form>
      </div>

      <!-- Column selection -->
      <div class="columns">
        <h3 style="color:#b30000;">Select Columns to Include</h3>
        <div class="small" style="margin-bottom:8px;">You can pick columns from <strong>alumni</strong> and <strong>ccs_alumni</strong>.</div>

        <form id="colsForm" onsubmit="submitCols(); return false;">
          <?php foreach ($columns as $key => $dbcol): ?>
            <div class="col-checkbox">
              <input type="checkbox" id="<?= htmlspecialchars($key) ?>" name="cols[]" value="<?= htmlspecialchars($key) ?>">
              <label for="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?> — <?= htmlspecialchars(prettyLabel($key)) ?></label>
            </div>
          <?php endforeach; ?>

          <div style="margin-top:12px;">
            <button type="button" class="btn btn-danger" onclick="submitCols()">Add Selected Columns to Report</button>
            <span class="small" style="margin-left:10px;">(Selected columns will be sent with the filters.)</span>
          </div>
        </form>

      </div>
    </div>
  </div>

<script>
// Select all columns
document.getElementById('selectAllCols').addEventListener('click', () => {
  document.querySelectorAll('#colsForm input[type=checkbox]').forEach(cb => cb.checked = true);
});

// When admin clicks "Add Selected Columns..." gather and submit via main form
function submitCols() {
  const checked = Array.from(document.querySelectorAll('#colsForm input[type=checkbox]:checked')).map(i => i.value);
  if (checked.length === 0) {
    if (!confirm('No columns selected. Generate a report with no columns?')) return;
  }

  // Remove any existing hidden selected_columns inputs
  document.querySelectorAll('input[name="selected_columns[]"]').forEach(el => el.remove());

  // Add hidden inputs to main form
  const main = document.getElementById('reportForm');
  checked.forEach(c => {
    const i = document.createElement('input');
    i.type = 'hidden';
    i.name = 'selected_columns[]';
    i.value = c;
    main.appendChild(i);
  });

  // Submit the main form to exportReport.php
  main.submit();
}
</script>
</body>
</html>
