<?php
// ==========================================================
// pages/reviewSubmit.php — Final Review & Submission Page
// ==========================================================

// Session already started in index.php
$form = $_SESSION['form_data'] ?? [];

if (empty($form)) {
  echo "<p style='text-align:center; color:#dc3545;'>No form data found. Please fill out the previous sections first.</p>";
  exit;
}
?>

<div class="review-container">
  <h1>Review Your Application</h1>
  <p class="instruction">Please review your complete application before submitting. You may go back to edit any section if needed.</p>

  <!-- ========================== -->
  <!-- PERSONAL INFORMATION CARD -->
  <!-- ========================== -->
  <div class="review-card">
    <h2>Personal Information</h2>
    <ul>
      <li><strong>Student ID:</strong> <?= htmlspecialchars($form['student_id'] ?? '') ?></li>
      <li><strong>Full Name:</strong> <?= htmlspecialchars(($form['surname'] ?? '') . ', ' . ($form['given_name'] ?? '') . ' ' . ($form['middle_name'] ?? '')) ?></li>
      <li><strong>Course:</strong> <?= htmlspecialchars($form['course'] ?? '') ?></li>
      <li><strong>Year Graduated:</strong> <?= htmlspecialchars($form['year_graduated'] ?? '') ?></li>
      <li><strong>Batch:</strong> <?= htmlspecialchars($form['batch_name'] ?? '') ?></li>
      <li><strong>Address:</strong>
        <?= htmlspecialchars(($form['region'] ?? '') . ', ' . ($form['province'] ?? '') . ', ' . ($form['city_municipality'] ?? '') . ', ' . ($form['barangay'] ?? '')) ?>
      </li>
      <li><strong>Contact No.:</strong> <?= htmlspecialchars($form['contact_number'] ?? '') ?></li>
      <li><strong>Email:</strong> <?= htmlspecialchars($form['email'] ?? '') ?></li>
      <li><strong>Birthday:</strong> <?= htmlspecialchars($form['birthday'] ?? '') ?></li>
      <li><strong>Blood Type:</strong> <?= htmlspecialchars($form['blood_type'] ?? '') ?></li>
    </ul>
  </div>

  <!-- ========================== -->
  <!-- EDUCATIONAL BACKGROUND -->
  <!-- ========================== -->
  <div class="review-card">
    <h2>Educational Background</h2>
    <ul>
      <li><strong>Elementary:</strong> <?= htmlspecialchars($form['elemSchool'] ?? '') ?> (<?= htmlspecialchars($form['elemYear'] ?? '') ?>)</li>
      <li><strong>Junior High:</strong> <?= htmlspecialchars($form['hsSchool'] ?? '') ?> (<?= htmlspecialchars($form['hsYear'] ?? '') ?>)</li>
      <li><strong>Senior High:</strong> <?= htmlspecialchars($form['shSchool'] ?? '') ?> (<?= htmlspecialchars($form['shYear'] ?? '') ?>)</li>
      <li><strong>Tertiary:</strong> <?= htmlspecialchars($form['degree'] ?? '') ?> (<?= htmlspecialchars($form['collegeYear'] ?? '') ?>)</li>
      <li><strong>Graduate School:</strong> <?= htmlspecialchars($form['gradSchool'] ?? '') ?> (<?= htmlspecialchars($form['gradYear'] ?? '') ?>)</li>
    </ul>
  </div>

  <!-- ========================== -->
  <!-- EMPLOYMENT RECORD -->
  <!-- ========================== -->
  <div class="review-card">
    <h2>Employment Record</h2>
    <ul>
      <li><strong>Company Name:</strong> <?= htmlspecialchars($form['company_name'] ?? $form['companyName'] ?? '') ?></li>
      <li><strong>Position:</strong> <?= htmlspecialchars($form['position'] ?? $form['jobTitle'] ?? '') ?></li>
      <li><strong>Address:</strong> <?= htmlspecialchars($form['company_address'] ?? $form['companyAddress'] ?? '') ?></li>
      <li><strong>Contact No.:</strong> <?= htmlspecialchars($form['company_contact'] ?? $form['companyContact'] ?? '') ?></li>
    </ul>
  </div>

  <!-- ========================== -->
  <!-- EMERGENCY CONTACT (OPTIONAL) -->
  <!-- ========================== -->
  <div class="review-card">
    <h2>Emergency Contact (Optional)</h2>
    <ul>
      <li><strong>Name:</strong> <?= htmlspecialchars($form['emergency_name'] ?? $form['emergencyName'] ?? '') ?></li>
      <li><strong>Address:</strong> <?= htmlspecialchars($form['emergency_address'] ?? $form['emergencyAddress'] ?? '') ?></li>
      <li><strong>Contact No.:</strong> <?= htmlspecialchars($form['emergency_contact'] ?? $form['emergencyContact'] ?? '') ?></li>
    </ul>
  </div>

  <!-- ========================== -->
  <!-- SUBMIT BUTTON -->
  <!-- ========================== -->
  <form action="/cssAlumniDirectorySystem/functions/submitForm.php" method="POST" class="submit-form">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($form['student_id'] ?? '') ?>">
    <input type="hidden" name="course" value="<?= htmlspecialchars($form['course'] ?? '') ?>">
    <input type="hidden" name="year_graduated" value="<?= htmlspecialchars($form['year_graduated'] ?? '') ?>">

    <button type="submit" class="submit-btn">✅ Submit Application</button>
    <a href="../index.php" class="back-btn" onclick="return confirmBack();">← Go Back to Landing Page</a>
  </form>
</div>

<script>
function confirmBack() {
  return confirm("Go back to the Landing Page? The form will not be saved unless you submit it.");
}
</script>

<style>
/* ========================== */
/* 🎨 Green-Themed Review Page */
/* ========================== */
.review-container {
  max-width: 900px;
  margin: 40px auto;
  background: #f8fff8;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  font-family: Arial, sans-serif;
}

.review-container h1 {
  color: #198754;
  text-align: center;
  font-size: 1.8em;
  margin-bottom: 10px;
}

.instruction {
  text-align: center;
  color: #555;
  margin-bottom: 25px;
}

.review-card {
  background: #ffffff;
  border-left: 6px solid #198754;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 0 5px rgba(0,0,0,0.05);
}

.review-card h2 {
  color: #198754;
  margin-bottom: 10px;
  font-size: 1.2em;
}

.review-card ul {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.review-card li {
  padding: 5px 0;
  border-bottom: 1px solid #eaeaea;
}

.review-card li strong {
  color: #198754;
}

.submit-form {
  text-align: center;
  margin-top: 30px;
}

.submit-btn {
  background: #198754;
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 6px;
  font-size: 1.1em;
  cursor: pointer;
  transition: background 0.3s;
}

.submit-btn:hover {
  background: #157347;
}

.back-btn {
  display: inline-block;
  margin-top: 15px;
  color: #dc3545;
  text-decoration: none;
  font-weight: bold;
}

.back-btn:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .review-container {
    padding: 20px;
  }

  .review-card {
    padding: 15px;
  }

  .review-card h2 {
    font-size: 1.1em;
  }

  .submit-btn {
    width: 100%;
  }
}
</style>
