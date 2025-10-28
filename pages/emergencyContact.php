<?php
// ==========================================================
// pages/emergencyContact.php — Emergency Contact Section
// ==========================================================
$form = $_SESSION['form_data'] ?? [];
$isRenewal = ($_GET['type'] ?? '') === 'Renewal';

$next_section = 'review';
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>"
      method="POST" enctype="multipart/form-data"
      class="section-form">

  <p class="section-instruction">
    Provide details for the person we should contact in case of an emergency.
    <?php if ($isRenewal): ?>
      <br><strong style="color:#6c757d;">(Optional for renewal — update only if there are changes.)</strong>
    <?php else: ?>
      <br><strong style="color:#dc3545;">(Required for new applicants.)</strong>
    <?php endif; ?>
  </p>

  <!-- Emergency Contact Name -->
  <div class="form-group">
    <label for="emergencyName">Full Name of Contact Person</label>
    <input type="text" id="emergencyName" name="emergencyName"
           placeholder="Last Name, First Name"
           value="<?= htmlspecialchars($form['emergencyName'] ?? '') ?>"
           <?= $isRenewal ? '' : 'required'; ?>>
  </div>

  <!-- Emergency Address -->
  <div class="form-group">
    <label for="emergencyAddress">Address</label>
    <textarea id="emergencyAddress" name="emergencyAddress" rows="3"
              placeholder="Full address (Region, Province, City, Barangay)"
              <?= $isRenewal ? '' : 'required'; ?>><?= htmlspecialchars($form['emergencyAddress'] ?? '') ?></textarea>
  </div>

  <!-- Emergency Contact Number -->
  <div class="form-group">
    <label for="emergencyContact">Contact Number</label>
    <input type="tel" id="emergencyContact" name="emergencyContact"
           placeholder="e.g., 09xxxxxxxxx"
           value="<?= htmlspecialchars($form['emergencyContact'] ?? '') ?>"
           pattern="[0-9]{11,13}" title="Phone number must be 11 to 13 digits."
           <?= $isRenewal ? '' : 'required'; ?>>
  </div>

  <button type="submit" class="next-section-button">
    Save and Proceed to Review & Submit
  </button>
</form>
