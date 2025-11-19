<?php
// ==========================================================
// classes/auth.php — Admin Authentication Handler
// ==========================================================
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../config.php';

class Auth {
    // -------------------------------------------------------
    // Login check
    // -------------------------------------------------------
    public static function login(string $username, string $password): bool {
        $pdo = Database::getPDO();

        // Try to find user in the database
        $stmt = $pdo->prepare("SELECT * FROM admin_account WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        // If found in DB, verify password
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_fullname'] = $admin['full_name']; // ✅ fixed underscore
            $_SESSION['is_admin'] = true;
            return true;
        }

        // Fallback credentials from config.php
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_id'] = 0;
            $_SESSION['admin_username'] = ADMIN_USERNAME;
            $_SESSION['admin_fullname'] = ADMIN_FULLNAME;
            $_SESSION['is_admin'] = true;
            return true;
        }

        return false;
    }

    // -------------------------------------------------------
    // Restrict access to logged-in admins only
    // -------------------------------------------------------
    public static function restrict(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['is_admin'])) {
            header("Location: adminLogin.php");
            exit;
        }
    }

    // -------------------------------------------------------
    // Logout
    // -------------------------------------------------------
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
        header("Location: ../pages/adminLogin.php");
        exit;
    }
}
