<?php
// functions/approve.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();

$student_id = $_GET['id'] ?? '';
if ($student_id === '') {
    die("Invalid student ID.");
}

// get admin name
$adminName = $_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Administrator';

// mark as approved (active)
$stmt = $pdo->prepare("
    UPDATE alumni
    SET status = 'active',
        validated_by = :validated_by,
        validated_date = NOW()
    WHERE student_id = :student_id
");
$stmt->execute([
    ':validated_by' => $adminName,
    ':student_id' => $student_id
]);

// optional — ensure this alumni also exists in ccs_alumni if program is CCS
$stmtCheck = $pdo->prepare("SELECT student_id FROM ccs_alumni WHERE student_id = :id");
$stmtCheck->execute([':id' => $student_id]);
if (!$stmtCheck->fetch()) {
    // auto-insert minimal row if missing
    $stmtInsert = $pdo->prepare("
        INSERT INTO ccs_alumni (student_id, course, year_grad, surname, given_name)
        SELECT student_id, course_year, tertiary_yr, surname, given_name
        FROM alumni WHERE student_id = :id
    ");
    $stmtInsert->execute([':id' => $student_id]);
}

$_SESSION['flash_message'] = "✅ Alumni record ($student_id) approved successfully.";
header("Location: ../pages/adminDashboard.php");
exit;
