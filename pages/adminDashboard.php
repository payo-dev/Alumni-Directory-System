<?php
// ==========================================================
//  ADMIN DASHBOARD (Red Theme + Alumni_CCS Integration)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

require_once __DIR__ . '/../classes/database.php';
$pdo = Database::getPDO();

// ✅ Flash message
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

// ✅ Search logic
$search = $_GET['search'] ?? '';
$searchQuery = "";
$params = [];

if (!empty($search)) {
    $searchQuery = "AND (surname LIKE :search OR given_name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

// ✅ Fetch Pending Alumni
$stmtPending = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, created_at
    FROM alumni_ccs
    WHERE status = 'pending' $searchQuery
    ORDER BY created_at DESC
");
$stmtPending->execute($params);
$pending = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch Active Alumni
$stmtActive = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, validated_date
    FROM alumni_ccs
    WHERE status = 'active' $searchQuery
    ORDER BY validated_date DESC
");
$stmtActive->execute($params);
$active = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch Archived Alumni
$stmtArchived = $pdo->prepare("
    SELECT id, surname, given_name, course_year, email, validated_date
    FROM alumni_ccs
    WHERE status = 'archived' $searchQuery
    ORDER BY validated_date DESC
");
$stmtArchived->execute($params);
$archived = $stmtArchived->fetchAll(PDO::FETCH_ASSOC);

// ✅ Stats
$totalPending  = $pdo->query("SELECT COUNT(*) FROM alumni_ccs WHERE status = 'pending'")->fetchColumn();
$totalActive   = $pdo->query("SELECT COUNT(*) FROM alumni_ccs WHERE status = 'active'")->fetchColumn();
$totalArchived = $pdo->query("SELECT COUNT(*) FROM alumni_ccs WHERE status = 'archived'")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Alumni CCS</title>
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
    .flash-message {
        background: #ffe6e6;
        color: #a10000;
        border: 1px solid #ffcccc;
        padding: 10px;
        border-radius: 5px;
        margin: 15px 0;
        font-weight: bold;
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
      <div class="flash-message"><?= htmlspecialchars($flash); ?></div>
  <?php endif; ?>

  <div class="search-bar">
    <div class="stats">
      Pending: <?= $totalPending ?> |
      Active: <?= $totalActive ?> |
      Archived: <?= $totalArchived ?>
    </div>

    <form method="GET" style="display:flex; gap:10px;">
      <input type="text" name="search" placeholder="Search name/email"
             value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Search</button>
      <?php if (!empty($search)) : ?>
        <a href="adminDashboard.php" style="color:#b30000; text-decoration:none;">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- PENDING LIST -->
  <section>
    <h2>Pending Applications</h2>
    <?php if (empty($pending)): ?>
      <p>No pending submissions.</p>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Date Submitted</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($pending as $row): ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
          <td><?= htmlspecialchars($row['course_year']); ?></td>
          <td><?= htmlspecialchars($row['email']); ?></td>
          <td><?= htmlspecialchars($row['created_at']); ?></td>
          <td class="actions">
            <a href="../pages/viewPending.php?id=<?= $row['id']; ?>">View</a> |
            <a href="../functions/approve.php?id=<?= $row['id']; ?>" onclick="return confirm('Approve this record?')">Approve</a> |
            <a href="../functions/reject.php?id=<?= $row['id']; ?>" onclick="return confirm('Reject and archive this record?')">Reject</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>

  <!-- ACTIVE LIST -->
  <section>
    <h2>Active Alumni</h2>
    <?php if (empty($active)): ?>
      <p>No active alumni yet.</p>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Validated On</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($active as $row): ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
          <td><?= htmlspecialchars($row['course_year']); ?></td>
          <td><?= htmlspecialchars($row['email']); ?></td>
          <td><?= htmlspecialchars($row['validated_date']); ?></td>
          <td class="actions">
            <a href="../pages/viewPending.php?id=<?= $row['id']; ?>">View</a> |
            <a href="../functions/reject.php?id=<?= $row['id']; ?>" onclick="return confirm('Archive this alumni record?')">Archive</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>

  <!-- ARCHIVED LIST -->
  <section>
    <h2>Archived Alumni</h2>
    <?php if (empty($archived)): ?>
      <p>No archived alumni yet.</p>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Course / Year</th><th>Email</th><th>Archived On</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($archived as $row): ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= htmlspecialchars($row['surname'] . ', ' . $row['given_name']); ?></td>
          <td><?= htmlspecialchars($row['course_year']); ?></td>
          <td><?= htmlspecialchars($row['email']); ?></td>
          <td><?= htmlspecialchars($row['validated_date']); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>
</div>
</body>
</html>
