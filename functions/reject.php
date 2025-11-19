<?php
// functions/reject.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();
$student_id = $_GET['id'] ?? '';
if ($student_id === '') die("Invalid student ID.");

// ✅ Reject alumni — set status = 'rejected'
$stmt = $pdo->prepare("
    UPDATE alumni_info
    SET status = 'rejected',
        validated_date = NOW()
    WHERE student_id = :student_id
");
$stmt->execute([':student_id' => $student_id]);

$_SESSION['flash_message'] = "⚠️ Alumni record ($student_id) rejected and moved to archive.";
header("Location: ../pages/adminDashboard.php");
exit;
