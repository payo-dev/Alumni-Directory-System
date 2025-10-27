<?php
// ==========================================================
// File: pages/alumniInfo.php — Personal Info Section
// ==========================================================

$next_section = 'education';
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>"
      method="POST" enctype="multipart/form-data"
      class="alumni-info-form section-form">

    <p class="section-instruction">
        Please fill in your current and personal details.
    </p>

    <!-- 2x2 Picture Upload -->
    <div class="form-group">
        <label for="2x2_picture">Upload 2x2 Picture (PNG/JPG)</label>
        <input type="file" id="2x2_picture" name="2x2_picture" accept=".png,.jpg,.jpeg" required>
    </div>

    <!-- Student ID -->
    <div class="form-group">
        <label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" required
               value="<?php echo htmlspecialchars($_SESSION['form_data']['student_id'] ?? ''); ?>">
    </div>

    <!-- Batch Name -->
    <div class="form-group">
        <label for="batch_name">Batch Name</label>
        <input type="text" id="batch_name" name="batch_name"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['batch_name'] ?? ''); ?>">
    </div>

    <!-- Full Name -->
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

    <!-- Program -->
    <div class="form-group">
        <label for="programCourse">Program / Course</label>
        <input type="text" id="programCourse" name="course_year"
               value="<?php echo htmlspecialchars(strtoupper($current_program)); ?>" readonly required>
    </div>

    <!-- Gender -->
    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo (($_SESSION['form_data']['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>

    <!-- Blood Type -->
    <div class="form-group">
        <label for="blood_type">Blood Type</label>
        <select id="blood_type" name="blood_type" required>
            <option value="">Select Blood Type</option>
            <?php
            $bloods = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            $selected = $_SESSION['form_data']['blood_type'] ?? '';
            foreach ($bloods as $b) {
                $sel = ($selected === $b) ? 'selected' : '';
                echo "<option value='$b' $sel>$b</option>";
            }
            ?>
        </select>
    </div>

    <!-- Date of Birth -->
    <div class="form-group">
        <label for="dateOfBirth">Date of Birth</label>
        <input type="date" id="dateOfBirth" name="birthday"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['birthday'] ?? ''); ?>" required>
    </div>

    <!-- Contact Number -->
    <div class="form-group">
        <label for="contactNumber">Contact Number</label>
        <input type="tel" id="contactNumber" name="contact_number" placeholder="e.g., 0917xxxxxxx"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['contact_number'] ?? ''); ?>" required>
    </div>

    <!-- Email Address -->
    <div class="form-group">
        <label for="emailAddress">Email Address</label>
        <input type="email" id="emailAddress" name="email"
               value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" required>
    </div>

    <!-- Present Address -->
    <div class="form-group" style="grid-column: 1 / -1;">
        <label for="presentAddress">Present Address</label>
        <textarea id="presentAddress" name="present_address" rows="3" required><?php
            echo htmlspecialchars($_SESSION['form_data']['present_address'] ?? '');
        ?></textarea>
    </div>

    <!-- Next Button -->
    <button type="submit" class="next-section-button">
        Proceed to Educational Background
    </button>
</form>
