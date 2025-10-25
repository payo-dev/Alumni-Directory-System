<?php
// File: pages/reviewSubmit.php

// These variables are available from index.php
// $current_program, $application_type
?>

<form action="submit_application.php" method="POST" class="review-submit-form section-form">
    <p class="section-instruction">Please review your complete application before submitting. (Final submission will redirect to a processing page.)</p>

    <h2>Review Summary</h2>
    
    <div style="border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9; border-radius: 5px; margin-bottom: 20px;">
        <p><strong>NOTE:</strong> In a live application, all data submitted in the previous steps would be retrieved (likely from a session or database) and displayed here for the user to confirm.</p>
        <p>For this placeholder, assume data review is successful.</p>
        
        <div class="form-group" style="margin-top: 20px;">
             <input type="checkbox" id="certification" name="certification" value="certified" required style="width: auto;">
             <label for="certification" style="display: inline; margin-left: 10px;">I solemnly certify that the above information is true and correct.</label>
        </div>
        
    </div>

    <input type="hidden" name="final_submission" value="true">

    <button type="submit" class="next-section-button" style="background-color: #007bff;">
        Submit Application
    </button>
</form>