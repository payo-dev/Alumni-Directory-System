<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    $_SESSION['renewal_error'] = "Please enter your email address.";
    header('Location: ../index.php?section=alumni&type=Renewal');
    exit;
}

$pdo = Database::getPDO();
$stmt = $pdo->prepare("SELECT * FROM alumni_ccs WHERE email = :email AND status = 'active' LIMIT 1");
$stmt->execute([':email' => $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['renewal_error'] = "No active alumni found with that email.";
    header('Location: ../index.php?section=alumni&type=Renewal');
    exit;
}

$_SESSION['form_data'] = $row;
$_SESSION['renewal_id'] = $row['id'];

header('Location: ../index.php?section=alumni&type=Renewal');
exit;
