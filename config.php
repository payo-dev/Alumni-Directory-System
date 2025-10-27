<?php
// ======================================================
// SESSION + ADMIN CONFIG
// ======================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hardcoded admin credentials (temporary fallback)
define('ADMIN_USERNAME', 'payo.dev');
define('ADMIN_PASSWORD', 'admin123');

// ======================================================
// DATABASE CONNECTION CONFIGURATION
// ======================================================

// Edit these values if your DB credentials differ
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'alumni_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default for XAMPP = empty string

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
