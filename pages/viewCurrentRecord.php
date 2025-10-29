<?php
// ==========================================================
// pages/viewCurrentRecord.php — Display Existing Alumni Record
// ==========================================================
require_once __DIR__ . '/../classes/database.php';

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    echo "<p style='text-align:center; color:#dc3545;'>No student ID provided.</p>";
    echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Back</a></p>";
    exit;
}

$pdo = Database::getPDO();
$stmt = $pdo->prepare("
    SELECT a.*, c.course, c.year_graduated
    FROM alumni a
    LEFT JOIN ccs_alumni c ON a.student_id = c.student_id
    WHERE a.student_id = :student_id
");
$stmt->execute([':student_id' => $studentId]);
$alumni = $stmt->fetch();

if (!$alumni) {
    echo "<p style='text-align:center; color:#dc3545;'>Record not found for this Student ID.</p>";
    echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Back</a></p>";
    exit;
}
?>

<div class="record-container">
  <h1>Current Alumni Record</h1>
  <p class="instruction">Below is your current information in our database.</p>

  <div class="record-card">
    <h2>Personal Information</h2>
    <ul>
      <li><strong>Student ID:</strong> <?= htmlspecialchars($alumni['student_id']) ?></li>
      <li><strong>Full Name:</strong> <?= htmlspecialchars($alumni['surname'] . ', ' . $alumni['given_name'] . ' ' . ($alumni['middle_name'] ?? '')) ?></li>
      <li><strong>Course:</strong> <?= htmlspecialchars($alumni['course'] ?? 'N/A') ?></li>
      <li><strong>Year Graduated:</strong> <?= htmlspecialchars($alumni['year_graduated'] ?? 'N/A') ?></li>
      <li><strong>Batch Name:</strong> <?= htmlspecialchars($alumni['batch_name'] ?? 'N/A') ?></li>
      <li><strong>Address:</strong> 
        <?= htmlspecialchars(($alumni['region'] ?? '') . ', ' . ($alumni['province'] ?? '') . ', ' . ($alumni['city_municipality'] ?? '') . ', ' . ($alumni['barangay'] ?? '')) ?>
      </li>
      <li><strong>Contact Number:</strong> <?= htmlspecialchars($alumni['contact_number'] ?? '') ?></li>
      <li><strong>Email:</strong> <?= htmlspecialchars($alumni['email'] ?? '') ?></li>
      <li><strong>Birthday:</strong> <?= htmlspecialchars($alumni['birthday'] ?? '') ?></li>
      <li><strong>Blood Type:</strong> <?= htmlspecialchars($alumni['blood_type'] ?? '') ?></li>
    </ul>
  </div>

  <div class="record-card">
    <h2>Educational Background</h2>
    <ul>
      <li><strong>Elementary:</strong> <?= htmlspecialchars($alumni['elementary_school'] ?? '') ?> (<?= htmlspecialchars($alumni['elementary_yr'] ?? '') ?>)</li>
      <li><strong>Junior High:</strong> <?= htmlspecialchars($alumni['junior_high_school'] ?? '') ?> (<?= htmlspecialchars($alumni['junior_high_yr'] ?? '') ?>)</li>
      <li><strong>Senior High:</strong> <?= htmlspecialchars($alumni['senior_high_school'] ?? '') ?> (<?= htmlspecialchars($alumni['senior_high_yr'] ?? '') ?>)</li>
      <li><strong>Tertiary:</strong> <?= htmlspecialchars($alumni['tertiary_school'] ?? '') ?> (<?= htmlspecialchars($alumni['tertiary_yr'] ?? '') ?>)</li>
      <li><strong>Graduate School:</strong> <?= htmlspecialchars($alumni['graduate_school'] ?? '') ?> (<?= htmlspecialchars($alumni['graduate_yr'] ?? '') ?>)</li>
    </ul>
  </div>

  <div class="record-card">
    <h2>Employment Record</h2>
    <ul>
      <li><strong>Company Name:</strong> <?= htmlspecialchars($alumni['company_name'] ?? '') ?></li>
      <li><strong>Position:</strong> <?= htmlspecialchars($alumni['position'] ?? '') ?></li>
      <li><strong>Company Address:</strong> <?= htmlspecialchars($alumni['company_address'] ?? '') ?></li>
      <li><strong>Company Contact:</strong> <?= htmlspecialchars($alumni['company_contact'] ?? '') ?></li>
    </ul>
  </div>

  <div class="record-card">
    <h2>Emergency Contact</h2>
    <ul>
      <li><strong>Name:</strong> <?= htmlspecialchars($alumni['emergency_name'] ?? '') ?></li>
      <li><strong>Address:</strong> <?= htmlspecialchars($alumni['emergency_address'] ?? '') ?></li>
      <li><strong>Contact No.:</strong> <?= htmlspecialchars($alumni['emergency_contact'] ?? '') ?></li>
    </ul>
  </div>

  <div class="btn-group">
    <a href="renewalForm.php?student_id=<?= urlencode($alumni['student_id']) ?>" class="renew-btn">← Back to Renewal Form</a>
  </div>
</div>

<style>
.record-container {
  max-width: 900px;
  margin: 40px auto;
  background: #f8fff8;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  font-family: Arial, sans-serif;
}
h1 {
  color: #198754;
  text-align: center;
  margin-bottom: 10px;
}
.instruction {
  text-align: center;
  color: #555;
  margin-bottom: 20px;
}
.record-card {
  background: white;
  border-left: 6px solid #198754;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 0 5px rgba(0,0,0,0.05);
}
.record-card h2 {
  color: #198754;
  margin-bottom: 10px;
}
.record-card ul {
  list-style: none;
  padding-left: 0;
  margin: 0;
}
.record-card li {
  padding: 5px 0;
  border-bottom: 1px solid #eaeaea;
}
.record-card li strong {
  color: #198754;
}
.btn-group {
  text-align: center;
  margin-top: 25px;
}
.renew-btn {
  background: #198754;
  color: white;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 6px;
}
.renew-btn:hover {
  background: #157347;
}
</style>
