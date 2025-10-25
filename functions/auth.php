<?php
require_once __DIR__ . '/../config.php';

/**
 * Checks hardcoded admin credentials for temporary login functionality.
 * @param string $username
 * @param string $password
 * @return bool True if credentials match, false otherwise.
 */
function authenticateAdmin($username, $password) {
    // For this temporary functional setup:
    return ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD);
}

/**
 * Starts a session and checks if the admin is logged in.
 * Use at the top of protected admin pages.
 */
function checkAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: adminLogin.php');
        exit;
    }
}
?>