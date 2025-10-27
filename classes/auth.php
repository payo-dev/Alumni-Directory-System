<?php
// ==========================================================
// classes/auth.php — Admin Authentication Handler
// ==========================================================
require_once __DIR__ . '/database.php';

class Auth {

    /**
     * Attempt to log in admin using plain-text password (as requested).
     * Returns true on success, false on failure.
     */
    public static function login(string $username, string $password): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::getPDO();

        // ✅ UPDATED TABLE NAME to `admin_account`
        $sql = "SELECT id, username, password, full_name
                FROM admin_account
                WHERE username = :username
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Plain-text password check (for local dev)
        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_fullname'] = $admin['full_name'];
            return true;
        }

        return false;
    }

    // ✅ Logout
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: /cssAlumniDirectorySystem/pages/adminLogin.php");
        exit;
    }

    // ✅ Check login state
    public static function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['admin_logged_in']);
    }

    // ✅ Restrict pages to logged-in admin
    public static function restrict(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['admin_logged_in'])) {
            header("Location: /cssAlumniDirectorySystem/pages/adminLogin.php");
            exit;
        }
    }
}
