<?php
// ==========================================================
// pages/adminDashboard.php — Colleges-Alumni Smart Dashboard (Fixed + Unified)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();
require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// flash message
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// search + filter params
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? 'All');
$statusFilter = trim($_GET['status'] ?? 'All');

// pagination setup (independent per section)
$limit = 5;
$pendingPage  = max(1, intval($_GET['pending_page'] ?? 1));
$activePage   = max(1, intval($_GET['active_page'] ?? 1));
$archivedPage = max(1, intval($_GET['archived_page'] ?? 1));

$pendingOffset  = ($pendingPage - 1) * $limit;
$activeOffset   = ($activePage - 1) * $limit;
$archivedOffset = ($archivedPage - 1) * $limit;

$searchQuery = "";
$params = [];

/* ==========================================================
   SMART SEARCH LOGIC
   ========================================================== */
$searchLower = strtolower($search);
if ($search !== '') {
    if ($searchLower === 'employed') {
        $searchQuery .= " AND (er.company_name IS NOT NULL AND TRIM(er.company_name) <> '')";
    } elseif ($searchLower === 'unemployed') {
        $searchQuery .= " AND (er.company_name IS NULL OR TRIM(er.company_name) = '')";
    } elseif (preg_match('/^(a|b|ab|o)[+-]$/i', $searchLower)) {
        $searchQuery .= " AND ai.blood_type = :blood";
        $params[':blood'] = strtoupper($search);
    } elseif (preg_match('/^\d{4}$/', $searchLower)) {
        $searchQuery .= " AND (ca.year_graduated = :yr)";
        $params[':yr'] = $search;
    } else {
        $searchQuery .= " AND (
            ca.student_id LIKE :s OR
            ca.surname LIKE :s OR
            ca.firstname LIKE :s OR
            ai.email LIKE :s OR
            ai.region LIKE :s OR
            ai.province LIKE :s OR
            ai.city_municipality LIKE :s OR
            ai.barangay LIKE :s OR
            ai.birthday LIKE :s OR
            ai.blood_type LIKE :s OR
            er.company_name LIKE :s OR
            er.position LIKE :s OR
            er.company_address LIKE :s OR
            ca.course LIKE :s OR
            ca.year_graduated LIKE :s
        )";
        $params[':s'] = "%$search%";
    }
}

if ($courseFilter !== '' && $courseFilter !== 'All') {
    $searchQuery .= " AND (COALESCE(ca.course, '') = :course)";
    $params[':course'] = $courseFilter;
}
if ($statusFilter !== '' && $statusFilter !== 'All') {
    $searchQuery .= " AND ai.status = :status";
    $params[':status'] = $statusFilter;
}

/* ==========================================================
   ANALYTICS COUNTS
   ========================================================== */
$totalAlumni = (int) $pdo->query("SELECT COUNT(*) FROM colleges_alumni")->fetchColumn();
$employed = (int) $pdo->query("SELECT COUNT(*) FROM alumni_emp_record WHERE company_name IS NOT NULL AND TRIM(company_name) <> ''")->fetchColumn();
$unemployed = $totalAlumni - $employed;

$statusCounts = [];
foreach ($pdo->query("SELECT status, COUNT(*) AS cnt FROM alumni_info GROUP BY status") as $r) {
    $statusCounts[$r['status']] = (int)$r['cnt'];
}

/* ==========================================================
   FETCH STATUS LISTS (with pagination)
   ========================================================== */
function fetchPaginated($pdo, $status, $searchQuery, $params, $limit, $offset, $includeRejected = false) {
    $statusCondition = $includeRejected
        ? "IN ('archived', 'rejected')"
        : "= :status";

    $sql = "
        SELECT 
            ca.student_id, ca.surname, ca.firstname, ca.course, ca.year_graduated,
            ai.email, ai.status, ai.validated_date, ai.created_at, ai.renewal_status
        FROM colleges_alumni ca
        LEFT JOIN alumni_info ai ON ai.student_id = ca.student_id
        LEFT JOIN alumni_emp_record er ON er.student_id = ca.student_id
        WHERE ai.status {$statusCondition} {$searchQuery}
        ORDER BY ai.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) $stmt->bindValue($key, $val);
    if (!$includeRejected) $stmt->bindValue(':status', $status);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalRows($pdo, $status, $searchQuery, $params, $includeRejected = false) {
    $statusCondition = $includeRejected
        ? "IN ('archived', 'rejected')"
        : "= :status";

    $sql = "
        SELECT COUNT(*) 
        FROM colleges_alumni ca
        LEFT JOIN alumni_info ai ON ai.student_id = ca.student_id
        LEFT JOIN alumni_emp_record er ON er.student_id = ca.student_id
        WHERE ai.status {$statusCondition} {$searchQuery}
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) $stmt->bindValue($key, $val);
    if (!$includeRejected) $stmt->bindValue(':status', $status);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

function renderPagination($totalRows, $limit, $currentPage, $status) {
    $totalPages = ceil($totalRows / $limit);
    if ($totalPages <= 1) return;

    echo "<div style='margin-top:10px; text-align:center;'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        $style = $i == $currentPage ? "background:#dc3545; color:white;" : "background:white; color:#b30000;";
        $query = http_build_query(array_merge($_GET, ["{$status}_page" => $i]));
        echo "<a href='?{$query}' style='margin:2px; padding:6px 10px; border:1px solid #dc3545; border-radius:4px; text-decoration:none; {$style}'>$i</a>";
    }
    echo "</div>";
}

// Fetch records
$pendingTotal  = getTotalRows($pdo, 'pending', $searchQuery, $params);
$activeTotal   = getTotalRows($pdo, 'active', $searchQuery, $params);
$archivedTotal = getTotalRows($pdo, 'archived', $searchQuery, $params, true);

