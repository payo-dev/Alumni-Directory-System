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
                           SET status = 'archived', is_archived = 1, validated_date = NOW()
                           WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $_SESSION['flash_message'] = "✅ Archived successfully.";
} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error archiving: " . $e->getMessage();
}
header("Location: ../pages/adminDashboard.php");
exit;
