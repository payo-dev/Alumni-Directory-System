<?php
<<<<<<< Updated upstream
// ==========================================================
// functions/approve.php — Approve Pending Alumni Record
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

try {
    $pdo = Database::getPDO();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        throw new Exception("Invalid record ID.");
    }

    // ✅ 1. Fetch the pending record
    $stmt = $pdo->prepare("SELECT * FROM pending_alumni WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        throw new Exception("Record not found in pending_alumni.");
    }

    // ✅ 2. Get columns for both tables
    $pendingCols = $pdo->query("SHOW COLUMNS FROM pending_alumni")->fetchAll(PDO::FETCH_COLUMN);
    $activeCols = $pdo->query("SHOW COLUMNS FROM active_alumni")->fetchAll(PDO::FETCH_COLUMN);

    // ✅ 3. Keep only matching columns
    $commonCols = array_intersect($pendingCols, $activeCols);

    // ✅ 4. Prepare the INSERT query dynamically
    $insertCols = implode(",", array_map(fn($col) => "`$col`", $commonCols));
    $placeholders = implode(",", array_map(fn($col) => ":$col", $commonCols));

    $insert = $pdo->prepare("INSERT INTO active_alumni ($insertCols) VALUES ($placeholders)");

    foreach ($commonCols as $col) {
        $insert->bindValue(":$col", $record[$col]);
    }

    $insert->execute();

    // ✅ 5. Delete from pending_alumni
    $delete = $pdo->prepare("DELETE FROM pending_alumni WHERE id = ?");
    $delete->execute([$id]);

    // ✅ 6. Optional: Log who validated (if session admin exists)
    $adminName = $_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Unknown';
    $pdo->prepare("UPDATE active_alumni SET validated_by = ?, validated_date = NOW() WHERE id = LAST_INSERT_ID()")
        ->execute([$adminName]);

    // ✅ 7. Add flash message and redirect
    $_SESSION['flash_message'] = "✅ Record approved successfully and moved to Active Alumni.";
    header("Location: ../pages/adminDashboard.php");
    exit;

} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error approving record: " . $e->getMessage();
    header("Location: ../pages/adminDashboard.php");
    exit;
}
?>
=======
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
>>>>>>> Stashed changes
