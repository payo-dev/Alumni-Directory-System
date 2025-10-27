<?php
// File: pages/educationalBackground.php

// These variables are available from index.php
// $current_program, $application_type

// The next section is 'employment' (if Renewal) or 'emergency' (if New)
$next_section = ($application_type === 'Renewal') ? 'employment' : 'emergency'; 
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="educational-background-form section-form">
    <p class="section-instruction">Please provide details of your academic journey.</p>

    <fieldset>
        <legend>Elementary Education</legend>
        <div class="form-group">
            <label for="elemSchool">School Name</label>
            <input type="text" id="elemSchool" name="elemSchool" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['elemSchool'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="elemYear">Year Graduated</label>
            <input type="number" id="elemYear" name="elemYear" min="1950" max="<?php echo date('Y'); ?>" placeholder="YYYY" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['elemYear'] ?? ''); ?>" required>
        </div>
    </fieldset>

    <fieldset>
        <legend>Secondary Education</legend>
        <div class="form-group">
            <label for="hsSchool">School Name</label>
            <input type="text" id="hsSchool" name="hsSchool" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['hsSchool'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="hsYear">Year Graduated</label>
            <input type="number" id="hsYear" name="hsYear" min="1950" max="<?php echo date('Y'); ?>" placeholder="YYYY" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['hsYear'] ?? ''); ?>" required>
        </div>
    </fieldset>

    <fieldset>
        <legend>College Education (WMSU)</legend>
        <div class="form-group">
            <label for="degree">Degree/Course</label>
            <input type="text" id="degree" name="degree" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['degree'] ?? strtoupper($current_program)); ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="collegeYear">Year Graduated from WMSU</label>
            <input type="number" id="collegeYear" name="collegeYear" min="1950" max="<?php echo date('Y'); ?>" placeholder="YYYY" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['collegeYear'] ?? ''); ?>" required>
        </div>
    </fieldset>

    <button type="submit" class="next-section-button">
        Proceed to <?php echo ($application_type === 'Renewal') ? 'Employment Record' : 'Emergency Contact'; ?>
    </button>
</form>