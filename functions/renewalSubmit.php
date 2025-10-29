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
    // Base fields always updated
    $fields = [
        'contact_number' => ':contact_number',
        'region' => ':region',
        'province' => ':province',
        'city_municipality' => ':city_municipality',
        'barangay' => ':barangay',
        'company_name' => ':company_name',
        'position' => ':position',
        'company_address' => ':company_address',
        'company_contact' => ':company_contact',
    ];

    // Optional emergency fields (only if user entered something)
    if (!empty($form['emergency_name'])) {
        $fields['emergency_name'] = ':emergency_name';
    }
    if (!empty($form['emergency_address'])) {
        $fields['emergency_address'] = ':emergency_address';
    }
    if (!empty($form['emergency_contact'])) {
        $fields['emergency_contact'] = ':emergency_contact';
    }

    // Build SQL dynamically
    $setClauses = [];
    foreach ($fields as $column => $placeholder) {
        $setClauses[] = "$column = $placeholder";
    }
    $setClauses[] = "renewal_status = 'pending'";

    $sql = "UPDATE alumni SET " . implode(', ', $setClauses) . " WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindValue(':contact_number', $form['contact_number'] ?? null);
    $stmt->bindValue(':region', $form['region'] ?? null);
    $stmt->bindValue(':province', $form['province'] ?? null);
    $stmt->bindValue(':city_municipality', $form['city_municipality'] ?? null);
    $stmt->bindValue(':barangay', $form['barangay'] ?? null);
    $stmt->bindValue(':company_name', $form['company_name'] ?? null);
    $stmt->bindValue(':position', $form['position'] ?? null);
    $stmt->bindValue(':company_address', $form['company_address'] ?? null);
    $stmt->bindValue(':company_contact', $form['company_contact'] ?? null);
    $stmt->bindValue(':student_id', $form['student_id']);

    // Bind optional emergency fields only if present
    if (!empty($form['emergency_name'])) {
        $stmt->bindValue(':emergency_name', $form['emergency_name']);
    }
    if (!empty($form['emergency_address'])) {
        $stmt->bindValue(':emergency_address', $form['emergency_address']);
    }
    if (!empty($form['emergency_contact'])) {
        $stmt->bindValue(':emergency_contact', $form['emergency_contact']);
    }

    $stmt->execute();

    header("Location: ../pages/thankYou.php?type=renewal&student_id=" . urlencode($form['student_id']));
    exit;

} catch (PDOException $e) {
    die("Error updating record: " . htmlspecialchars($e->getMessage()));
}
?>
