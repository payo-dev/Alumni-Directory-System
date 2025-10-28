<?php
// ==========================================================
// functions/checkRenewal.php — Validate Renewal Email
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/renewalVerification.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    $_SESSION['renewal_error'] = "Please enter your Gmail address.";
    header('Location: ../pages/renewalVerification.php');
    exit;
}

try {
    $pdo = Database::getPDO();

    // ✅ Check if email exists and status = active
    $stmt = $pdo->prepare("SELECT * FROM alumni WHERE email = :email AND status = 'active' LIMIT 1");
    $stmt->execute([':email' => $email]);
    $alumni = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumni) {
        $_SESSION['renewal_error'] = "No active alumni found with that Gmail.";
        header('Location: ../pages/renewalVerification.php');
        exit;
    }

    // ✅ Store record for renewal prefill
    $_SESSION['form_data'] = $alumni;
    $_SESSION['renewal_id'] = $alumni['student_id'];
    $_SESSION['application_type'] = 'Renewal';

    // ✅ Redirect to the first section with type=Renewal
    header('Location: ../index.php?section=alumni&program=ccs&type=Renewal');
    exit;

} catch (PDOException $e) {
    $_SESSION['renewal_error'] = "Database error: " . $e->getMessage();
    header('Location: ../pages/renewalVerification.php');
    exit;
}
