<?php
// ==========================================================
// pages/viewCurrentRecord.php — Enhanced Alumni Record View
// ==========================================================
require_once __DIR__ . '/../classes/database.php';

$studentId = $_GET['student_id'] ?? null;
if (!$studentId) {
  echo "<p style='text-align:center; color:#dc3545;'>No student ID provided.</p>";
  echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Back</a></p>";
  exit;
}

$pdo = Database::getPDO();

// ----------------------------------------------------------
// 1️⃣ MAIN INFO (join colleges_alumni + alumni_info)
// ----------------------------------------------------------
$sqlMain = "
  SELECT ca.*, ai.region, ai.province, ai.city_municipality, ai.barangay,
         ai.birthday, ai.blood_type, ai.picture_path
  FROM colleges_alumni ca
  LEFT JOIN alumni_info ai ON ca.student_id = ai.student_id
  WHERE ca.student_id = :student_id
";
$stmtMain = $pdo->prepare($sqlMain);
$stmtMain->execute([':student_id' => $studentId]);
$main = $stmtMain->fetch();

if (!$main) {
  echo "<p style='text-align:center; color:#dc3545;'>Record not found for this Student ID.</p>";
  echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Back</a></p>";
  exit;
}

// ----------------------------------------------------------
// 2️⃣ EDUCATION HISTORY (alumni_edu_bg)
// ----------------------------------------------------------
$sqlEdu = "SELECT * FROM alumni_edu_bg WHERE student_id = :student_id ORDER BY id ASC";
$stmtEdu = $pdo->prepare($sqlEdu);
$stmtEdu->execute([':student_id' => $studentId]);
$eduHistory = $stmtEdu->fetchAll();

// ----------------------------------------------------------
// 3️⃣ EMPLOYMENT HISTORY (alumni_emp_record)
// ----------------------------------------------------------
$sqlEmp = "SELECT * FROM alumni_emp_record WHERE student_id = :student_id ORDER BY id DESC";
$stmtEmp = $pdo->prepare($sqlEmp);
$stmtEmp->execute([':student_id' => $studentId]);
$empHistory = $stmtEmp->fetchAll();

// ----------------------------------------------------------
// 4️⃣ EMERGENCY CONTACT (alumni_emer_contact)
// ----------------------------------------------------------
$sqlEmer = "SELECT * FROM alumni_emer_contact WHERE student_id = :student_id LIMIT 1";
$stmtEmer = $pdo->prepare($sqlEmer);
$stmtEmer->execute([':student_id' => $studentId]);
$emer = $stmtEmer->fetch();
?>

