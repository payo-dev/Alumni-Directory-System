<?php
// ==========================================================
// pages/educationalBackground.php — Educational Background Section
// ==========================================================
$form = $_SESSION['form_data'] ?? [];
$isRenewal = ($_GET['type'] ?? '') === 'Renewal';

$next_section = 'employment';
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>"
      method="POST" enctype="multipart/form-data"
      class="section-form">

  <p class="section-instruction">
    Please provide your complete educational background.
    <?php if ($isRenewal): ?>
      <br><strong style="color:#007bff;">(All fields below are static for renewal.)</strong>
    <?php endif; ?><br><strong style="color:#dc3545;">(Required for new applicants.)</strong>
  </p>

  <?php
  $sections = [
    'Elementary' => ['elemSchool', 'elemYear'],
    'Junior High School' => ['hsSchool', 'hsYear'],
    'Senior High School' => ['shSchool', 'shYear'],
    'Tertiary' => ['degree', 'collegeYear'],
    'Graduate School' => ['gradSchool', 'gradYear'],
  ];

  foreach ($sections as $label => [$school, $year]): ?>
    <fieldset>
      <legend><?= $label ?></legend>
      <div class="two-col">
        <div class="form-group">
          <label for="<?= $school ?>">School Name</label>
          <input type="text" id="<?= $school ?>" name="<?= $school ?>"
                 value="<?= htmlspecialchars($form[$school] ?? '') ?>"
                 <?= $isRenewal ? 'readonly' : ($label === 'Graduate School' ? '' : 'required'); ?>>
        </div>

        <div class="form-group">
          <label for="<?= $year ?>">Year Graduated</label>
          <input type="number" id="<?= $year ?>" name="<?= $year ?>"
                 value="<?= htmlspecialchars($form[$year] ?? '') ?>"
                 min="1900" max="<?= date('Y'); ?>"
                 <?= $isRenewal ? 'readonly' : ($label === 'Graduate School' ? '' : 'required'); ?>>
        </div>
      </div>
    </fieldset>
  <?php endforeach; ?>

  <button type="submit" class="next-section-button">
    Save and Proceed to Employment Record
  </button>
</form>

<style>
.two-col {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}
.two-col .form-group {
  flex: 1;
  min-width: 240px;
}
@media (max-width: 768px) {
  .two-col {
    flex-direction: column;
  }
  fieldset legend {
    font-size: 1.1em;
  }
}
</style>
