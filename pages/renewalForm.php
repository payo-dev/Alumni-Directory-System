<?php
// ==========================================================
// pages/renewalForm.php — Pre-filled Renewal Form
// ==========================================================
require_once __DIR__ . '/../classes/database.php';

$form = $_SESSION['form_data'] ?? [];

if (empty($form)) {
    echo "<p style='text-align:center; color:#dc3545;'>No record loaded. Please verify your email first.</p>";
    echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Go Back</a></p>";
    exit;
}

?>

<div class="renewal-container">
  <h1>CCS Alumni Renewal Form</h1>
  <p class="instruction">
    Please review and update your details below. Fields marked as optional only need updates if information has changed.
  </p>

  <form action="../functions/renewalSubmit.php" method="POST" class="renewal-form">
    <fieldset>
      <legend>Personal Information</legend>

      <div class="form-group">
        <label>Student ID</label>
        <input type="text" name="student_id" value="<?= htmlspecialchars($form['student_id']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" 
               value="<?= htmlspecialchars($form['surname'] . ', ' . $form['given_name'] . ' ' . $form['middle_name']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>Contact Number (Update if changed)</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($form['contact_number'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Email (Read Only)</label>
        <input type="email" name="email" value="<?= htmlspecialchars($form['email'] ?? '') ?>" readonly>
      </div>

      <div class="form-group">
        <label>Address (Update if changed)</label>
        <input type="text" name="region" placeholder="Region" value="<?= htmlspecialchars($form['region'] ?? '') ?>">
        <input type="text" name="province" placeholder="Province" value="<?= htmlspecialchars($form['province'] ?? '') ?>">
        <input type="text" name="city_municipality" placeholder="City / Municipality" value="<?= htmlspecialchars($form['city_municipality'] ?? '') ?>">
        <input type="text" name="barangay" placeholder="Barangay" value="<?= htmlspecialchars($form['barangay'] ?? '') ?>">
      </div>
    </fieldset>

    <fieldset>
      <legend>Employment (Optional)</legend>

      <div class="form-group">
        <label>Company Name</label>
        <input type="text" name="company_name" value="<?= htmlspecialchars($form['company_name'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Position</label>
        <input type="text" name="position" value="<?= htmlspecialchars($form['position'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Company Address</label>
        <input type="text" name="company_address" value="<?= htmlspecialchars($form['company_address'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Company Contact</label>
        <input type="text" name="company_contact" value="<?= htmlspecialchars($form['company_contact'] ?? '') ?>">
      </div>
    </fieldset>

    <fieldset>
  <legend>Emergency Contact (Optional)</legend>

  <div class="form-group">
    <label>Contact Name</label>
    <input type="text" name="emergency_name" value="<?= htmlspecialchars($form['emergency_name'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Address</label>
    <input type="text" name="emergency_address" value="<?= htmlspecialchars($form['emergency_address'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label>Contact Number</label>
    <input type="text" name="emergency_contact" value="<?= htmlspecialchars($form['emergency_contact'] ?? '') ?>">
  </div>
</fieldset>


    <button type="submit" class="submit-btn">✅ Submit Renewal</button>
    <a href="../pages/viewCurrentRecord.php?student_id=<?= urlencode($form['student_id']) ?>" class="view-btn">👁️ View Current Record</a>
  </form>
</div>

<style>
.renewal-container {
  max-width: 800px;
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
}
.instruction {
  text-align: center;
  color: #555;
  margin-bottom: 25px;
}
fieldset {
  border: 1px solid #198754;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}
legend {
  color: #198754;
  font-weight: bold;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  color: #198754;
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}
.form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.submit-btn {
  background: #198754;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 6px;
  font-size: 1em;
  cursor: pointer;
}
.submit-btn:hover {
  background: #157347;
}
.view-btn {
  display: inline-block;
  margin-left: 10px;
  color: #198754;
  text-decoration: none;
}
.view-btn:hover {
  text-decoration: underline;
}
</style>
