<?php
// ==========================================================
// pages/adminAnalytics.php — Colleges-Alumni Analytics Dashboard (Unified Schema)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();
require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// 🧠 Fetch Admin Full Name (from database)
$adminUsername = $_SESSION['admin_username'] ?? '';
$adminFullName = 'Administrator';
if ($adminUsername !== '') {
    $stmtAdmin = $pdo->prepare("SELECT full_name FROM admin_account WHERE username = :username LIMIT 1");
    $stmtAdmin->execute([':username' => $adminUsername]);
    $adminFullName = $stmtAdmin->fetchColumn() ?: 'Administrator';
}

// === STATS (Unified Tables) ===

// Total Alumni
$totalAlumni = (int)$pdo->query("SELECT COUNT(*) FROM colleges_alumni")->fetchColumn();

// Employed (has company_name)
$employed = (int)$pdo->query("
    SELECT COUNT(*) FROM alumni_emp_record 
    WHERE company_name IS NOT NULL AND TRIM(company_name) <> ''
")->fetchColumn();
$unemployed = $totalAlumni - $employed;

// Status Distribution (from alumni_info)
$statusCounts = $pdo->query("
    SELECT status, COUNT(*) AS cnt
    FROM alumni_info
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Alumni by Course
$courses = $pdo->query("
    SELECT COALESCE(ca.course, 'Unknown') AS course, COUNT(*) AS cnt
    FROM colleges_alumni ca
    GROUP BY course
    ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Top 5 Graduation Years
$gradYears = $pdo->query("
    SELECT ca.year_graduated AS year_grad, COUNT(*) AS cnt
    FROM colleges_alumni ca
    WHERE ca.year_graduated IS NOT NULL
    GROUP BY year_grad
    ORDER BY year_grad DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Top 5 Cities
$cities = $pdo->query("
    SELECT ai.city_municipality AS city, COUNT(*) AS cnt
    FROM alumni_info ai
    WHERE ai.city_municipality IS NOT NULL AND TRIM(ai.city_municipality) <> ''
    GROUP BY city
    ORDER BY cnt DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Blood Type Distribution
$bloodTypes = $pdo->query("
    SELECT ai.blood_type, COUNT(*) AS cnt
    FROM alumni_info ai
    WHERE ai.blood_type IS NOT NULL AND TRIM(ai.blood_type) <> ''
    GROUP BY ai.blood_type
    ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Analytics — CCS Alumni Directory</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../assets/css/styles.css">
<style>
body { background:#fff5f5; font-family: Arial, sans-serif; }
.mainContainer {
  max-width:1250px; margin:40px auto; background:white;
  border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.08); padding:30px 40px;
}
header {
  display:flex; justify-content:space-between; align-items:center;
  border-bottom:3px solid #dc3545; padding-bottom:10px; margin-bottom:25px;
}
header h1 { color:#b30000; margin:0; }
.nav-links a { color:#b30000; text-decoration:none; margin-left:12px; font-weight:bold; }
.cards {
  display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap:20px; margin-bottom:40px;
}
.card {
  background:#fff; border-left:6px solid #dc3545; border:1px solid #f5c2c2;
  padding:20px; border-radius:8px; box-shadow:0 3px 8px rgba(0,0,0,0.05);
}
.card h3 { color:#b30000; margin:0 0 8px; font-size:1.1em; }
.card .value { font-size:1.8em; font-weight:bold; color:#333; }
.chart-grid {
  display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap:25px; justify-items:center; margin-top:20px;
}
.chart-box {
  background:#fff; border:1px solid #f0b3b3; border-radius:10px;
  box-shadow:0 4px 10px rgba(0,0,0,0.05);
  padding:15px 20px 25px 20px; text-align:center;
  width:100%; max-width:370px;
}
.chart-box h2 {
  color:#b30000; font-size:1em; margin-bottom:10px;
  border-bottom:1px solid #f2dcdc; padding-bottom:5px;
}
.chart-box canvas { width:100% !important; height:230px !important; }
footer {
  text-align:center; color:#888; font-size:0.9em; margin-top:40px;
}
</style>
</head>
<body>
<div class="mainContainer">
  <header>
    <h1>📊 Alumni Analytics Overview</h1>
    <div class="nav-links">
      Logged in as <strong><?= htmlspecialchars($adminFullName) ?></strong>
      <a href="adminDashboard.php">🏠 Dashboard</a>
      <a href="../classes/logout.php">🚪 Logout</a>
    </div>
  </header>

  <!-- SUMMARY CARDS -->
  <div class="cards">
    <div class="card"><h3>Total Alumni</h3><div class="value"><?= $totalAlumni ?></div></div>
    <div class="card"><h3>Employed</h3><div class="value"><?= $employed ?></div></div>
    <div class="card"><h3>Unemployed</h3><div class="value"><?= $unemployed ?></div></div>
    <div class="card"><h3>Active Alumni</h3><div class="value"><?= $statusCounts['active'] ?? 0 ?></div></div>
    <div class="card"><h3>Pending Applications</h3><div class="value"><?= $statusCounts['pending'] ?? 0 ?></div></div>
    <div class="card"><h3>Rejected / Archived</h3><div class="value"><?= ($statusCounts['archived'] ?? 0) + ($statusCounts['rejected'] ?? 0) ?></div></div>
  </div>

  <!-- GRID OF CHARTS -->
  <div class="chart-grid">
    <div class="chart-box"><h2>Employment Overview</h2><canvas id="employmentChart"></canvas></div>
    <div class="chart-box"><h2>Alumni by Course</h2><canvas id="courseChart"></canvas></div>
    <div class="chart-box"><h2>Alumni by Status</h2><canvas id="statusChart"></canvas></div>
    <div class="chart-box"><h2>Top 5 Graduation Years</h2><canvas id="gradChart"></canvas></div>
    <div class="chart-box"><h2>Top 5 Cities</h2><canvas id="cityChart"></canvas></div>
    <div class="chart-box"><h2>Blood Type Distribution</h2><canvas id="bloodChart"></canvas></div>
  </div>

  <footer>© <?= date('Y') ?> CCS Alumni Directory — Analytics Dashboard</footer>
</div>

<script>
new Chart(document.getElementById('employmentChart'), {
  type: 'doughnut',
  data: { labels: ['Employed', 'Unemployed'], datasets: [{ data: [<?= $employed ?>, <?= $unemployed ?>], backgroundColor: ['#28a745','#dc3545'] }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('courseChart'), {
  type: 'pie',
  data: { labels: <?= json_encode(array_column($courses, 'course')) ?>,
          datasets: [{ data: <?= json_encode(array_column($courses, 'cnt')) ?>, backgroundColor:['#b30000','#dc3545','#ff6b6b','#ffb3b3'] }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('statusChart'), {
  type: 'pie',
  data: { labels: <?= json_encode(array_keys($statusCounts)) ?>,
          datasets: [{ data: <?= json_encode(array_values($statusCounts)) ?>, backgroundColor:['#ffc107','#28a745','#dc3545','#6c757d'] }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('gradChart'), {
  type: 'bar',
  data: { labels: <?= json_encode(array_column($gradYears, 'year_grad')) ?>,
          datasets: [{ label:'Graduates', data: <?= json_encode(array_column($gradYears, 'cnt')) ?>, backgroundColor:'#b30000' }] },
  options: { scales: { y: { beginAtZero:true } } }
});

new Chart(document.getElementById('cityChart'), {
  type: 'bar',
  data: { labels: <?= json_encode(array_column($cities, 'city')) ?>,
          datasets: [{ label:'Alumni Count', data: <?= json_encode(array_column($cities, 'cnt')) ?>, backgroundColor:'#dc3545' }] },
  options: { scales: { y: { beginAtZero:true } } }
});

new Chart(document.getElementById('bloodChart'), {
  type: 'bar',
  data: { labels: <?= json_encode(array_column($bloodTypes, 'blood_type')) ?>,
          datasets: [{ label:'Alumni Count', data: <?= json_encode(array_column($bloodTypes, 'cnt')) ?>, backgroundColor:'#ff4d4d' }] },
  options: { scales: { y: { beginAtZero:true } } }
});
</script>
</body>
</html>
