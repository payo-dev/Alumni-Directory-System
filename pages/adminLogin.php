<?php
// File: adminLogin.php
// This is a new file for the Administrator Login Page

// Placeholder for any initial PHP logic (e.g., checking if user is already logged in)
$login_message = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css"> 
    <title>Admin Login - Alumni System</title>
    <style>
        /* Specific styles for the login form */
        .login-container {
            max-width: 400px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.98);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .login-container h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
        }
        .login-container .form-group input {
            width: 100%;
        }
        .login-button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .login-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="default-program-bg">
    <div class="login-container">
        <h1>Admin Login</h1>
        
        <form action="process_login.php" method="POST">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <?php if (!empty($login_message)) : ?>
                <p style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($login_message); ?></p>
            <?php endif; ?>
            
            <button type="submit" class="login-button">Login</button>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="index.php">← Back to Alumni Form</a>
            </p>
        </form>
    </div>
</body>
</html>