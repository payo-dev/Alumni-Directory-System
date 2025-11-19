<?php
// ==========================================================
// pages/reportGenerator.php — Colleges-Alumni Unified Report Generator
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();

/* ==========================================================
   🪪 Fetch Admin Full Name (for header)
   ========================================================== */
$adminUsername = $_SESSION['admin_username'] ?? '';
$adminFullName = 'Administrator';
if ($adminUsername !== '') {
    $stmtAdmin = $pdo->prepare("SELECT full_name FROM admin_account WHERE username = :username LIMIT 1");
    $stmtAdmin->execute([':username' => $adminUsername]);
    $adminFullName = $stmtAdmin->fetchColumn() ?: 'Administrator';
}

/* ==========================================================
   1️⃣ — Fetch Column Info for Checkbox Selection
   ========================================================== */
$columns = [];
foreach (['colleges_alumni', 'alumni_info', 'alumni_emp_record'] as $table) {
    $stmt = $pdo->query("SHOW COLUMNS FROM {$table}");
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns["{$table}.{$col['Field']}"] = "{$table}.{$col['Field']}";
    }
}

/* ==========================================================
   2️⃣ — Load Filter Dropdown Data
   ========================================================== */
$courses  = $pdo->query("SELECT DISTINCT course FROM colleges_alumni WHERE course <> '' ORDER BY course")->fetchAll(PDO::FETCH_COLUMN);
$years    = $pdo->query("SELECT DISTINCT year_graduated FROM colleges_alumni WHERE year_graduated IS NOT NULL ORDER BY year_graduated DESC")->fetchAll(PDO::FETCH_COLUMN);
$statuses = $pdo->query("SELECT DISTINCT status FROM alumni_info ORDER BY status")->fetchAll(PDO::FETCH_COLUMN);
$cities   = $pdo->query("SELECT DISTINCT city_municipality FROM alumni_info WHERE city_municipality <> '' ORDER BY city_municipality ASC")->fetchAll(PDO::FETCH_COLUMN);

/* ==========================================================
   3️⃣ — Handle Preview Query
   ========================================================== */
