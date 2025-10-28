<?php
<<<<<<< Updated upstream
// ==========================================================
// functions/reject.php — Moves record from pending_alumni → archived_alumni
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';

=======
// functions/reject.php
session_start();
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
>>>>>>> Stashed changes
Auth::restrict();

$pdo = Database::getPDO();

<<<<<<< Updated upstream
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "❌ Invalid record ID.";
    header("Location: ../pages/adminDashboard.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    // ✅ Fetch record
    $stmt = $pdo->prepare("SELECT * FROM pending_alumni WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        $_SESSION['flash_message'] = "❌ Record not found.";
        header("Location: ../pages/adminDashboard.php");
        exit;
    }

    // ✅ Ensure archived_alumni table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS archived_alumni LIKE pending_alumni");

    // ✅ Insert into archived_alumni
    $columns = array_keys($record);
    $placeholders = array_map(fn($col) => ":$col", $columns);
    $insertSQL = "INSERT INTO archived_alumni (" . implode(",", $columns) . ")
                  VALUES (" . implode(",", $placeholders) . ")";
    $insert = $pdo->prepare($insertSQL);
    $insert->execute($record);

    // ✅ Delete from pending_alumni
    $delete = $pdo->prepare("DELETE FROM pending_alumni WHERE id = ?");
    $delete->execute([$id]);

    $_SESSION['flash_message'] = "⚠️ Record rejected and archived. Applicant should be contacted.";
    header("Location: ../pages/adminDashboard.php");
    exit;

} catch (Exception $e) {
    $_SESSION['flash_message'] = "❌ Error rejecting record: " . $e->getMessage();
    header("Location: ../pages/adminDashboard.php");
    exit;
}
=======
$student_id = $_GET['id'] ?? '';
if ($student_id === '') {
    die("Invalid student ID.");
}

$stmt = $pdo->prepare("
    UPDATE alumni
    SET status = 'archived',
        validated_date = NOW()
    WHERE student_id = :student_id
");
$stmt->execute([':student_id' => $student_id]);

$_SESSION['flash_message'] = "⚠️ Alumni record ($student_id) archived / rejected.";
header("Location: ../pages/adminDashboard.php");
exit;
>>>>>>> Stashed changes
