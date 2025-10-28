<?php
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* ========================== */
    /* 🌿 THANK YOU PAGE STYLING */
    /* ========================== */
    body {
      font-family: "Poppins", Arial, sans-serif;
      background: linear-gradient(135deg, #d8f3dc, #ffffff);
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #333;
    }

    .thankyou-container {
      background: #ffffff;
      max-width: 500px;
      width: 90%;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .thankyou-container h1 {
      color: #198754;
      font-size: 1.8em;
      margin-bottom: 15px;
    }

    .thankyou-container p {
      font-size: 1rem;
      margin: 8px 0;
    }

    .thankyou-container strong {
      color: #146c43;
    }

    .thankyou-icon {
      font-size: 3.5em;
      color: #198754;
      margin-bottom: 10px;
    }

    .thankyou-btn {
      display: inline-block;
      background: #198754;
      color: white;
      text-decoration: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 1rem;
      margin-top: 20px;
      transition: 0.3s ease;
    }

    .thankyou-btn:hover {
      background: #146c43;
      transform: scale(1.03);
    }

    .thankyou-note {
      margin-top: 25px;
      font-size: 0.9rem;
      color: #555;
    }

    @media (max-width: 600px) {
      .thankyou-container {
        padding: 30px 20px;
      }

      .thankyou-container h1 {
        font-size: 1.5em;
      }

      .thankyou-btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="thankyou-container">
    <div class="thankyou-icon">✅</div>
    <h1>Thank You for Submitting Your Application!</h1>
    <p>Your alumni information has been successfully recorded.</p>

    <?php if ($id): ?>
      <p><strong>Reference ID:</strong> <?= htmlspecialchars($id) ?></p>
    <?php endif; ?>
<<<<<<< Updated upstream
    <p>You will receive an update via the email address you provided.</p>
    <br>
    <a href="../index.php" style="display:inline-block; padding:10px 20px; background:#007bff; color:white; border-radius:5px; text-decoration:none;">
      Return to Home
    </a>
=======

    <p>You will receive updates via the email you provided.</p>

    <a href="../index.php" class="thankyou-btn">Return to Home</a>

    <div class="thankyou-note">
      <p>Need to make changes? You can renew or update your record anytime.</p>
    </div>
>>>>>>> Stashed changes
  </div>
</body>
</html>
