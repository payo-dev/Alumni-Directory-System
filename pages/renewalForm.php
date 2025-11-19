<?php
// ==========================================================
// pages/renewalForm.php — Alumni Renewal Form (Dynamic & Aligned)
// ==========================================================
require_once __DIR__ . '/../classes/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alumni = $_SESSION['form_data'] ?? [];
$studentId = $_GET['student_id'] ?? ($alumni['student_id'] ?? null);

if (!$studentId) {
    echo "<p style='text-align:center;color:#dc3545;'>No student ID found. Please verify your email again.</p>";
    echo "<p style='text-align:center;'><a href='renewalVerification.php' class='back-btn'>← Back to Verification</a></p>";
    exit;
}
?>

<div class="renewal-container">
  <h1>CCS Alumni Renewal Form</h1>
  <p class="instruction">Please review and update your latest information below.</p>

  <form method="POST" action="../functions/renewalSubmit.php" class="renewal-form">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentId) ?>">

    <!-- PERSONAL SECTION -->
    <fieldset>
      <legend>Personal Information</legend>

      <div class="two-col">
        <div class="form-group">
          <label for="surname">Last Name</label>
          <input type="text" id="surname" name="surname"
                 value="<?= htmlspecialchars($alumni['surname'] ?? '') ?>"
                 placeholder="Enter updated surname if changed">
        </div>
        <div class="form-group">
          <label for="given_name">First Name</label>
          <input type="text" id="given_name" name="given_name"
                 value="<?= htmlspecialchars($alumni['given_name'] ?? '') ?>"
                 readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="contact_number">Contact Number</label>
        <input type="tel" id="contact_number" name="contact_number"
               value="<?= htmlspecialchars($alumni['contact_number'] ?? '') ?>"
               placeholder="e.g. 09xxxxxxxxx">
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($alumni['email'] ?? '') ?>"
               readonly>
      </div>
    </fieldset>

    <!-- ADDRESS SECTION -->
    <fieldset>
      <legend>Address</legend>
      <div class="grid-2">
        <div class="form-group">
          <label>Region</label>
          <input type="text" name="region"
                 value="<?= htmlspecialchars($alumni['region'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Province</label>
          <input type="text" name="province"
                 value="<?= htmlspecialchars($alumni['province'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>City / Municipality</label>
          <input type="text" name="city_municipality"
                 value="<?= htmlspecialchars($alumni['city_municipality'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Barangay</label>
          <input type="text" name="barangay"
                 value="<?= htmlspecialchars($alumni['barangay'] ?? '') ?>">
        </div>
      </div>
    </fieldset>

    <!-- EMPLOYMENT SECTION -->
    <fieldset>
      <legend>Employment Information</legend>
      <div class="two-col">
        <div class="form-group">
          <label for="company_name">Company Name</label>
          <input type="text" id="company_name" name="company_name"
                 value="<?= htmlspecialchars($alumni['company_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="position">Position / Job Title</label>
          <input type="text" id="position" name="position"
                 value="<?= htmlspecialchars($alumni['position'] ?? '') ?>">
        </div>
      </div>

      <div class="two-col">
        <div class="form-group">
          <label for="company_address">Company Address</label>
          <input type="text" id="company_address" name="company_address"
                 value="<?= htmlspecialchars($alumni['company_address'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="company_contact">Company Contact</label>
          <input type="text" id="company_contact" name="company_contact"
                 value="<?= htmlspecialchars($alumni['company_contact'] ?? '') ?>">
        </div>
      </div>
    </fieldset>

    <!-- EDUCATION ADDITION SECTION -->
    <fieldset>
      <legend>Additional Educational Background</legend>
      <p class="sub-instruction">If you have earned a new tertiary degree, you may add it below.</p>

      <div class="two-col">
        <div class="form-group">
          <label for="new_tertiary_school">New College / University</label>
          <input type="text" id="new_tertiary_school" name="new_tertiary_school"
                 placeholder="e.g. Western Mindanao State University">
        </div>
        <div class="form-group">
          <label for="new_tertiary_yr">Year Graduated</label>
          <input type="number" id="new_tertiary_yr" name="new_tertiary_yr"
                 min="1900" max="<?= date('Y') + 1 ?>">
        </div>
      </div>
    </fieldset>

    <!-- EMERGENCY CONTACT -->
    <fieldset>
      <legend>Emergency Contact</legend>
      <div class="two-col">
        <div class="form-group">
          <label for="emergency_name">Full Name</label>
          <input type="text" id="emergency_name" name="emergency_name"
                 value="<?= htmlspecialchars($alumni['emergency_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="emergency_contact">Contact Number</label>
          <input type="text" id="emergency_contact" name="emergency_contact"
                 value="<?= htmlspecialchars($alumni['emergency_contact'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="emergency_address">Address</label>
        <input type="text" id="emergency_address" name="emergency_address"
               value="<?= htmlspecialchars($alumni['emergency_address'] ?? '') ?>">
      </div>
    </fieldset>

    <!-- SUBMIT BUTTON -->
    <div class="btn-group">
      <button type="submit" class="submit-btn">Submit Renewal</button>
      <a href="renewalVerification.php" class="back-btn">← Back to Verification</a>
    </div>
  </form>
</div>

<style>
.renewal-container {
  max-width: 850px;
  margin: 40px auto;
  background: #f8fff8;
  padding: 35px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
  margin-bottom: 25px;
}
.sub-instruction {
  color: #777;
  margin-bottom: 10px;
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
  padding: 0 10px;
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  font-weight: bold;
  color: #198754;
  margin-bottom: 5px;
}
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="number"] {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.two-col {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}
.two-col .form-group {
  flex: 1;
  min-width: 250px;
}
.grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 15px;
}
.btn-group {
  text-align: center;
  margin-top: 25px;
}
.submit-btn {
  background: #198754;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1em;
}
.submit-btn:hover {
  background: #157347;
}
.back-btn {
  display: inline-block;
  margin-left: 15px;
  color: #198754;
  text-decoration: none;
}
.back-btn:hover {
  text-decoration: underline;
}
</style>
