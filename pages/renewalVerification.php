<?php
// ==========================================================
// pages/renewalVerification.php — Verify Alumni for Renewal (Fixed + Combined)
// ==========================================================

// ✅ Correct include path from /pages/ to /classes/
require_once __DIR__ . '/../classes/database.php';

// ✅ Prevent "session already active" notice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pdo = Database::getPDO();

    // ✅ Corrected query — joins alumni_info and uses ai.email
    $stmt = $pdo->prepare("
        SELECT ca.*, ai.email, ai.region, ai.province, ai.city_municipality, ai.barangay,
               ai.birthday, ai.blood_type, ai.picture_path
        FROM colleges_alumni ca
        LEFT JOIN alumni_info ai ON ca.student_id = ai.student_id
        WHERE ai.email = :email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $alumni = $stmt->fetch();

    if ($alumni) {
        // ✅ Prevent duplicate renewal attempts
        if (!empty($alumni['renewal_status']) && $alumni['renewal_status'] === 'pending') {
            $message = "Your renewal request is currently pending approval. Please wait for confirmation before submitting again.";
        } else {
            $_SESSION['form_data'] = $alumni;
            header("Location: renewalForm.php?student_id=" . urlencode($alumni['student_id']));
            exit;
        }
    } else {
        $message = "No record found for this email. Please make sure you are using the email you registered with.";
    }
}
?>

<div class="verification-container">
  <h1>CCS Alumni Renewal</h1>
  <p class="instruction">Enter your registered email to begin your renewal process.</p>

  <form method="POST" class="verification-form">
    <div class="form-group">
      <label for="email">Registered Email Address</label>
      <input type="email" name="email" id="email" placeholder="e.g. yourname@gmail.com" required>
    </div>

    <?php if (!empty($message)): ?>
      <p class="error-msg"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <button type="submit" class="next-section-button">🔍 Verify Email</button>
    <a href="../index.php" class="back-btn">← Back to Landing Page</a>
  </form>
</div>

<style>
.verification-container {
  max-width: 500px;
  margin: 50px auto;
  background: #f8fff8;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  text-align: center;
  font-family: Arial, sans-serif;
}
h1 {
  color: #198754;
  margin-bottom: 15px;
}
.instruction {
  color: #555;
  margin-bottom: 20px;
}
.form-group {
  margin-bottom: 15px;
  text-align: left;
}
.form-group label {
  display: block;
  color: #198754;
  font-weight: bold;
  margin-bottom: 5px;
}
input[type="email"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.next-section-button {
  background: #198754;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 1em;
  cursor: pointer;
}
.next-section-button:hover {
  background: #157347;
}
.error-msg {
  color: #dc3545;
  margin-bottom: 15px;
}
.back-btn {
  display: inline-block;
  margin-top: 10px;
  color: #198754;
  text-decoration: none;
}
.back-btn:hover {
  text-decoration: underline;
}
</style>
