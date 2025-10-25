<?php
// File: pages/employmentRecord.php

// These variables are available from index.php
// $current_program, $application_type

$next_section = 'emergency'; 
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="employment-record-form section-form">
    <p class="section-instruction">Please provide your current employment details.</p>

    <div class="form-group">
        <label for="employmentStatus">Current Employment Status</label>
        <select id="employmentStatus" name="employmentStatus" required>
            <option value="">Select Status</option>
            <option value="Employed Full-Time" <?php echo (($_POST['employmentStatus'] ?? '') == 'Employed Full-Time') ? 'selected' : ''; ?>>Employed (Full-Time)</option>
            <option value="Employed Part-Time" <?php echo (($_POST['employmentStatus'] ?? '') == 'Employed Part-Time') ? 'selected' : ''; ?>>Employed (Part-Time)</option>
            <option value="Self-Employed" <?php echo (($_POST['employmentStatus'] ?? '') == 'Self-Employed') ? 'selected' : ''; ?>>Self-Employed</option>
            <option value="Unemployed" <?php echo (($_POST['employmentStatus'] ?? '') == 'Unemployed') ? 'selected' : ''; ?>>Unemployed</option>
            <option value="Retired" <?php echo (($_POST['employmentStatus'] ?? '') == 'Retired') ? 'selected' : ''; ?>>Retired</option>
        </select>
    </div>

    <div class="form-group">
        <label for="companyName">Company/Organization Name</label>
        <input type="text" id="companyName" name="companyName" 
               value="<?php echo htmlspecialchars($_POST['companyName'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="jobTitle">Position/Job Title</label>
        <input type="text" id="jobTitle" name="jobTitle" 
               value="<?php echo htmlspecialchars($_POST['jobTitle'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="companyAddress">Company Address</label>
        <textarea id="companyAddress" name="companyAddress" rows="3" required><?php echo htmlspecialchars($_POST['companyAddress'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="next-section-button">Proceed to Emergency Contact</button>
</form>