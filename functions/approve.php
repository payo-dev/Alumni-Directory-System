<?php
// functions/approve.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();
$student_id = $_GET['id'] ?? '';
if ($student_id === '') die("Invalid student ID.");

// Get admin name
$adminName = $_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Administrator';

// ✅ Approve alumni — set status = 'active'
$stmt = $pdo->prepare("
    UPDATE alumni_info
    SET status = 'active',
        validated_by = :validated_by,
        validated_date = NOW()
    WHERE student_id = :student_id
");
$stmt->execute([
    ':validated_by' => $adminName,
    ':student_id' => $student_id
]);

$_SESSION['flash_message'] = "✅ Alumni record ($student_id) approved successfully.";
header("Location: ../pages/adminDashboard.php");
exit;