<div class="record-container">
  <h1>Current Alumni Record</h1>
  <p class="instruction">Below is your information and history from our records.</p>

  <!-- PERSONAL INFORMATION -->
  <div class="record-card">
    <h2>Personal Information</h2>
    <div class="info-section">
      <?php if (!empty($main['picture_path'])): ?>
        <img src="../<?= htmlspecialchars($main['picture_path']) ?>" alt="2x2 Photo" class="profile-pic">
      <?php endif; ?>
      <ul>
        <li><strong>Student ID:</strong> <?= htmlspecialchars($main['student_id']) ?></li>
        <li><strong>Full Name:</strong> <?= htmlspecialchars($main['surname'] . ', ' . $main['given_name'] . ' ' . ($main['middle_name'] ?? '')) ?></li>
        <li><strong>Course:</strong> <?= htmlspecialchars($main['course'] ?? 'N/A') ?></li>
        <li><strong>Year Graduated:</strong> <?= htmlspecialchars($main['year_graduated'] ?? 'N/A') ?></li>
        <li><strong>Batch Name:</strong> <?= htmlspecialchars($main['batch_name'] ?? 'N/A') ?></li>
        <li><strong>Address:</strong> <?= htmlspecialchars(($main['region'] ?? '') . ', ' . ($main['province'] ?? '') . ', ' . ($main['city_municipality'] ?? '') . ', ' . ($main['barangay'] ?? '')) ?></li>
        <li><strong>Contact Number:</strong> <?= htmlspecialchars($main['contact_number'] ?? '') ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($main['email'] ?? '') ?></li>
        <li><strong>Birthday:</strong> <?= htmlspecialchars($main['birthday'] ?? '') ?></li>
        <li><strong>Blood Type:</strong> <?= htmlspecialchars($main['blood_type'] ?? '') ?></li>
      </ul>
    </div>
  </div>

  <!-- EDUCATION HISTORY -->
  <div class="record-card">
    <h2>Educational Background</h2>
    <?php if (empty($eduHistory)): ?>
      <p>No education records found.</p>
    <?php else: ?>
      <table class="history-table">
        <thead>
          <tr><th>Level</th><th>School Name</th><th>Year Graduated</th></tr>
        </thead>
        <tbody>
        <?php foreach ($eduHistory as $row): ?>
          <?php
            $entries = [
              'Elementary' => [$row['elementary_school'], $row['elementary_yr']],
              'Junior High' => [$row['junior_high_school'], $row['junior_high_yr']],
              'Senior High' => [$row['senior_high_school'], $row['senior_high_yr']],
              'Tertiary' => [$row['tertiary_school'], $row['tertiary_yr']],
              'Graduate' => [$row['graduate_school'], $row['graduate_yr']]
            ];
          ?>
          <?php foreach ($entries as $level => [$school, $year]): ?>
            <?php if (!empty($school)): ?>
              <tr>
                <td><?= htmlspecialchars($level) ?></td>
                <td><?= htmlspecialchars($school) ?></td>
                <td><?= htmlspecialchars($year ?: '-') ?></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- EMPLOYMENT HISTORY -->
  <div class="record-card">
    <h2>Employment History</h2>
    <?php if (empty($empHistory)): ?>
      <p>No employment records found.</p>
    <?php else: ?>
      <table class="history-table">
        <thead>
          <tr><th>Company Name</th><th>Position</th><th>Address</th><th>Contact</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($empHistory as $emp): ?>
            <tr class="<?= ($emp['status'] ?? '') === 'active' ? 'active-row' : 'inactive-row' ?>">
              <td><?= htmlspecialchars($emp['company_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($emp['position'] ?? '-') ?></td>
              <td><?= htmlspecialchars($emp['company_address'] ?? '-') ?></td>
              <td><?= htmlspecialchars($emp['company_contact'] ?? '-') ?></td>
              <td><?= htmlspecialchars(ucfirst($emp['status'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- EMERGENCY CONTACT -->
  <div class="record-card">
    <h2>Emergency Contact</h2>
    <?php if (!$emer): ?>
      <p>No emergency contact on record.</p>
    <?php else: ?>
      <ul>
        <li><strong>Name:</strong> <?= htmlspecialchars($emer['emergency_name'] ?? '-') ?></li>
        <li><strong>Address:</strong> <?= htmlspecialchars($emer['emergency_address'] ?? '-') ?></li>
        <li><strong>Contact No.:</strong> <?= htmlspecialchars($emer['emergency_contact'] ?? '-') ?></li>
      </ul>
    <?php endif; ?>
  </div>

  <!-- BUTTON -->
  <div class="btn-group">
    <a href="renewalForm.php?student_id=<?= urlencode($main['student_id']) ?>" class="renew-btn">← Back to Renewal Form</a>
  </div>
</div>

<style>
.record-container { max-width: 950px; margin: 40px auto; background: #f8fff8; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); font-family: Arial, sans-serif; }
h1 { color: #198754; text-align: center; margin-bottom: 10px; }
.instruction { text-align: center; color: #555; margin-bottom: 20px; }
.record-card { background: white; border-left: 6px solid #198754; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.05); }
.record-card h2 { color: #198754; margin-bottom: 10px; }
.record-card ul { list-style: none; padding-left: 0; margin: 0; }
.record-card li { padding: 5px 0; border-bottom: 1px solid #eaeaea; }
.record-card li strong { color: #198754; }
.profile-pic { float: right; width: 120px; height: 120px; border-radius: 8px; border: 2px solid #198754; object-fit: cover; margin-left: 15px; }
.history-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.history-table th, .history-table td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; font-size: 0.95em; }
.history-table th { background: #e9f7ef; color: #198754; }
.active-row { background: #e8ffed; }
.inactive-row { color: #999; background: #f9f9f9; }
.btn-group { text-align: center; margin-top: 25px; }
.renew-btn { background: #198754; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; }
.renew-btn:hover { background: #157347; }
@media (max-width: 768px) { .profile-pic { float: none; display:block; margin:0 auto 10px; } .history-table th, .history-table td { font-size: 0.9em; } }
</style>
