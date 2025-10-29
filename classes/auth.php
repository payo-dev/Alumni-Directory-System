<?php
// ==========================================================
// classes/auth.php — Secure Admin Authentication
// ==========================================================
require_once __DIR__ . '/database.php';

class Auth {

    public static function login(string $username, string $password): bool {
        // Ensure no duplicate session_start warnings
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::getPDO();

        $sql = "SELECT id, username, password, full_name
                FROM admin_account
                WHERE username = :username
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Securely verify hashed password
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_fullname'] = $admin['full_name'];
            return true;
        }

        return false;
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        header("Location: /cssAlumniDirectorySystem/pages/adminLogin.php");
        exit;
    }

    public static function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['admin_logged_in']);
    }

    public static function restrict(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['admin_logged_in'])) {
            header("Location: /cssAlumniDirectorySystem/pages/adminLogin.php");
            exit;
        }
    }
}
