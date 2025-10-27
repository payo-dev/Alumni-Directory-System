<?php
// ==========================================================
//  ADMIN DASHBOARD (Red Theme + Name/Course Display)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// ✅ Flash message
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// ✅ Search
$search = $_GET['search'] ?? '';
$searchQuery = "";  
$params = [];

if (!empty($search)) {
    $searchQuery = "AND (surname LIKE :search OR given_name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

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
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
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

  <?php if ($flash): ?>
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
        <?php endif; ?>
      </form>
    </div>

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
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

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
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <!-- =======================
       ARCHIVED ALUMNI
  ======================= -->
  <section>
    <h2>Archived Alumni</h2>

    <?php if (empty($archived)): ?>
      <p>No archived alumni found.</p>
    <?php else: ?>
      <table>
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
