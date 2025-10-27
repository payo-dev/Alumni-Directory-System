<?php
// functions/approve.php
require_once __DIR__ . '/auth.php';
requireAdmin();
require_once __DIR__ . '/../classes/database.php';

$pdo = Database::getPDO();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: /pages/adminDashboard.php");
    exit;
}

$adminName = $_SESSION['admin_fullname'] ?? ($_SESSION['admin_username'] ?? 'Admin');

try {
    $pdo->beginTransaction();

    // 1) Copy record into active_alumni (only if not already copied)
    // Use explicit column list — adjust if you added/removed columns
    $cols = [
        'type_of_application','picture_path','student_id','batch_name','surname','given_name','middle_name',
        'course_year','present_address','contact_number','email','birthday','blood_type',
        'elementary_school','elementary_yr','junior_high_school','junior_high_yr',
        'senior_high_school','senior_high_yr','tertiary_school','tertiary_yr',
        'graduate_school','graduate_yr','company_name','position','company_address','company_contact',
        'emergency_name','emergency_address','emergency_contact','is_archived','created_at','updated_at'
    ];

    // Build column lists for INSERT ... SELECT
    $insertCols = implode(',', $cols);
    $selectCols = implode(',', $cols);

    // Add validated_by and validated_date to insert
    $sqlInsert = "INSERT INTO active_alumni ({$insertCols}, validated_by, validated_date) 
                  SELECT {$selectCols}, :vby, NOW() FROM pending_alumni WHERE id = :id";

    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute([':vby' => $adminName, ':id' => $id]);

    // 2) Update pending_alumni status
    $stmt2 = $pdo->prepare("UPDATE pending_alumni SET status = 'approved', validated_by = :vby, validated_date = NOW() WHERE id = :id");
    $stmt2->execute([':vby' => $adminName, ':id' => $id]);

    $pdo->commit();

    // Redirect back to dashboard (you could add a success param)
    header("Location: /pages/adminDashboard.php");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    // For debugging you might echo $e->getMessage(); in production, log it instead
    die("Approval failed: " . $e->getMessage());
}
