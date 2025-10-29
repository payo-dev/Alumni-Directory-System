<?php
// pages/viewPending.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();
$student_id = $_GET['id'] ?? null;
if (!$student_id) die("<h3>Invalid record ID.</h3>");

// ✅ FIXED: Use year_graduated instead of year_grad
$sql = "
  SELECT a.*, c.course, c.year_graduated
  FROM alumni a
  LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
  WHERE a.student_id = :student_id
  LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':student_id' => $student_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$record) die("<h3>No record found for student ID: " . htmlspecialchars($student_id) . "</h3>");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Alumni Record</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <style>
    body { background:#f7f7f7; font-family:Arial; }
    .container { width:85%; margin:30px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
    h1 { color:#b30000; text-align:center; }
    table { width:100%; border-collapse:collapse; margin-top:18px; }
    th, td { padding:10px; border:1px solid #eee; text-align:left; vertical-align:top; }
    th { background:#fafafa; width:30%; }
    .actions { margin-top:18px; text-align:center; }
    .actions a { display:inline-block; margin:0 8px; padding:10px 16px; border-radius:6px; color:#fff; text-decoration:none; }
    .approve { background:#28a745; }
    .reject { background:#dc3545; }
    .back { background:#6c757d; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Alumni Record — <?= htmlspecialchars($record['student_id']); ?></h1>

    <table>
      <tbody>
        <tr><th>Student ID</th><td><?= htmlspecialchars($record['student_id']); ?></td></tr>
        <tr><th>Name</th><td><?= htmlspecialchars($record['surname'] . ', ' . $record['given_name'] . ($record['middle_name'] ? ' ' . $record['middle_name'] : '')); ?></td></tr>
        <tr><th>Course</th><td><?= htmlspecialchars($record['course'] ?? '-'); ?></td></tr>
        <tr><th>Year Graduated</th><td><?= htmlspecialchars($record['year_graduated'] ?? '-'); ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($record['email'] ?? '-'); ?></td></tr>
        <tr><th>Contact Number</th><td><?= htmlspecialchars($record['contact_number'] ?? '-'); ?></td></tr>
        <tr>
          <th>Address</th>
          <td>
            <?= htmlspecialchars(($record['region'] ?? '') . ', ' . ($record['province'] ?? '') . ', ' . ($record['city_municipality'] ?? '') . ', ' . ($record['barangay'] ?? '')); ?>
          </td>
        </tr>
        <tr><th>Birthday</th><td><?= htmlspecialchars($record['birthday'] ?? '-'); ?></td></tr>
        <tr><th>Blood Type</th><td><?= htmlspecialchars($record['blood_type'] ?? '-'); ?></td></tr>
        <tr><th>Tertiary School</th><td><?= htmlspecialchars($record['tertiary_school'] ?? '-') . ' — ' . htmlspecialchars($record['tertiary_yr'] ?? '-'); ?></td></tr>
        <tr><th>Employment</th><td><?= nl2br(htmlspecialchars(($record['company_name'] ?? '-') . ' / ' . ($record['position'] ?? '-') . "\n" . ($record['company_address'] ?? '-') )); ?></td></tr>
        <tr><th>Emergency Contact</th><td><?= nl2br(htmlspecialchars(($record['emergency_name'] ?? '-') . "\n" . ($record['emergency_contact'] ?? '-') . "\n" . ($record['emergency_address'] ?? '-'))); ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($record['status'] ?? '-'); ?></td></tr>
        <tr><th>Issued Date</th><td><?= htmlspecialchars($record['issued_date'] ?? $record['created_at'] ?? '-'); ?></td></tr>
        <tr><th>Validated By</th><td><?= htmlspecialchars($record['validated_by'] ?? '-'); ?></td></tr>
        <tr><th>Validated Date</th><td><?= htmlspecialchars($record['validated_date'] ?? '-'); ?></td></tr>
        <tr><th>2x2 Picture</th>
          <td>
            <?php if (!empty($record['picture_path']) && file_exists(__DIR__ . '/../' . $record['picture_path'])): ?>
              <img src="/<?= htmlspecialchars($record['picture_path']); ?>" alt="2x2" style="max-width:140px;border-radius:6px;border:1px solid #ddd;">
            <?php else: ?>
              — no picture uploaded —
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="actions">
      <?php if (($record['status'] ?? '') === 'pending'): ?>
        <a href="../functions/approve.php?id=<?= urlencode($record['student_id']); ?>" class="approve" onclick="return confirm('Approve this applicant?')">Approve</a>
        <a href="../functions/reject.php?id=<?= urlencode($record['student_id']); ?>" class="reject" onclick="return confirm('Reject and archive this applicant?')">Reject</a>
      <?php endif; ?>
      <a href="../pages/adminDashboard.php" class="back">Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
