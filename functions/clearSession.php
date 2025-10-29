<?php
// ==========================================================
// functions/clearSession.php — Clears Form Session and Redirects
// ==========================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear only form data session (to avoid affecting admin sessions)
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

// Optional: clear other temporary form-related data if you have any
if (isset($_SESSION['current_section'])) {
    unset($_SESSION['current_section']);
}

// Fully destroy session only if you want to reset everything
// session_destroy();

// Redirect back to landing page
header("Location: ../index.php");
exit;
?>
