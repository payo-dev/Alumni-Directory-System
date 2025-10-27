<?php
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; text-align:center; padding:50px;">
  <div style="background:white; padding:40px; max-width:500px; margin:auto; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
    <h1>🎉 Thank You for Submitting Your Application!</h1>
    <p>Your alumni information has been successfully submitted.</p>
    <?php if ($id): ?>
      <p><strong>Reference ID:</strong> <?= htmlspecialchars($id) ?></p>
    <?php endif; ?>
    <p>You will receive updates via the email you provided.</p>
    <br>
    <a href="../index.php" style="display:inline-block; padding:10px 20px; background:#dc3545; color:white; border-radius:5px; text-decoration:none;">
      Return to Home
    </a>
  </div>
</body>
</html>
