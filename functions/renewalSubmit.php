<?php
// ==========================================================
// functions/renewalSubmit.php — Handles Renewal Updates
// ==========================================================
require_once __DIR__ . '/../classes/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$pdo = Database::getPDO();
$form = $_POST;

try {
    $sql = "UPDATE alumni SET
                contact_number = :contact_number,
                region = :region,
                province = :province,
                city_municipality = :city_municipality,
                barangay = :barangay,
                company_name = :company_name,
                position = :position,
                company_address = :company_address,
                company_contact = :company_contact,
                renewal_status = 'pending'
            WHERE student_id = :student_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':contact_number' => $form['contact_number'] ?? null,
        ':region' => $form['region'] ?? null,
        ':province' => $form['province'] ?? null,
        ':city_municipality' => $form['city_municipality'] ?? null,
        ':barangay' => $form['barangay'] ?? null,
        ':company_name' => $form['company_name'] ?? null,
        ':position' => $form['position'] ?? null,
        ':company_address' => $form['company_address'] ?? null,
        ':company_contact' => $form['company_contact'] ?? null,
        ':student_id' => $form['student_id']
    ]);

    header("Location: ../pages/thankYou.php?type=renewal&student_id=" . urlencode($form['student_id']));
    exit;

} catch (PDOException $e) {
    die("Error updating record: " . $e->getMessage());
}
?>
