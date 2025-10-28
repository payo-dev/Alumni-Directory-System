<?php
<<<<<<< Updated upstream
// ==========================================================
//  ADMIN DASHBOARD (Red Theme + Name/Course Display)
// ==========================================================
=======
// pages/adminDashboard.php
>>>>>>> Stashed changes
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// flash
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

<<<<<<< Updated upstream
// ✅ Search
$search = $_GET['search'] ?? '';
$searchQuery = "";  
=======
// search + filter params
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? 'All');
$statusFilter = trim($_GET['status'] ?? 'All');

$searchQuery = "";
>>>>>>> Stashed changes
$params = [];

if ($search !== '') {
    $searchQuery .= " AND (a.surname LIKE :search OR a.given_name LIKE :search OR a.email LIKE :search OR a.student_id LIKE :search)";
    $params[':search'] = "%$search%";
}

<<<<<<< Updated upstream
// ✅ Fetch data
$stmt = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, created_at
    FROM pending_alumni
    WHERE status = 'pending' $searchQuery
    ORDER BY created_at DESC
");
$stmt->execute($params);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, created_at
    FROM active_alumni
    WHERE 1 $searchQuery
    ORDER BY created_at DESC
");
$stmt2->execute($params);
$approved = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$stmt3 = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, created_at
    FROM archived_alumni
    WHERE 1 $searchQuery
    ORDER BY created_at DESC
");
$stmt3->execute($params);
$archived = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// ✅ Counts
$totalPending = $pdo->query("SELECT COUNT(*) FROM pending_alumni WHERE status = 'pending'")->fetchColumn();
$totalApproved = $pdo->query("SELECT COUNT(*) FROM active_alumni")->fetchColumn();
$totalArchived = $pdo->query("SELECT COUNT(*) FROM archived_alumni")->fetchColumn();
=======
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
>>>>>>> Stashed changes
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
<<<<<<< Updated upstream
  <title>Admin Dashboard - Alumni System</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body.default-program-bg {
        background: #fff5f5;
        font-family: Arial, sans-serif;
    }
    .mainContainer {
        max-width: 1200px;
        margin: 30px auto;
        background: #ffffff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 12px rgba(0,0,0,0.15);
    }
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #dc3545;
        padding-bottom: 12px;
    }
    header h1 {
        color: #b30000;
        margin: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 5px;
        overflow: hidden;
    }
    th, td {
        padding: 10px;
        border: 1px solid #f0b3b3;
        text-align: left;
    }
    th {
        background: #dc3545;
        color: white;
        font-weight: bold;
    }
    tr:nth-child(even) {
        background: #ffe5e5;
    }
    tr:hover {
        background: #ffcccc;
    }
    .actions a {
        margin-right: 8px;
        text-decoration: none;
        color: #b30000;
        font-weight: bold;
    }
    .actions a:hover {
        text-decoration: underline;
    }
    .search-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0;
    }
    .search-bar input {
        padding: 6px;
        width: 220px;
        border: 1px solid #dc3545;
        border-radius: 4px;
    }
    .search-bar button {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 12px;
        cursor: pointer;
    }
    .search-bar button:hover {
        background: #b30000;
    }
    .stats {
        font-weight: bold;
        color: #b30000;
    }
    .nav-links a {
        margin-left: 12px;
        text-decoration: none;
        color: #dc3545;
    }
    .nav-links a:hover {
        text-decoration: underline;
    }
    .flash-message {
        background: #ffe6e6;
        color: #a10000;
        border: 1px solid #ffcccc;
        padding: 10px;
        border-radius: 5px;
        margin: 15px 0;
        font-weight: bold;
    }
    .flash-message.error {
        background: #ffcccc;
        color: #7a0000;
        border-color: #ff9999;
    }
    h2 {
        margin-top: 40px;
        border-top: 2px solid #f0b3b3;
        padding-top: 20px;
        color: #b30000;
    }
  </style>
</head>
<body class="default-program-bg">