$pending  = fetchPaginated($pdo, 'pending', $searchQuery, $params, $limit, $pendingOffset);
$active   = fetchPaginated($pdo, 'active', $searchQuery, $params, $limit, $activeOffset);
$archived = fetchPaginated($pdo, 'archived', $searchQuery, $params, $limit, $archivedOffset, true);

$courseOptions = ['All','BSCS','BSIT','ACT'];
$statusOptions = ['All','pending','active','archived','rejected'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - CCS Alumni</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body { background:#fff5f5; font-family: Arial, sans-serif; }
    .mainContainer { max-width:1200px; margin:30px auto; background:#fff; padding:22px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    header { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #dc3545; padding-bottom:12px; }
    header h1 { color:#b30000; margin:0; }
    .nav-links a { margin-left:12px; color:#dc3545; text-decoration:none; border:1px solid #dc3545; padding:4px 8px; border-radius:5px; }
    .search-bar { display:flex; gap:10px; align-items:center; justify-content:space-between; margin:18px 0; flex-wrap:wrap; }
    .search-left { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .search-left input, .search-left select { padding:6px 8px; border:1px solid #dc3545; border-radius:4px; }
    th, td { padding:10px; border:1px solid #f0b3b3; text-align:left; }
    th { background:#dc3545; color:#fff; }
    tr:nth-child(even){ background:#fff0f0; }
    .actions a { color:#b30000; font-weight:bold; text-decoration:none; margin-right:8px; }
  </style>
</head>
<body>
<div class="mainContainer">
  <header>
    <h1>Admin Dashboard</h1>
    <div class="nav-links">
<?php
// Fetch admin full name from DB using session username
$adminUsername = $_SESSION['admin_username'] ?? '';
$adminFullName = 'Administrator';
if ($adminUsername !== '') {
    $stmtAdmin = $pdo->prepare("SELECT full_name FROM admin_account WHERE username = :username LIMIT 1");
    $stmtAdmin->execute([':username' => $adminUsername]);
    $adminFullName = $stmtAdmin->fetchColumn() ?: 'Administrator';
}
?>
Logged in as <strong><?= htmlspecialchars($adminFullName); ?></strong>

      <a href="adminAnalytics.php">📈 Analytics</a>
      <a href="reportGenerator.php">🧾 Report</a>
      <a href="../index.php">🏠 Home</a>
      <a href="../classes/logout.php">🚪 Logout</a>
    </div>
  </header>

  <div class="search-bar">
    <div class="search-left">
      <form method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search alumni..." value="<?= htmlspecialchars($search); ?>">
        <select name="course">
          <?php foreach ($courseOptions as $opt): $sel = ($courseFilter === $opt) ? 'selected' : ''; ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= $sel ?>><?= htmlspecialchars($opt) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="status">
          <?php foreach ($statusOptions as $opt): $sel = ($statusFilter === $opt) ? 'selected' : ''; ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= $sel ?>><?= htmlspecialchars(ucfirst($opt)) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" style="padding:6px 10px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Filter</button>
        <?php if ($search || ($courseFilter !== 'All') || ($statusFilter !== 'All')): ?>
          <a href="adminDashboard.php" style="margin-left:8px; color:#b30000;">Reset</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <?php
  function renderTable($title, $rows, $totalRows, $limit, $currentPage, $status) {
      echo "<section><h2 style='color:#b30000; border-top:2px solid #f0b3b3; padding-top:10px;'>$title</h2>";
      if (empty($rows)) {
          echo "<p>No records found.</p></section>";
          return;
      }
      echo "<table><thead><tr>
              <th>Student ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Date</th><th>Actions</th>
            </tr></thead><tbody>";
      foreach ($rows as $r) {
          $id = urlencode($r['student_id']);
          $date = $r['validated_date'] ?? $r['created_at'] ?? '-';
          $actions = '';

          switch ($r['status']) {
              case 'pending':
                  $actions = "
                      <a href='../pages/viewPending.php?id={$id}'>View</a>
                      <a href='../functions/approve.php?id={$id}' onclick=\"return confirm('Approve this record?')\">Approve</a>
                      <a href='../functions/reject.php?id={$id}' onclick=\"return confirm('Reject and archive this record?')\">Reject</a>
                  ";
                  break;
              case 'active':
                  $actions = "
                      <a href='../pages/viewPending.php?id={$id}'>View</a>
                      <a href='../functions/archive.php?id={$id}' onclick=\"return confirm('Archive this record?')\">Archive</a>
                  ";
                  break;
              default:
                  $actions = "
                      <a href='../pages/viewPending.php?id={$id}'>View</a>
                      <a href='../functions/restore.php?id={$id}' onclick=\"return confirm('Restore this record to active?')\">Restore</a>
                  ";
          }

          echo "<tr>
                  <td>".htmlspecialchars($r['student_id'])."</td>
                  <td>".htmlspecialchars($r['surname'].', '.$r['firstname'])."</td>
                  <td>".htmlspecialchars($r['course'].' / '.$r['year_graduated'])."</td>
                  <td>".htmlspecialchars($r['email'])."</td>
                  <td>".htmlspecialchars($date)."</td>
                  <td class='actions'>{$actions}</td>
                </tr>";
      }
      echo "</tbody></table>";
      renderPagination($totalRows, $limit, $currentPage, $status);
      echo "</section>";
  }

  renderTable("Pending Applications", $pending, $pendingTotal, $limit, $pendingPage, 'pending');
  renderTable("Active Alumni", $active, $activeTotal, $limit, $activePage, 'active');
  renderTable("Rejected / Archived", $archived, $archivedTotal, $limit, $archivedPage, 'archived');
  ?>
</div>
</body>
</html>
