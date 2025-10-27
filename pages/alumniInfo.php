<?php
// File: pages/alumniInfo.php

$next_section = 'education'; 
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="alumni-info-form section-form">
    <p class="section-instruction">Please fill in your current and personal details.</p>

    <fieldset>
        <legend>Full Name</legend>
        <div class="form-group">
            <label for="surname">Last Name</label>
            <input type="text" id="surname" name="surname" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['surname'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="given_name">First Name</label>
            <input type="text" id="given_name" name="given_name" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['given_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" 
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['middle_name'] ?? ''); ?>">
        </div>
    </fieldset>

    <div class="form-group">
        <label for="programCourse">Program/Course</label>
        <input type="text" id="programCourse" name="course_year"
               value="<?php echo htmlspecialchars(strtoupper($current_program)); ?>" readonly required>
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="dateOfBirth">Date of Birth</label>
        <input type="date" id="dateOfBirth" name="birthday" 
               value="<?php echo htmlspecialchars($_SESSION['form_data']['birthday'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="contactNumber">Contact Number</label>
        <input type="tel" id="contactNumber" name="contact_number" placeholder="e.g., 0917xxxxxxx"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['contact_number'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="emailAddress">Email Address</label>
        <input type="email" id="emailAddress" name="email"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="presentAddress">Present Address</label>
        <textarea id="presentAddress" name="present_address" rows="3" required><?php echo htmlspecialchars($_SESSION['form_data']['present_address'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="next-section-button">Proceed to Educational Background</button>
</form>
