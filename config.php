<?php
// ======================================================
// CONFIGURATION FILE — Global System Settings
// ======================================================

// Start session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================================================
// ADMIN CREDENTIALS (Fallback if admin_account table fails)
// ======================================================
define('ADMIN_USERNAME', 'payo.dev');
define('ADMIN_PASSWORD', 'admin123');

// Helper function for redirects
function redirect($path) {
    header("Location: " . BASE_URL . "/pages/thankYou.php?id=" . $insertId);
    exit();
}

// ======================================================
// DATABASE CONNECTION SETTINGS
// ======================================================

// ✅ Database credentials for local XAMPP setup
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'old_alumni_db'); // <-- Your working database name
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default = empty password

// ✅ Optional: define base URL for redirects
define('BASE_URL', '/cssAlumniDirectorySystem/');

// ======================================================
// GLOBAL PDO INSTANCE (optional use outside classes)
// ======================================================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
