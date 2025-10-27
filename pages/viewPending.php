<?php
// ==========================================================
// pages/viewPending.php — View detailed pending alumni info
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';

Auth::restrict();
$pdo = Database::getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("<h3>Invalid record ID.</h3>");

$stmt = $pdo->prepare("SELECT * FROM pending_alumni WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) die("<h3>No record found with that ID.</h3>");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Pending Alumni</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <style>
    body { background: #f7f7f7; font-family: Arial, sans-serif; color: #333; }
    .container {
      width: 80%; margin: 40px auto; background: #fff; padding: 20px 40px;
      border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h1 { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td {
      padding: 10px; border: 1px solid #ddd; text-align: left; vertical-align: top;
    }
    th { background: #f2f2f2; width: 30%; }
    .actions { margin-top: 20px; text-align: center; }
    .actions a {
      margin: 0 10px; padding: 10px 20px; border-radius: 5px;
      text-decoration: none; color: white; transition: background 0.3s;
    }
    .approve { background: #28a745; }
    .approve:hover { background: #218838; }
    .reject { background: #dc3545; }
    .reject:hover { background: #c82333; }
    .back { background: #6c757d; }
    .back:hover { background: #5a6268; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Pending Alumni Record</h1>

    <table>
      <tbody>
        <?php foreach ($record as $key => $value): ?>
          <tr>
            <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></th>
            <td><?= htmlspecialchars($value ?? '—'); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="actions">
      <a href="../functions/approve.php?id=<?= $record['id']; ?>"
         class="approve"
         onclick="return confirm('✅ Approve this applicant?');">Approve</a>

      <a href="../functions/reject.php?id=<?= $record['id']; ?>"
         class="reject"
         onclick="return confirm('⚠️ Reject this applicant and move to Archive?');">Reject</a>

      <a href="../pages/adminDashboard.php" class="back">Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
