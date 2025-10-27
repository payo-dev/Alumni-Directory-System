<?php
// ==========================================================
// pages/reviewSubmit.php — Final Step Form
// ==========================================================
?>
<form action="/cssAlumniDirectorySystem/functions/submitForm.php" method="POST" enctype="multipart/form-data" class="review-submit-form section-form">
  <p class="section-instruction">
    Please review your complete application before submitting.
  </p>

  <h2>Review Summary</h2>

  <div style="border:1px solid #ddd; padding:20px; background:#f9f9f9; border-radius:5px; margin-bottom:20px;">
    <p><strong>NOTE:</strong> This is a placeholder for your review summary. (You can display entered info here later.)</p>

    <div class="form-group" style="margin-top:20px;">
      <input type="checkbox" id="certification" name="certification" value="certified" required style="width:auto;">
      <label for="certification" style="display:inline; margin-left:10px;">
        I solemnly certify that the above information is true and correct.
      </label>
    </div>
  </div>

  <input type="hidden" name="final_submission" value="true">

  <button type="submit" class="next-section-button"
          style="background-color:#007bff; color:#fff; padding:10px 20px; border:none; border-radius:5px;">
    Submit Application
  </button>
</form>
