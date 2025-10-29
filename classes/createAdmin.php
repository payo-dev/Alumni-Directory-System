<?php
require_once 'classes/database.php';
$pdo = Database::getPDO();

$username = 'payo.dev';
$fullname = 'System Administrator';
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admin_account (username, password, full_name) VALUES (?, ?, ?)");
$stmt->execute([$username, $passwordHash, $fullname]);

echo "✅ Admin created successfully.<br>";
echo "Username: payo.dev<br>";
echo "Password: admin123 (hashed in DB)";
?>