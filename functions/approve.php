<?php
session_start();
require_once __DIR__ . '/../classes/database.php';
require_once __DIR__ . '/../classes/auth.php';

Auth::restrict();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash_message'] = "❌ Invalid ID.";
    header("Location: ../pages/adminDashboard.php");
    exit;
}

$pdo = Database::getPDO();
try {
    $stmt = $pdo->prepare("UPDATE alumni_ccs
                           SET status = 'active', is_validated = 1, validated_by = :vby, validated_date = NOW()
                           WHERE id = :id");
    $stmt->execute([
        ':vby' => $_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'admin',
        ':id' => $id
    ]);
    $_SESSION['flash_message'] = "✅ Approved successfully.";
} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error approving: " . $e->getMessage();
}
header("Location: ../pages/adminDashboard.php");
exit;
