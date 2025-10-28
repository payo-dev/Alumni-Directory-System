<?php
// ==========================================================
// index.php — Main Controller for Alumni Form Flow
// ==========================================================
session_start();
require_once __DIR__ . '/classes/database.php';

// --- SECTION CONFIG ---
$sections = [
  'alumni'     => ['title' => 'Alumni Info'],
  'education'  => ['title' => 'Educational Background'],
  'employment' => ['title' => 'Employment Record'],
  'emergency'  => ['title' => 'Emergency Contact'],
  'review'     => ['title' => 'Review & Submit'],
];

// --- URL PARAMETERS ---
$application_type = $_GET['type'] ?? 'New';
$current_program  = $_GET['program'] ?? 'default';
$current_section  = $_GET['section'] ?? null;

// --- Save Session Data ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
  $_SESSION['form_data'] = array_merge($_SESSION['form_data'] ?? [], $_POST);
  header("Location: " . $_SERVER['REQUEST_URI']);
  exit;
}

// --- Routing Logic ---
$file_map = [
  'alumni' => 'pages/alumniInfo.php',
  'education' => 'pages/educationalBackground.php',
  'employment' => 'pages/employmentRecord.php',
  'emergency' => 'pages/emergencyContact.php',
  'review' => 'pages/reviewSubmit.php',
];
$file_to_include = $file_map[$current_section] ?? '';

$section_keys = array_keys($sections);
$current_step = $current_section ? array_search($current_section, $section_keys) + 1 : 0;
$total_steps = count($section_keys);

$body_class = ($current_program === 'ccs') ? 'ccs-program-bg' : 'default-program-bg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alumni Directory System</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="<?php echo $body_class; ?>">
<div class="mainContainer">
  <!-- HEADER -->
  <header class="header-content">
    <div class="logo-area">
      <img src="assets/images/logo1.png" alt="University Logo" class="logo">
      <img src="assets/images/logo2.png" alt="Program Logo" class="logo">
    </div>

    <div class="banner-text">
      <div class="university-title-block">
        <p class="university-line">Western Mindanao State University</p>
        
        <!-- ✅ Clickable Alumni Relation Office that redirects to admin login -->
        <p class="office-line" style="cursor:pointer; color:#007bff;" onclick="goToAdmin()">
          ALUMNI RELATION OFFICE
        </p>
        
        <p class="city-line">Zamboanga City</p>
      </div>
    </div>

    <div class="right-header-block">
      <?php if ($current_section): ?>
        <a href="functions/clearSession.php" 
           class="back-link-img"
           onclick="return confirm('Go back to the Landing Page? The form will not be saved unless you submit it.');">
          <img src="assets/images/back.png" alt="Back" class="back-icon">
        </a>
      <?php else: ?>
        <div class="program-selector">
          <div class="program-label-group">
            <label for="program-select">Program:</label>
            <select id="program-select" onchange="updateProgram(this.value)">
              <option value="default" <?= ($current_program === 'default') ? 'selected' : '' ?>>Select Program</option>
              <option value="ccs" <?= ($current_program === 'ccs') ? 'selected' : '' ?>>CCS</option>
            </select>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <!-- LANDING PAGE -->
  <?php if (!$current_section): ?>
    <h1 class="main-title">Alumni Information Form</h1>
    <div class="application-type-selector">
      <button class="type-button selected" data-type="new">NEW</button>
      <button class="type-button" data-type="renewal">RENEWAL</button>
    </div>
    <p class="instruction-line">Choose NEW for your first registration or RENEWAL if you are updating an existing record.</p>
  <?php endif; ?>

  <!-- SECTION FORMS -->
  <?php if ($current_section): ?>
    <h1 class="main-title"><?php echo htmlspecialchars($sections[$current_section]['title']); ?></h1>
    <div class="progression-bar-container">
      <span class="progression-text">Step <?php echo $current_step; ?>/<?php echo $total_steps; ?></span>
      <div class="progress-bar">
        <div class="progress-fill" style="width: <?php echo ($current_step / $total_steps) * 100; ?>%;"></div>
      </div>
    </div>

    <nav class="section-nav">
      <?php foreach ($section_keys as $section_name): ?>
        <a href="index.php?section=<?php echo $section_name; ?>&program=<?php echo $current_program; ?>&type=<?php echo $application_type; ?>"
           class="<?php echo ($section_name === $current_section) ? 'nav-link active' : 'nav-link'; ?>">
          <?php echo $sections[$section_name]['title']; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="section-forms-container">
      <?php include($file_to_include); ?>
    </div>
  <?php endif; ?>
</div>

<script>
function updateProgram(value){
  window.location.href = `index.php?program=${value}`;
}

document.querySelectorAll('.type-button').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const type = btn.dataset.type;
    const prog = document.getElementById('program-select').value;
    if(prog === 'default') return alert('Please select a program first.');

    if(type === 'renewal') {
      // Redirect to the verification page first
      window.location.href = `pages/renewalVerification.php?program=${prog}`;
    } else {
      // Normal new application
      window.location.href = `index.php?section=alumni&program=${prog}&type=New`;
    }
  });
});

// ✅ Redirect function for ALUMNI RELATION OFFICE
function goToAdmin() {
  window.location.href = "pages/adminLogin.php";
}
if(type === 'renewal') {
  // Redirect to the verification page first
  window.location.href = `pages/renewalVerification.php?program=${prog}`;
} else {
  // Normal new application
  window.location.href = `index.php?section=alumni&program=${prog}&type=New`;
}

</script>

</body>
</html>
