<?php
// ==========================================================
// pages/adminLogin.php — Admin Login Page
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/auth.php';

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $loginError = 'Please enter your username and password.';
    } else {
        if (Auth::login($username, $password)) {
            // ✅ Redirect to Admin Dashboard
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
  <title>Admin Login - Alumni System</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body {
      background: #f5f6fa;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-container {
      background: white;
      padding: 35px 40px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      width: 380px;
      text-align: center;
    }
    h2 {
      color: #007bff;
      margin-bottom: 25px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }
    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
      font-size: 16px;
    }
    button:hover {
      background-color: #0056b3;
    }
    .error {
      color: red;
      margin-bottom: 15px;
      font-weight: bold;
    }
    .back-link {
      display: inline-block;
      margin-top: 15px;
      color: #007bff;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
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
  </div>

</body>
</html>
