<?php
// pages/adminDashboard.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// flash
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// search + filter params
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? 'All');
$statusFilter = trim($_GET['status'] ?? 'All');

$searchQuery = "";
$params = [];

if ($search !== '') {
    $searchQuery .= " AND (a.surname LIKE :search OR a.given_name LIKE :search OR a.email LIKE :search OR a.student_id LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($courseFilter !== '' && $courseFilter !== 'All') {
    $searchQuery .= " AND (COALESCE(c.course, '') = :course OR a.tertiary_school LIKE :course_like)";
    $params[':course'] = $courseFilter;
    $params[':course_like'] = "%{$courseFilter}%";
}


if ($statusFilter !== '' && $statusFilter !== 'All') {
    $searchQuery .= " AND a.status = :status";
    $params[':status'] = $statusFilter;
}

// === Analytics ===
// total alumni
$totalAlumni = (int) $pdo->query("SELECT COUNT(*) FROM alumni")->fetchColumn();

// employed = company_name not null AND not empty
$employed = (int) $pdo->query("SELECT COUNT(*) FROM alumni WHERE company_name IS NOT NULL AND TRIM(company_name) <> ''")->fetchColumn();
$unemployed = $totalAlumni - $employed;

// status counts
$countsStmt = $pdo->query("
    SELECT status, COUNT(*) AS cnt
    FROM alumni
    GROUP BY status
");
$statusCounts = [];
while ($r = $countsStmt->fetch()) {
    $statusCounts[$r['status']] = (int)$r['cnt'];
}

// grad-year distribution (uses ccs_alumni.year_graduated first, then alumni.tertiary_yr, alumni.graduate_yr)
$yearStmt = $pdo->query("
    SELECT COALESCE(c.year_graduated, a.tertiary_yr, a.graduate_yr) AS grad_year, COUNT(*) AS cnt
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    GROUP BY grad_year
    HAVING grad_year IS NOT NULL AND grad_year <> ''
    ORDER BY grad_year DESC
");
$gradYears = $yearStmt->fetchAll(PDO::FETCH_ASSOC);

// === Lists ===
// Pending
$sqlPending = "
    SELECT a.student_id, a.surname, a.given_name, COALESCE(c.course, '') AS course, COALESCE(c.year_graduated, a.tertiary_yr, a.graduate_yr) AS grad_year, a.email, a.issued_date, a.created_at, a.status
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    WHERE a.status = 'pending' {$searchQuery}
    ORDER BY a.created_at DESC
";
$stmt = $pdo->prepare($sqlPending);
$stmt->execute($params);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Active
$sqlActive = "
    SELECT a.student_id, a.surname, a.given_name, COALESCE(c.course, '') AS course, COALESCE(c.year_graduated, a.tertiary_yr, a.graduate_yr) AS grad_year, a.email, a.validated_date, a.status
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    WHERE a.status = 'active' {$searchQuery}
    ORDER BY a.validated_date DESC
";
$stmt = $pdo->prepare($sqlActive);
$stmt->execute($params);
$active = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Archived
$sqlArchived = "
    SELECT a.student_id, a.surname, a.given_name, COALESCE(c.course, '') AS course, COALESCE(c.year_graduated, a.tertiary_yr, a.graduate_yr) AS grad_year, a.email, a.validated_date, a.status
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    WHERE a.status = 'archived' {$searchQuery}
    ORDER BY a.validated_date DESC
";
$stmt = $pdo->prepare($sqlArchived);
$stmt->execute($params);
$archived = $stmt->fetchAll(PDO::FETCH_ASSOC);

// sample course options for filter (expand later)
$courseOptions = ['All','BSCS','BSIT','ACT'];
$statusOptions = ['All','pending','active','archived'];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Alumni (WMSU)</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* red admin theme quick inline */
    body { background:#fff5f5; font-family: Arial, sans-serif; }
    .mainContainer { max-width:1200px; margin:30px auto; background:#fff; padding:22px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    header { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #dc3545; padding-bottom:12px; }
    header h1 { color:#b30000; margin:0; }
    .nav-links a { margin-left:12px; color:#dc3545; text-decoration:none; }
    .search-bar { display:flex; gap:10px; align-items:center; justify-content:space-between; margin:18px 0; }
    .search-left { display:flex; gap:8px; align-items:center; }
    .search-left input, .search-left select { padding:6px 8px; border:1px solid #dc3545; border-radius:4px; }
    .stats { font-weight:bold; color:#b30000; }
    .analytics { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
    .card { background:#fff; border:1px solid #f3d6d6; border-left:6px solid #dc3545; padding:14px; border-radius:6px; min-width:180px; box-shadow:0 2px 6px rgba(0,0,0,0.04); }
    .card h3 { margin:0 0 6px 0; color:#b30000; font-size:1.05em; }
    table { width:100%; border-collapse:collapse; margin-top:14px; }
    th, td { padding:10px; border:1px solid #f0b3b3; text-align:left; }
    th { background:#dc3545; color:#fff; }
    tr:nth-child(even){ background:#fff0f0; }
    .actions a { color:#b30000; font-weight:bold; text-decoration:none; margin-right:8px; }
    .export-form { margin-top:10px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .small { font-size:0.9em; color:#555; }
  </style>
</head>
<body>
<div class="mainContainer">
  <header>
    <h1>Admin Dashboard</h1>
    <div class="nav-links">
      Logged in as <strong><?= htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Administrator'); ?></strong>
      <a href="../index.php">🏠 Home</a>
      <a href="../classes/logout.php">🚪 Logout</a>
    </div>
  </header>

  <?php if ($flash): ?>
    <div style="margin-top:12px; padding:10px; background:#ffe6e6; border:1px solid #ffcccc; color:#a10000; border-radius:6px;">
      <?= htmlspecialchars($flash) ?>
    </div>
  <?php endif; ?>

  <div class="search-bar">
    <div class="search-left">
      <form method="GET" style="display:flex; gap:8px; align-items:center;">
        <input type="text" name="search" placeholder="Search name / email / student ID" value="<?= htmlspecialchars($search); ?>">
        <select name="course">
          <?php foreach ($courseOptions as $opt): $sel = ($courseFilter === $opt) ? 'selected' : ''; ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= $sel ?>><?= htmlspecialchars($opt) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="status">
          <?php foreach ($statusOptions as $opt): $sel = ($statusFilter === $opt) ? 'selected' : ''; ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= $sel ?>><?= htmlspecialchars($opt) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" style="padding:6px 10px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Filter</button>
        <?php if ($search || ($courseFilter !== 'All') || ($statusFilter !== 'All')): ?>
          <a href="adminDashboard.php" style="margin-left:8px; color:#b30000;">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="stats">
      Total Alumni: <?= $totalAlumni ?> |
      Pending: <?= $statusCounts['pending'] ?? 0 ?> |
      Active: <?= $statusCounts['active'] ?? 0 ?> |
      Archived: <?= $statusCounts['archived'] ?? 0 ?>
    </div>
  </div>

  <!-- Analytics -->
  <div class="analytics">
    <div class="card">
      <h3>Total Alumni</h3>
      <div class="small"><?= $totalAlumni ?></div>
    </div>

    <div class="card">
      <h3>Employed</h3>
      <div class="small"><?= $employed ?></div>
    </div>

    <div class="card">
      <h3>Unemployed</h3>
      <div class="small"><?= $unemployed ?></div>
    </div>

    <div class="card">
      <h3>By Graduation Year</h3>
      <div class="small">
        <?php if (empty($gradYears)): ?>
          No grad year data.
        <?php else: ?>
          <ul style="margin:6px 0 0 0; padding-left:16px;">
            <?php foreach ($gradYears as $g): ?>
              <li><?= htmlspecialchars($g['grad_year']) ?> — <?= (int)$g['cnt'] ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <h3>Export / Reports</h3>
      <form method="POST" action="../functions/exportReport.php" class="export-form">
        <input type="hidden" name="course" value="<?= htmlspecialchars($courseFilter) ?>">
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
        <!-- columns selector -->
        <label class="small">Columns:</label>
        <label><input type="checkbox" name="columns[]" value="student_id" checked> ID</label>
        <label><input type="checkbox" name="columns[]" value="surname" checked> Surname</label>
        <label><input type="checkbox" name="columns[]" value="given_name" checked> Given</label>
        <label><input type="checkbox" name="columns[]" value="course" checked> Course</label>
        <label><input type="checkbox" name="columns[]" value="grad_year" checked> GradYear</label>
        <label><input type="checkbox" name="columns[]" value="email" checked> Email</label>
        <label><input type="checkbox" name="columns[]" value="company_name"> Company</label>
        <button type="submit" style="padding:6px 10px; background:#b30000; color:#fff; border:none; border-radius:4px; cursor:pointer;">Export CSV</button>
      </form>
    </div>
  </div>

  <!-- PENDING -->
  <section>
    <h2 style="color:#b30000; border-top:2px solid #f0b3b3; padding-top:10px;">Pending Applications</h2>
    <?php if (empty($pending)): ?>
      <p>No pending submissions found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Student ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Submitted</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($pending as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['student_id']); ?></td>
            <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
            <td><?= htmlspecialchars(($row['course'] ?: $row['grad_year'])); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['created_at'] ?? $row['issued_date'] ?? '-'); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= urlencode($row['student_id']); ?>">View</a>
              <a href="../functions/approve.php?id=<?= urlencode($row['student_id']); ?>" onclick="return confirm('Approve this record?')">Approve</a>
              <a href="../functions/reject.php?id=<?= urlencode($row['student_id']); ?>" onclick="return confirm('Reject and archive this record?')">Reject</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <!-- ACTIVE -->
  <section>
    <h2 style="color:#b30000;">Active Alumni</h2>
    <?php if (empty($active)): ?>
      <p>No active alumni yet.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>Student ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Validated On</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($active as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['student_id']); ?></td>
            <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
            <td><?= htmlspecialchars(($row['course'] ?: $row['grad_year'])); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['validated_date'] ?? '-'); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= urlencode($row['student_id']); ?>">View</a>
              <a href="../functions/archive.php?id=<?= urlencode($row['student_id']); ?>" onclick="return confirm('Archive this alumni record?')">Archive</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <!-- ARCHIVED -->
  <section>
    <h2 style="color:#b30000;">Archived Alumni</h2>
    <?php if (empty($archived)): ?>
      <p>No archived alumni found.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>Student ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Archived On</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($archived as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['student_id']); ?></td>
            <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
            <td><?= htmlspecialchars(($row['course'] ?: $row['grad_year'])); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['validated_date'] ?? '-'); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= urlencode($row['student_id']); ?>">View</a>
              <a href="../functions/restore.php?id=<?= urlencode($row['student_id']); ?>" onclick="return confirm('Restore this alumni record to active?')">Restore</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>
</body>
</html>
