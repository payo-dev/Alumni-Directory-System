<?php
// pages/viewPending.php
require_once __DIR__ . '/../functions/auth.php';
requireAdmin();
require_once __DIR__ . '/../classes/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /pages/adminDashboard.php");
    exit;
}

$pdo = Database::getPDO();
$stmt = $pdo->prepare("SELECT * FROM pending_alumni WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$rec = $stmt->fetch();

if (!$rec) {
    echo "Record not found.";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Submission #<?php echo $rec['id']; ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<?php if (file_exists(__DIR__ . '/../includes/header.php')) include_once __DIR__ . '/../includes/header.php'; ?>

<div class="mainContainer">
  <h1>Submission Details</h1>

  <p><strong>ID:</strong> <?php echo $rec['id']; ?></p>
  <p><strong>Name:</strong> <?php echo htmlspecialchars($rec['surname'] . ', ' . $rec['given_name'] . ' ' . $rec['middle_name']); ?></p>
  <p><strong>Type:</strong> <?php echo htmlspecialchars($rec['type_of_application']); ?></p>
  <p><strong>Student ID:</strong> <?php echo htmlspecialchars($rec['student_id']); ?></p>
  <p><strong>Batch:</strong> <?php echo htmlspecialchars($rec['batch_name']); ?></p>
  <p><strong>Course/Year:</strong> <?php echo htmlspecialchars($rec['course_year']); ?></p>
  <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($rec['present_address'])); ?></p>
  <p><strong>Contact:</strong> <?php echo htmlspecialchars($rec['contact_number']); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($rec['email']); ?></p>
  <p><strong>Birthday:</strong> <?php echo htmlspecialchars($rec['birthday']); ?></p>

  <?php if (!empty($rec['picture_path'])): ?>
    <p><img src="<?php echo htmlspecialchars($rec['picture_path']); ?>" alt="Picture" style="max-width:200px;"></p>
  <?php endif; ?>

  <div style="margin-top:12px">
    <a href="/functions/approve.php?id=<?php echo $rec['id']; ?>" onclick="return confirm('Approve this submission?')">Approve</a> |
    <a href="/functions/reject.php?id=<?php echo $rec['id']; ?>" onclick="return confirm('Reject this submission?')">Reject</a> |
    <a href="/pages/adminDashboard.php">Back</a>
  </div>
</div>

<?php if (file_exists(__DIR__ . '/../includes/footer.php')) include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
