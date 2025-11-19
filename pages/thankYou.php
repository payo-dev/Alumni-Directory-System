<?php
// ==========================================================
// pages/thankYou.php — Submission Confirmation Page
// ==========================================================
$type = $_GET['type'] ?? 'new';
$id = $_GET['id'] ?? ($_GET['student_id'] ?? null);

// Normalize type for message
$typeText = strtolower($type) === 'renewal' ? 'Renewal Request' : 'New Application';
$nextAction = strtolower($type) === 'renewal'
    ? 'Our office will review your updated details soon.'
    : 'Your alumni record has been successfully added to our system.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You - Alumni Submission</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* ==================================================== */
    /* 🌿 THANK YOU PAGE STYLING — Clean + CCS Green Theme */
    /* ==================================================== */
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
      overflow-x: hidden;
    }

    .thankyou-container {
      background: #ffffff;
      max-width: 520px;
      width: 92%;
      padding: 40px 35px;
      border-radius: 14px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      animation: fadeIn 0.7s ease-in-out;
      position: relative;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .thankyou-icon {
      font-size: 3.8em;
      color: #198754;
      margin-bottom: 15px;
      animation: pulse 1.4s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.08); }
    }

    h1 {
      color: #198754;
      font-size: 1.9em;
      margin-bottom: 10px;
    }

    p {
      font-size: 1rem;
      margin: 8px 0;
      color: #444;
    }

    strong {
      color: #146c43;
    }

    .thankyou-type {
      background: #d1e7dd;
      color: #0f5132;
      display: inline-block;
      padding: 5px 12px;
      border-radius: 6px;
      font-size: 0.9em;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .thankyou-btn {
      display: inline-block;
      background: #198754;
      color: white;
      text-decoration: none;
      padding: 12px 26px;
      border-radius: 8px;
      font-size: 1rem;
      margin-top: 25px;
      transition: 0.3s ease;
      box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
    }

    .thankyou-btn:hover {
      background: #146c43;
      transform: scale(1.04);
      box-shadow: 0 6px 14px rgba(20, 108, 67, 0.3);
    }

    .thankyou-note {
      margin-top: 25px;
      font-size: 0.9rem;
      color: #555;
      line-height: 1.5;
    }

    @media (max-width: 600px) {
      .thankyou-container {
        padding: 30px 20px;
      }
      h1 {
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
    <span class="thankyou-type"><?= strtoupper($typeText) ?></span>
    <h1>Thank You for Your Submission!</h1>

    <?php if (strtolower($type) === 'renewal'): ?>
      <p>Your <strong>renewal request</strong> has been successfully submitted.</p>
    <?php else: ?>
      <p>Your <strong>new alumni registration</strong> has been recorded.</p>
    <?php endif; ?>

    <?php if ($id): ?>
      <p><strong>Reference ID:</strong> <?= htmlspecialchars($id) ?></p>
    <?php endif; ?>

    <p><?= htmlspecialchars($nextAction) ?></p>
    <p>You will receive updates via your registered email.</p>

    <a href="../index.php" class="thankyou-btn">🏠 Return to Home</a>

    <div class="thankyou-note">
      <?php if (strtolower($type) === 'renewal'): ?>
        <p>Our Alumni Office will verify your updates.  
        You’ll be notified once your record is approved.</p>
      <?php else: ?>
        <p>Need to modify your record later?  
        You can use the Renewal option on our homepage anytime.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
