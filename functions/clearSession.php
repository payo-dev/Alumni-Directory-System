<?php
// ==========================================================
// functions/clearSession.php — Clears Form Session Safely
// ==========================================================

// ✅ Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------
// 🧹 Remove form-related session variables only
// ----------------------------------------------------------
$formKeys = ['form_data', 'current_section', 'application_type', 'program'];

foreach ($formKeys as $key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

// ----------------------------------------------------------
// 🧭 Optional: also clear POST cache (for browser back button)
// ----------------------------------------------------------
if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'index.php')) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
}

// ----------------------------------------------------------
// 🚀 Redirect user back to Landing Page
// ----------------------------------------------------------
header("Location: ../index.php");
exit;
