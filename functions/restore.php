<?php
// ==========================================================
// functions/restore.php — Restore Archived Alumni
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';
require_once __DIR__ . '/../classes/auth.php';
Auth::restrict();

$pdo = Database::getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash_message'] = "❌ Invalid alumni ID.";
    header("Location: ../pages/adminDashboard.php");
    exit;
}

try {
    // ✅ Fetch the record from archived_alumni
    $stmt = $pdo->prepare("SELECT * FROM archived_alumni WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        $_SESSION['flash_message'] = "❌ Record not found in archive.";
        header("Location: ../pages/adminDashboard.php");
        exit;
    }

    // ✅ Insert into active_alumni
    $fields = array_keys($record);
    $columns = implode(',', $fields);
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $values = array_values($record);

    $insert = $pdo->prepare("INSERT INTO active_alumni ($columns) VALUES ($placeholders)");
    $insert->execute($values);

    // ✅ Delete from archived_alumni
    $delete = $pdo->prepare("DELETE FROM archived_alumni WHERE id = ?");
    $delete->execute([$id]);

    $_SESSION['flash_message'] = "♻️ Alumni record restored successfully.";
} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error restoring record: " . $e->getMessage();
}

header("Location: ../pages/adminDashboard.php");
exit;
?>
