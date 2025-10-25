<?php
  // ==========================================================
  // 1. PHP CONTROLLER LOGIC
  // ==========================================================
  require_once 'classes/functions.php'; 

  // --- NAVIGATION CONFIGURATION ---
  $sections = [
    'alumni' => ['title' => 'Alumni Info'],
    'education' => ['title' => 'Education Background'],
    'employment' => ['title' => 'Employment Record'],
    'emergency' => ['title' => 'Emergency Contact'],
    'review' => ['title' => 'Review & Submit'],
  ];

  // Get the current application type (New/Renewal)
  $application_type = $_GET['type'] ?? 'New'; 
  
  // Determine total steps based on application type (New skips Employment)
  $section_keys = array_keys($sections);
  if ($application_type === 'New') {
    $section_keys = array_diff($section_keys, ['employment']);
  }
  $total_steps = count($section_keys);
  $program_name = strtoupper($_GET['program'] ?? 'default'); // Store program name (e.g., CCS)

  // Get the current program selected (used to set the background class)
  $current_program = $_GET['program'] ?? 'default';

  // Get the current form section from the URL
  $current_section = $_GET['section'] ?? null; 
  
  $base_path = 'pages/';
  $file_to_include = '';
  $current_step = 0;

  // JavaScript confirmation message for leaving the form
  $confirm_message = "WARNING: Changes will not be saved. Are you sure you want to leave this page?";

  // Simple routing logic and step calculation
  if ($current_section) {
    // Calculate current step number
    $current_step = array_search($current_section, array_values($section_keys)) + 1;

    switch ($current_section) {
      case 'alumni':
        $file_to_include = $base_path . 'alumniInfo.php';
        break;
      case 'education':
        $file_to_include = $base_path . 'educationalBackground.php';
        break;
      case 'employment':
          // Conditional routing (Employment only for Renewal)
          if ($application_type === 'Renewal') {
               $file_to_include = $base_path . 'employmentRecord.php';
          } else {
               // Skip employment if 'New' and redirect to next step (Emergency)
               header("Location: index.php?section=emergency&program=$current_program&type=$application_type");
               exit;
          }
        break;
      case 'emergency':
        $file_to_include = $base_path . 'emergencyContact.php';
        break;
      case 'review':
        $file_to_include = $base_path . 'reviewSubmit.php';
        break;
      default:
        $file_to_include = $base_path . 'alumniInfo.php';
        $current_step = 1;
        break;
    }
  }
  
  // Create the body class based on the URL program parameter
  $body_class = $current_program === 'ccs' ? 'ccs-program-bg' : 'default-program-bg';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css"> 
    <title>Alumni Directory System Landing Page</title>
  </head>
  <body class="<?php echo $body_class; ?>">
    <div class="mainContainer">
      
      <header class="header-content">
        
        <div class="logo-area">
          <img src="assets/images/logo1.png" alt="University Logo" class="logo">
          <img src="assets/images/logo2.png" alt="Program Logo" class="logo">
        </div>

        <div class="banner-text">
            <div class="university-title-block">
                <p class="university-line">Western Mindanao State University</p>
                <p class="office-line">
                    <?php if (!$current_section) : // Landing Page: Show Admin Login link and keep title central ?>
                        <a href="adminLogin.php" class="admin-login-link-text">ALUMNI RELATION OFFICE</a>
                    <?php else: // Form Flow: Just show the text ?>
                        ALUMNI RELATION OFFICE
                    <?php endif; ?>
                </p>
                <p class="city-line">Zamboanga City</p>
            </div>
        </div>
          
        <div class="right-header-block">
            <?php if ($current_section) : ?>
                <a href="index.php" class="back-link-img" 
                    onclick="return confirm('<?php echo htmlspecialchars($confirm_message, ENT_QUOTES); ?>');">
                    <img src="assets/images/back.png" alt="Back" class="back-icon">
                </a>
            <?php else: ?>
                <div class="program-selector">
                    <div class="program-label-group">
                        <label for="program-select">Program:</label>
                        <select id="program-select" onchange="updateBackground(this.value)">
                            <option value="default" <?php echo ($current_program === 'default') ? 'selected' : ''; ?>>Select Program</option> 
                            <option value="ccs" <?php echo ($current_program === 'ccs') ? 'selected' : ''; ?>>CCS</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
        </div>

      </header>
      
      <?php if (!$current_section) : ?>
        
        <h1 class="main-title">Alumni Information Form</h1>
        
        <div class="application-type-selector">
          <button class="type-button selected" data-type="new">NEW</button>
          <button class="type-button" data-type="renewal">RENEWAL</button>
        </div>
        
        <p class="instruction-line">
          Choose NEW for your first registration or RENEWAL if you are updating an existing record.
        </p>
      <?php endif; ?>
      
      <?php if ($current_section) : ?>
        
        <h1 class="main-title"><?php echo htmlspecialchars($sections[$current_section]['title']); ?></h1>

        <div class="progression-bar-container">
            <span class="progression-text">Step <?php echo $current_step; ?>/<?php echo $total_steps; ?></span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo (($current_step / $total_steps) * 100); ?>%;"></div>
            </div>
        </div>

        <nav class="section-nav">
          <?php 
          $current_index = array_search($current_section, array_values($section_keys));

          foreach ($section_keys as $key => $section_name): 
            $link_class = ($section_name === $current_section) ? 'nav-link active' : 'nav-link';
            $link_url = "index.php?section=$section_name&program=$current_program&type=$application_type";
            
            $onclick_handler = '';
            if ($key < $current_index) {
                $onclick_handler = 'onclick="return confirm(\'' . htmlspecialchars($confirm_message, ENT_QUOTES) . '\');"';
            }
          ?>
            <a href="<?php echo $link_url; ?>" class="<?php echo $link_class; ?>" <?php echo $onclick_handler; ?>>
              <?php echo $sections[$section_name]['title']; ?>
            </a>
          <?php endforeach; ?>
        </nav>
        
        <div class="section-forms-container">
          <?php
            if (file_exists($file_to_include)) {
              include($file_to_include); 
            } else {
              echo '<p>Error: Section not found.</p>';
            }
          ?>
        </div>
      <?php endif; ?>
      
    </div>
    
    <?php generate_form_scripts(); ?>
    
  </body>
</html>