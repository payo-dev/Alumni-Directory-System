<?php
// ==========================================================
//  ADMIN DASHBOARD (Final Version with Homepage Link)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// ✅ Handle search filter
$search = $_GET['search'] ?? '';
$searchQuery = "";  
$params = [];

if (!empty($search)) {
    $searchQuery = "AND (surname LIKE :search OR given_name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

// ✅ Get pending submissions
$stmt = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, created_at
    FROM pending_alumni
    WHERE status = 'pending' $searchQuery
    ORDER BY created_at DESC
");
$stmt->execute($params);
$pending = $stmt->fetchAll();

// ✅ Count Pending and Approved Applicants
$totalPending = $pdo->query("SELECT COUNT(*) FROM pending_alumni WHERE status = 'pending'")->fetchColumn();
$countApproved = $pdo->query("SELECT COUNT(*) FROM pending_alumni WHERE status = 'approved'")->fetchColumn();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Alumni System</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body.default-program-bg {
        background: #f5f7fa;
        font-family: Arial, sans-serif;
    }
    .mainContainer {
        max-width: 1200px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 10px;
        border: 1px solid #e6e6e6;
        text-align: left;
    }
    th {
        background: #007bff;
        color: white;
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    .actions a {
        margin-right: 8px;
        text-decoration: none;
        color: #007bff;
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
        width: 200px;
    }
    .stats {
        font-weight: bold;
    }
    .nav-links a {
        margin-left: 12px;
        text-decoration: none;
        color: #007bff;
    }
    .nav-links a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body class="default-program-bg">

<?php if (file_exists(__DIR__ . '/../includes/header.php')) include_once __DIR__ . '/../includes/header.php'; ?>

<div class="mainContainer">
  <header>
    <h1>Admin Dashboard</h1>
    <div class="nav-links">
      Logged in as 
      <strong><?= htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Administrator'); ?></strong>
      <a href="../index.php">🏠 Home</a>
      <a href="../classes/logout.php">🚪 Logout</a>
    </div>
  </header>

  <section>
    <h2>Pending Submissions</h2>

    <!-- ✅ Counts + Search Bar -->
    <div class="search-bar">
      <div class="stats">
        Pending: <?= $totalPending ?> |
        Approved: <?= $countApproved ?>
      </div>

      <form method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" placeholder="Search name/email"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)) : ?>
          <a href="adminDashboard.php" style="text-decoration:none; margin-left:8px;">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- ✅ Data Table -->
    <?php if (empty($pending)): ?>
      <p>No pending submissions found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course / Year</th>
            <th>Email</th>
            <th>Submitted</th>
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
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>

<?php if (file_exists(__DIR__ . '/../includes/footer.php')) include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
