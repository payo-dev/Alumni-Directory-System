<?php
// ==========================================================
<<<<<<< Updated upstream
//  DATABASE CONNECTION CLASS
=======
// classes/database.php — Centralized PDO Handler
>>>>>>> Stashed changes
// ==========================================================
require_once __DIR__ . '/../config.php';

class Database {
    private static ?PDO $pdo = null;

    public static function getPDO(): PDO {
<<<<<<< Updated upstream
        if (!self::$pdo) {
            self::$pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
=======
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
>>>>>>> Stashed changes
        }
        return self::$pdo;
    }
}
