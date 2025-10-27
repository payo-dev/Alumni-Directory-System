<?php
// functions/reject.php
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

$stmt = $pdo->prepare("UPDATE pending_alumni SET status = 'rejected', validated_by = :vby, validated_date = NOW() WHERE id = :id");
$stmt->execute([':vby' => $adminName, ':id' => $id]);

header("Location: /pages/adminDashboard.php");
exit;