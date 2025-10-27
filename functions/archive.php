<?php
// ==========================================================
// functions/archive.php — Move Approved Alumni to Archive
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
    // ✅ Fetch the record from active_alumni
    $stmt = $pdo->prepare("SELECT * FROM active_alumni WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        $_SESSION['flash_message'] = "❌ Record not found in active alumni.";
        header("Location: ../pages/adminDashboard.php");
        exit;
    }

    // ✅ Insert into archived_alumni
    $fields = array_keys($record);
    $columns = implode(',', $fields);
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $values = array_values($record);

    $insert = $pdo->prepare("INSERT INTO archived_alumni ($columns) VALUES ($placeholders)");
    $insert->execute($values);

    // ✅ Delete from active_alumni
    $delete = $pdo->prepare("DELETE FROM active_alumni WHERE id = ?");
    $delete->execute([$id]);

    $_SESSION['flash_message'] = "🗂️ Alumni record archived successfully.";
} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error archiving record: " . $e->getMessage();
}

header("Location: ../pages/adminDashboard.php");
exit;
?>
