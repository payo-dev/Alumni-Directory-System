<?php
// ==========================================================
// pages/adminLogin.php — Admin Login Page (Red Dashboard Theme)
// ==========================================================

// ✅ Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication handler
require_once __DIR__ . '/../classes/auth.php';

$loginError = '';

// ----------------------------------------------------------
// 🔐 Handle Login Submission
// ----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $loginError = 'Please enter your username and password.';
    } else {
        if (Auth::login($username, $password)) {
            // Redirect to dashboard
            header("Location: /cssAlumniDirectorySystem/pages/adminDashboard.php");
            exit;
        } else {
            $loginError = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Alumni Directory System</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* =============================== */
    /* ❤️ Admin Login - Red Theme */
    /* =============================== */
    body {
      background: #fff5f5;
      font-family: "Poppins", Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: #ffffff;
      border: 1px solid #f3d6d6;
      border-left: 6px solid #dc3545;
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      width: 380px;
      padding: 40px 35px;
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-container h2 {
      color: #b30000;
      margin-bottom: 25px;
      font-size: 1.8em;
      border-bottom: 2px solid #f0b3b3;
      padding-bottom: 10px;
    }

    .error {
      color: #b30000;
      background: #ffe6e6;
      border: 1px solid #ffcccc;
      border-radius: 6px;
      padding: 10px;
      margin-bottom: 15px;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .error::before {
      content: "❌";
      font-size: 1.2em;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      margin: 8px 0;
      border: 1px solid #f0b3b3;
      border-radius: 6px;
      font-size: 15px;
      transition: border 0.2s;
    }

    input:focus {
      outline: none;
      border-color: #dc3545;
      box-shadow: 0 0 4px rgba(220,53,69,0.3);
    }

    button {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s, transform 0.1s;
    }

    button:hover {
      background-color: #b30000;
      transform: scale(1.02);
    }

    .back-link {
      display: inline-block;
      margin-top: 15px;
      color: #dc3545;
      text-decoration: none;
      font-weight: bold;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    footer {
      text-align: center;
      font-size: 0.85em;
      color: #777;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($loginError)): ?>
      <div class="error"><?= htmlspecialchars($loginError); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required autocomplete="off">
      <input type="password" name="password" placeholder="Password" required autocomplete="off">
      <button type="submit">Login</button>
    </form>

    <a href="../index.php" class="back-link">← Back to Home</a>

    <footer>© <?= date('Y') ?> CCS Alumni Directory System</footer>
  </div>

</body>
</html>
