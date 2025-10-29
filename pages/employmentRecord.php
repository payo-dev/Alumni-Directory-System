<?php
// ==========================================================
// pages/employmentRecord.php — Employment Record Section
// ==========================================================
$form = $_SESSION['form_data'] ?? [];
$isRenewal = ($_GET['type'] ?? '') === 'Renewal';

$next_section = 'emergency';
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>"
      method="POST" enctype="multipart/form-data"
      class="section-form">

  <p class="section-instruction">
    Provide your employment details (optional).
    <?php if ($isRenewal): ?>
      <br><strong style="color:#6c757d;">You may update only if your employment information has changed.</strong>
    <?php else: ?>
      <br><strong style="color:#dc3545;">You may skip this section if not employed yet.</strong>
    <?php endif; ?>
  </p>

  <fieldset>
    <legend>Employment Record (Optional)</legend>

    <div class="form-group">
      <label for="companyName">Company Name</label>
      <input type="text" id="companyName" name="companyName"
             value="<?= htmlspecialchars($form['companyName'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label for="jobTitle">Position</label>
      <input type="text" id="jobTitle" name="jobTitle"
             value="<?= htmlspecialchars($form['jobTitle'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label for="companyAddress">Company Address</label>
      <textarea id="companyAddress" name="companyAddress" rows="2"
                placeholder="Street, City/Municipality, Province"><?= htmlspecialchars($form['companyAddress'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label for="companyContact">Company Contact Number</label>
      <input type="tel" id="companyContact" name="companyContact"
             placeholder="e.g., 0917xxxxxxx or (062)xxxxxxx"
             value="<?= htmlspecialchars($form['companyContact'] ?? '') ?>">
    </div>
  </fieldset>

  <button type="submit" class="next-section-button">
    Save and Proceed to Emergency Contact
  </button>
</form>
