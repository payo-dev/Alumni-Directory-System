<?php
// File: pages/emergencyContact.php

// These variables are available from index.php
// $current_program, $application_type

$next_section = 'review';
$current_url_params = "program={$current_program}&type={$application_type}";
?>

<form action="index.php?section=<?php echo $next_section; ?>&<?php echo $current_url_params; ?>" method="POST" class="emergency-contact-form section-form">
    <p class="section-instruction">Provide details for the person we should contact in case of an emergency.</p>

    <div class="form-group">
        <label for="emergencyName">Full Name of Contact Person</label>
        <input type="text" id="emergencyName" name="emergencyName" placeholder="Last Name, First Name" 
               value="<?php echo htmlspecialchars($_SESSION['form_data']['emergencyName'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="relationship">Relationship to this person</label>
        <input type="text" id="relationship" name="relationship" placeholder="e.g., Mother, Spouse, Friend" 
               value="<?php echo htmlspecialchars($_SESSION['form_data']['relationship'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="emergencyContact">Contact Number</label>
        <input type="tel" id="emergencyContact" name="emergencyContact" placeholder="e.g., 0917xxxxxxx" 
               value="<?php echo htmlspecialchars($_SESSION['form_data']['emergencyContact'] ?? ''); ?>" 
               required pattern="[0-9]{11,13}" title="Phone number must be 11 to 13 digits.">
    </div>

    <div class="form-group">
        <label for="emergencyAddress">Address</label>
        <textarea id="emergencyAddress" name="emergencyAddress" rows="3" required><?php echo htmlspecialchars($_SESSION['form_data']['emergencyAddress'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="next-section-button">Proceed to Review & Submit</button>
</form>