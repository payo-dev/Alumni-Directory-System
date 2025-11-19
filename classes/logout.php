<?php
// ==========================================================
// classes/logout.php — Secure Admin Logout
// ==========================================================

// ✅ Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------
// 🧹 Unset all session variables safely
// ----------------------------------------------------------
$_SESSION = [];

// ----------------------------------------------------------
// 🍪 Delete session cookie (recommended best practice)
// ----------------------------------------------------------
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ----------------------------------------------------------
// 💣 Fully destroy the session
// ----------------------------------------------------------
session_destroy();

// ----------------------------------------------------------
// 🚀 Redirect back to admin login page
// ----------------------------------------------------------
header("Location: ../pages/adminLogin.php");
exit;
