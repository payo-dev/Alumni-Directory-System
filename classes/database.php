<?php
// ==========================================================
// classes/database.php — Centralized PDO Handler (Expanded)
// ==========================================================
require_once __DIR__ . '/../config.php';

class Database {
    private static ?PDO $pdo = null;

    // -------------------------------------------------------
    // Get PDO Connection
    // -------------------------------------------------------
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

    // -------------------------------------------------------
    // Safe Query Helper (Shorter syntax)
    // -------------------------------------------------------
    public static function run(string $sql, array $params = []): PDOStatement {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // -------------------------------------------------------
    // Transaction Helpers
    // -------------------------------------------------------
    public static function begin(): void {
        self::getPDO()->beginTransaction();
    }

    public static function commit(): void {
        self::getPDO()->commit();
    }

    public static function rollback(): void {
        if (self::getPDO()->inTransaction()) {
            self::getPDO()->rollBack();
        }
    }
}
?>