=======
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
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
      <div class="flash-message <?= str_starts_with($flash, '❌') ? 'error' : ''; ?>">
        <?= htmlspecialchars($flash); ?>
      </div>
  <?php endif; ?>

  <!-- =======================
       PENDING SUBMISSIONS
  ======================= -->
  <section>
    <h2>Pending Submissions</h2>

    <div class="search-bar">
      <div class="stats">
        Pending: <?= $totalPending ?> |
        Approved: <?= $totalApproved ?> |
        Archived: <?= $totalArchived ?>
      </div>

      <form method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" placeholder="Search name/email"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)) : ?>
          <a href="adminDashboard.php" style="text-decoration:none; color:#b30000;">Reset</a>
=======
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
>>>>>>> Stashed changes
        <?php endif; ?>
      </form>
    </div>

<<<<<<< Updated upstream
=======
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
>>>>>>> Stashed changes
    <?php if (empty($pending)): ?>
      <p>No pending submissions found.</p>
    <?php else: ?>
      <table>
        <thead>
<<<<<<< Updated upstream
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course / Year</th>
            <th>Email</th>
            <th>Date Submitted</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($pending as $r): ?>
          <tr>
            <td><?= $r['id']; ?></td>
            <td><?= htmlspecialchars($r['surname'] . ', ' . $r['given_name']); ?></td>
            <td><?= htmlspecialchars($r['course_year']); ?></td>
            <td><?= htmlspecialchars($r['email']); ?></td>
            <td><?= htmlspecialchars($r['created_at']); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= $r['id']; ?>">View</a>
              <a href="../functions/approve.php?id=<?= $r['id']; ?>" onclick="return confirm('Approve this applicant?')">Approve</a>
              <a href="../functions/reject.php?id=<?= $r['id']; ?>" onclick="return confirm('Reject this applicant?')">Reject</a>
=======
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
>>>>>>> Stashed changes
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<<<<<<< Updated upstream
  <!-- =======================
       APPROVED ALUMNI
  ======================= -->
  <section>
    <h2>Approved Alumni</h2>

    <?php if (empty($approved)): ?>
      <p>No approved alumni found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course / Year</th>
            <th>Email</th>
            <th>Approved On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($approved as $a): ?>
          <tr>
            <td><?= $a['id']; ?></td>
            <td><?= htmlspecialchars($a['surname'] . ', ' . $a['given_name']); ?></td>
            <td><?= htmlspecialchars($a['course_year']); ?></td>
            <td><?= htmlspecialchars($a['email']); ?></td>
            <td><?= htmlspecialchars($a['created_at']); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= $a['id']; ?>&from=active">View</a>
              <a href="../functions/archive.php?id=<?= $a['id']; ?>" onclick="return confirm('Archive this alumni record? This will move it to the archive list.')">Archive</a>
=======
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
>>>>>>> Stashed changes
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<<<<<<< Updated upstream
  <!-- =======================
       ARCHIVED ALUMNI
  ======================= -->
  <section>
    <h2>Archived Alumni</h2>

=======
  <!-- ARCHIVED -->
  <section>
    <h2 style="color:#b30000;">Archived Alumni</h2>
>>>>>>> Stashed changes
    <?php if (empty($archived)): ?>
      <p>No archived alumni found.</p>
    <?php else: ?>
      <table>
<<<<<<< Updated upstream
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course / Year</th>
            <th>Email</th>
            <th>Archived On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($archived as $ar): ?>
          <tr>
            <td><?= $ar['id']; ?></td>
            <td><?= htmlspecialchars($ar['surname'] . ', ' . $ar['given_name']); ?></td>
            <td><?= htmlspecialchars($ar['course_year']); ?></td>
            <td><?= htmlspecialchars($ar['email']); ?></td>
            <td><?= htmlspecialchars($ar['created_at']); ?></td>
            <td class="actions">
              <a href="../pages/viewPending.php?id=<?= $ar['id']; ?>&from=archive">View</a>
              <a href="../functions/restore.php?id=<?= $ar['id']; ?>" onclick="return confirm('Restore this alumni back to Approved list?')">Restore</a>
=======
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
>>>>>>> Stashed changes
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
