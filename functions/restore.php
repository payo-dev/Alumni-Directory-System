<?php
// functions/restore.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();
$student_id = $_GET['id'] ?? '';
if ($student_id === '') die("Invalid student ID.");

// ✅ Restore alumni — set status = 'active'
$stmt = $pdo->prepare("
    UPDATE alumni_info
    SET status = 'active',
        validated_date = NOW()
    WHERE student_id = :student_id
");
$stmt->execute([':student_id' => $student_id]);

$_SESSION['flash_message'] = "♻️ Alumni record ($student_id) restored to active.";
header("Location: ../pages/adminDashboard.php");
exit;
