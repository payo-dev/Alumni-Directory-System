<?php
// ==========================================================
// pages/alumniInfo.php — Personal Info Section (New + Renewal)
// ==========================================================
$isRenewal = ($_GET['type'] ?? '') === 'Renewal';
$form = $_SESSION['form_data'] ?? [];

$next_section = 'education'; 
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="alumni-info-form section-form">
    <p class="section-instruction">Please fill in your current and personal details.</p>

  <p class="section-instruction">
    Please fill in your current and personal details.
    <?php if ($isRenewal): ?>
      <br><strong style="color:#007bff;">(Some fields are locked for renewal.)</strong>
    <?php endif; ?><br><strong style="color:#dc3545;">(Required for new applicants.)</strong>
  </p>

  <!-- 2x2 Picture Upload -->
  <div class="form-group" style="text-align:center;">
    <label for="2x2_picture"><strong>Upload 2x2 Picture (PNG/JPG)</strong></label>
    <?php if (!empty($form['2x2_picture_path'])): ?>
      <div style="margin:10px auto;">
        <img src="<?= htmlspecialchars($form['2x2_picture_path']); ?>" alt="Uploaded Picture"
             style="width:120px; height:120px; object-fit:cover; border-radius:8px; border:2px solid #dc3545;">
      </div>
    <?php endif; ?>
    <input type="file" id="2x2_picture" name="2x2_picture"
           accept=".png,.jpg,.jpeg"
           style="margin:auto;"
           <?= $isRenewal ? '' : 'required'; ?>>
  </div>

  <!-- Student ID -->
  <div class="form-group">
    <label for="student_id">Student ID</label>
    <input type="text" id="student_id" name="student_id"
           value="<?= htmlspecialchars($form['student_id'] ?? '') ?>"
           <?= $isRenewal ? 'readonly' : 'required'; ?>>
  </div>

  <!-- Batch Name -->
  <div class="form-group">
    <label for="batch_name">Batch Name</label>
    <input type="text" id="batch_name" name="batch_name"
           value="<?= htmlspecialchars($form['batch_name'] ?? '') ?>"
           <?= $isRenewal ? 'readonly' : 'required'; ?>>
  </div>

  <!-- Full Name -->
  <fieldset>
    <legend>Full Name</legend>
    <div class="two-col">
      <div class="form-group">
        <label for="surname">Last Name</label>
        <input type="text" id="surname" name="surname"
               value="<?= htmlspecialchars($form['surname'] ?? '') ?>"
               <?= $isRenewal ? '' : 'required'; ?>>
      </div>

      <div class="form-group">
        <label for="given_name">First Name</label>
        <input type="text" id="given_name" name="given_name"
               value="<?= htmlspecialchars($form['given_name'] ?? '') ?>"
               <?= $isRenewal ? 'readonly' : 'required'; ?>>
      </div>
    </div>

    <div class="form-group">
      <label for="middle_name">Middle Name (optional)</label>
      <input type="text" id="middle_name" name="middle_name"
             value="<?= htmlspecialchars($form['middle_name'] ?? '') ?>">
    </div>
  </fieldset>

  <!-- Course and Year Graduated -->
  <fieldset>
    <legend>Program Details</legend>
    <div class="two-col">
      <div class="form-group">
        <label for="course">Course</label>
        <select id="course" name="course" <?= $isRenewal ? 'disabled' : 'required'; ?>>
          <option value="">Select Course</option>
          <?php
            $courses = ['BSCS', 'BSIT', 'ACT'];
            foreach ($courses as $c) {
              $selected = (($form['course'] ?? '') === $c) ? 'selected' : '';
              echo "<option value='$c' $selected>$c</option>";
            }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label for="year_graduated">Year Graduated</label>
        <input type="number" id="year_graduated" name="year_graduated"
               min="1900" max="<?= date('Y')+1; ?>"
               value="<?= htmlspecialchars($form['year_graduated'] ?? '') ?>"
               <?= $isRenewal ? 'readonly' : 'required'; ?>>
      </div>
    </div>
  </fieldset>

  <!-- Address -->
  <fieldset>
    <legend>Present Address</legend>
    <div class="grid-2">
      <div class="form-group">
        <label>Region</label>
        <input type="text" name="region"
               value="<?= htmlspecialchars($form['region'] ?? '') ?>"
               <?= $isRenewal ? '' : 'required'; ?>>
      </div>

      <div class="form-group">
        <label>Province</label>
        <input type="text" name="province"
               value="<?= htmlspecialchars($form['province'] ?? '') ?>"
               <?= $isRenewal ? '' : 'required'; ?>>
      </div>

      <div class="form-group">
        <label>City / Municipality</label>
        <input type="text" name="city_municipality"
               value="<?= htmlspecialchars($form['city_municipality'] ?? '') ?>"
               <?= $isRenewal ? '' : 'required'; ?>>
      </div>

      <div class="form-group">
        <label>Barangay</label>
        <input type="text" name="barangay"
               value="<?= htmlspecialchars($form['barangay'] ?? '') ?>"
               <?= $isRenewal ? '' : 'required'; ?>>
      </div>
    </div>
  </fieldset>

  <!-- Contact, Email, Birthday, Blood -->
  <div class="two-col">
    <div class="form-group">
      <label for="contactNumber">Contact Number</label>
      <input type="tel" id="contactNumber" name="contact_number"
             value="<?= htmlspecialchars($form['contact_number'] ?? '') ?>"
             <?= $isRenewal ? '' : 'required'; ?>>
    </div>

    <div class="form-group">
      <label for="emailAddress">Email Address</label>
      <input type="email" id="emailAddress" name="email"
             pattern=".+@gmail\.com"
             placeholder="yourname@gmail.com"
             value="<?= htmlspecialchars($form['email'] ?? '') ?>"
             <?= $isRenewal ? '' : 'required'; ?>>
    </div>
  </div>

  <div class="two-col">
    <div class="form-group">
      <label for="birthday">Birthday</label>
      <input type="date" id="birthday" name="birthday"
             value="<?= htmlspecialchars($form['birthday'] ?? '') ?>"
             <?= $isRenewal ? 'readonly' : 'required'; ?>>
    </div>

    <div class="form-group">
      <label for="blood_type">Blood Type</label>
      <select id="blood_type" name="blood_type" <?= $isRenewal ? 'disabled' : 'required'; ?>>
        <option value="">Select</option>
        <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?>
          <option value="<?= $b ?>" <?= (($form['blood_type'] ?? '') === $b) ? 'selected' : '' ?>><?= $b ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Button -->
  <button type="submit" class="next-section-button">
    Save and Proceed to Educational Background
  </button>
</form>

<style>
/* Responsive Enhancements */
.two-col {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}
.two-col .form-group {
  flex: 1;
  min-width: 240px;
}
.grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 15px;
}
@media (max-width: 768px) {
  fieldset legend {
    font-size: 1.1em;
  }
  .next-section-button {
    font-size: 1em;
  }
}
</style>
