<?php
// File: pages/alumniInfo.php

// These variables are available from index.php
// $current_program, $application_type

// Placeholder for next section. 
$next_section = 'education'; 
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="alumni-info-form section-form">
    <p class="section-instruction">Please fill in your current and personal details.</p>
    
    <div class="form-group">
        <label for="fullName">Full Name (Last Name, First Name, M.I.)</label>
        <input type="text" id="fullName" name="fullName" placeholder="e.g., Dela Cruz, Juan A." 
               value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="programCourse">Program/Course</label>
        <input type="text" id="programCourse" name="programCourse" 
               value="<?php echo htmlspecialchars(strtoupper($current_program)); ?>" readonly required>
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo (($_POST['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="dateOfBirth">Date of Birth</label>
        <input type="date" id="dateOfBirth" name="dateOfBirth" 
               value="<?php echo htmlspecialchars($_POST['dateOfBirth'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="contactNumber">Contact Number</label>
        <input type="tel" id="contactNumber" name="contactNumber" placeholder="e.g., 0917xxxxxxx" 
               value="<?php echo htmlspecialchars($_POST['contactNumber'] ?? ''); ?>" 
               required pattern="[0-9]{11,13}" title="Phone number must be 11 to 13 digits.">
    </div>

    <div class="form-group">
        <label for="emailAddress">Email Address</label>
        <input type="email" id="emailAddress" name="emailAddress" placeholder="e.g., juan.delacruz@example.com"
               value="<?php echo htmlspecialchars($_POST['emailAddress'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="civilStatus">Civil Status</label>
        <select id="civilStatus" name="civilStatus" required>
            <option value="">Select Status</option>
            <option value="Single" <?php echo (($_POST['civilStatus'] ?? '') == 'Single') ? 'selected' : ''; ?>>Single</option>
            <option value="Married" <?php echo (($_POST['civilStatus'] ?? '') == 'Married') ? 'selected' : ''; ?>>Married</option>
            <option value="Divorced" <?php echo (($_POST['civilStatus'] ?? '') == 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
            <option value="Widowed" <?php echo (($_POST['civilStatus'] ?? '') == 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
        </select>
    </div>

    <div class="form-group">
        <label for="presentAddress">Present Address (House/Block/Street, Barangay, City, Province)</label>
        <textarea id="presentAddress" name="presentAddress" rows="3" required><?php echo htmlspecialchars($_POST['presentAddress'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="next-section-button">Proceed to Educational Background</button>
</form>