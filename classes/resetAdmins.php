<?php
// ==========================================================
// classes/resetAdmins.php — One-click Admin Account Reset
// ==========================================================
require_once __DIR__ . '/database.php';

try {
    $pdo = Database::getPDO();

    // Drop old table if exists
    $pdo->exec("DROP TABLE IF EXISTS admin_account");

    // Recreate clean admin_account table
    $pdo->exec("
        CREATE TABLE admin_account (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Securely hash passwords
    $hash1 = password_hash('admin123', PASSWORD_DEFAULT);
    $hash2 = password_hash('admin321', PASSWORD_DEFAULT);

    // Insert both admins
    $stmt = $pdo->prepare("INSERT INTO admin_account (username, password, full_name) VALUES (?, ?, ?)");
    $stmt->execute(['payo.dev', $hash1, 'Mathew Payopelin']);
    $stmt->execute(['sirJaydee', $hash2, 'Jaydee Ballaho']);

    echo "<h2>✅ Admin accounts reset successfully!</h2>";
    echo "<p><strong>Login credentials:</strong></p>";
    echo "<ul>
            <li>Username: <b>payo.dev</b> — Password: <b>admin123</b></li>
            <li>Username: <b>sirJaydee</b> — Password: <b>admin321</b></li>
          </ul>";
    echo "<p>Now try logging in at: <a href='../pages/adminLogin.php'>Admin Login Page</a></p>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>❌ Error resetting admins:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
