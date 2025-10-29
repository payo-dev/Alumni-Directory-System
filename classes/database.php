<?php
// ==========================================================
// classes/database.php — Centralized PDO Handler
// ==========================================================
require_once __DIR__ . '/../config.php';

class Database {
    private static ?PDO $pdo = null;

    public static function getPDO(): PDO {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$pdo;
    }
}
?>