$summary = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filterCourse  = trim($_POST['filter_course'] ?? '');
    $filterYear    = trim($_POST['filter_year'] ?? '');
    $filterStatus  = trim($_POST['filter_status'] ?? '');
    $filterCity    = trim($_POST['filter_city'] ?? '');
    $filterEmp     = trim($_POST['filter_employment'] ?? '');
    $dateFrom      = trim($_POST['date_from'] ?? '');
    $dateTo        = trim($_POST['date_to'] ?? '');

    $where = [];
    $params = [];

    if ($filterCourse !== '') {
        $where[] = "ca.course = :course";
        $params[':course'] = $filterCourse;
    }
    if ($filterYear !== '') {
        $where[] = "ca.year_graduated = :yr";
        $params[':yr'] = $filterYear;
    }
    if ($filterStatus !== '') {
        $where[] = "ai.status = :status";
        $params[':status'] = $filterStatus;
    }
    if ($filterCity !== '') {
        $where[] = "ai.city_municipality = :city";
        $params[':city'] = $filterCity;
    }
    if ($filterEmp === 'employed') {
        $where[] = "(er.company_name IS NOT NULL AND TRIM(er.company_name) <> '')";
    } elseif ($filterEmp === 'unemployed') {
        $where[] = "(er.company_name IS NULL OR TRIM(er.company_name) = '')";
    }
    if ($dateFrom !== '' && $dateTo !== '') {
        $where[] = "DATE(ai.created_at) BETWEEN :from AND :to";
        $params[':from'] = $dateFrom;
        $params[':to']   = $dateTo;
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $pdo->prepare("
        SELECT ca.*, ai.status, ai.city_municipality, er.company_name, er.position
        FROM colleges_alumni ca
        LEFT JOIN alumni_info ai ON ai.student_id = ca.student_id
        LEFT JOIN alumni_emp_record er ON er.student_id = ca.student_id
        $whereSQL
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compute summary
    $summary['total'] = count($rows);
    $summary['employed'] = count(array_filter($rows, fn($r) => !empty(trim($r['company_name']))));
    $summary['unemployed'] = $summary['total'] - $summary['employed'];

    $courseCounts = [];
    $cityCounts = [];
    $yearList = [];

    foreach ($rows as $r) {
        if (!empty($r['course'])) $courseCounts[$r['course']] = ($courseCounts[$r['course']] ?? 0) + 1;
        if (!empty($r['city_municipality'])) $cityCounts[$r['city_municipality']] = ($cityCounts[$r['city_municipality']] ?? 0) + 1;
        if (!empty($r['year_graduated'])) $yearList[] = $r['year_graduated'];
    }

    arsort($courseCounts);
    arsort($cityCounts);
    $summary['top_courses'] = array_slice($courseCounts, 0, 3, true);
    $summary['top_cities']  = array_slice($cityCounts, 0, 3, true);
    $summary['year_min'] = $yearList ? min($yearList) : '-';
    $summary['year_max'] = $yearList ? max($yearList) : '-';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Report Generator — Admin</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body { background:#fff5f5; font-family:Arial, sans-serif; }
    .mainContainer { max-width:1200px; margin:30px auto; background:#fff; padding:22px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    header { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #dc3545; padding-bottom:12px; margin-bottom:25px; }
    header h1 { color:#b30000; margin:0; }
    .nav-links a { margin-left:12px; color:#dc3545; text-decoration:none; border:1px solid #dc3545; padding:4px 8px; border-radius:5px; }
    .filters, .columns { background:#fff; padding:14px; border-radius:6px; border:1px solid #f2dede; }
    label { font-weight:600; margin-bottom:5px; display:block; }
    select, input[type=text], input[type=date] { width:100%; padding:6px; border-radius:4px; border:1px solid #ccc; }
    .actions { margin-top:14px; display:flex; gap:10px; flex-wrap:wrap; }
    .btn { padding:10px 16px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-outline { background:#fff; border:2px solid #dc3545; color:#b30000; }
    .summary-box { margin-top:20px; border:1px solid #f2dede; border-radius:6px; padding:12px; }
    .summary-box h3 { color:#b30000; margin-top:0; }
    .summary-list { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:10px; }
    .summary-item { background:#fff0f0; border-left:5px solid #dc3545; padding:10px; border-radius:6px; }
    .summary-item h4 { margin:0; font-size:1em; color:#b30000; }
  </style>
</head>
<body>
<div class="mainContainer">
  <header>
    <h1>🧾 Report Generator</h1>
    <div class="nav-links">
      Logged in as <strong><?= htmlspecialchars($adminFullName) ?></strong>
      <a href="adminDashboard.php">🏠 Dashboard</a>
      <a href="adminAnalytics.php">📊 Analytics</a>
      <a href="../classes/logout.php">🚪 Logout</a>
    </div>
  </header>

  <form method="POST" id="reportForm">
    <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:18px;">
      <!-- Filters -->
      <div class="filters">
        <h3 style="color:#b30000;">Filters</h3>
        <label>Course</label>
        <select name="filter_course" id="filter_course">
          <option value="">— Any —</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= (($_POST['filter_course'] ?? '') === $c) ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Year Graduated</label>
        <select name="filter_year" id="filter_year">
          <option value="">— Any —</option>
          <?php foreach ($years as $y): ?>
            <option value="<?= htmlspecialchars($y) ?>" <?= (($_POST['filter_year'] ?? '') === $y) ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Status</label>
        <select name="filter_status" id="filter_status">
          <option value="">— Any —</option>
          <?php foreach ($statuses as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= (($_POST['filter_status'] ?? '') === $s) ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($s)) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Employment</label>
        <select name="filter_employment" id="filter_employment">
          <option value="">— Any —</option>
          <option value="employed" <?= (($_POST['filter_employment'] ?? '') === 'employed') ? 'selected' : '' ?>>Employed</option>
          <option value="unemployed" <?= (($_POST['filter_employment'] ?? '') === 'unemployed') ? 'selected' : '' ?>>Unemployed</option>
        </select>

        <label>City / Municipality</label>
        <select name="filter_city" id="filter_city">
          <option value="">— Any —</option>
          <?php foreach ($cities as $city): ?>
            <option value="<?= htmlspecialchars($city) ?>" <?= (($_POST['filter_city'] ?? '') === $city) ? 'selected' : '' ?>><?= htmlspecialchars($city) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Date Range</label>
        <div style="display:flex; gap:10px;">
          <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($_POST['date_from'] ?? '') ?>">
          <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($_POST['date_to'] ?? '') ?>">
        </div>

        <div class="actions">
          <button type="submit" class="btn btn-danger">Preview Summary</button>
          <a id="exportCSV" class="btn btn-danger">⬇️ Export CSV</a>
          <a id="previewPDF" target="_blank" class="btn btn-outline">🧾 Preview PDF</a>
          <a id="downloadPDF" class="btn btn-danger">📄 Download PDF</a>
        </div>
      </div>

      <!-- Columns -->
      <div class="columns">
        <h3 style="color:#b30000;">Columns (Select for Export)</h3>
        <div style="max-height:400px; overflow:auto;">
          <?php foreach ($columns as $key => $dbcol): ?>
            <div style="margin-bottom:6px;">
              <input type="checkbox" id="<?= htmlspecialchars($key) ?>" name="selected_columns[]" value="<?= htmlspecialchars($key) ?>" checked>
              <label for="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </form>

  <?php if (!empty($summary)): ?>
    <div class="summary-box">
      <h3>📊 Report Summary Preview</h3>
      <div class="summary-list">
        <div class="summary-item"><h4>Total Alumni</h4><?= $summary['total'] ?></div>
        <div class="summary-item"><h4>Employed</h4><?= $summary['employed'] ?></div>
        <div class="summary-item"><h4>Unemployed</h4><?= $summary['unemployed'] ?></div>
        <div class="summary-item"><h4>Top Courses</h4>
          <?php foreach ($summary['top_courses'] as $c => $count) echo "$c ($count)<br>"; ?>
        </div>
        <div class="summary-item"><h4>Top Cities</h4>
          <?php foreach ($summary['top_cities'] as $c => $count) echo "$c ($count)<br>"; ?>
        </div>
        <div class="summary-item"><h4>Year Range</h4><?= $summary['year_min'] ?>–<?= $summary['year_max'] ?></div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
const filters = ['filter_course','filter_year','filter_status','filter_city','filter_employment','date_from','date_to'];
function updateLinks() {
  const params = new URLSearchParams();
  filters.forEach(f => {
    const v = document.getElementById(f).value;
    if (v) params.append(f, v);
  });
  document.getElementById('previewPDF').href = '../functions/exportPDF.php?preview=1&' + params.toString();
  document.getElementById('downloadPDF').href = '../functions/exportPDF.php?' + params.toString();
  document.getElementById('exportCSV').href = '../functions/exportReport.php?' + params.toString();
}
filters.forEach(f => document.getElementById(f).addEventListener('change', updateLinks));
window.addEventListener('DOMContentLoaded', updateLinks);
</script>
</body>
</html>
