<?php
// ==========================================================
// functions/submitForm.php — Handles Form Submission + Upload
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$formData = $_SESSION['form_data'] ?? [];
if (empty($formData)) {
    die("No form data found. Please complete the form.");
}

$pdo = Database::getPDO();

// --------------------------------------
// 1️⃣ Handle Image Upload (2x2 picture)
// --------------------------------------
$uploadDir = realpath(__DIR__ . '/../assets/alumni_2x2');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$picturePath = null;
if (!empty($_FILES['2x2_picture']['name'])) {
    $fileTmp = $_FILES['2x2_picture']['tmp_name'];
    $fileName = basename($_FILES['2x2_picture']['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
        $newName = uniqid('alumni_', true) . '.' . $ext;
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Save relative path for DB
            $picturePath = 'assets/alumni_2x2/' . $newName;
        }
    }
}

// --------------------------------------
// 2️⃣ Prepare fields for insert
// --------------------------------------
$fieldsMap = [
    'type_of_application' => $formData['type_of_application'] ?? 'New',
    'picture_path' => $picturePath,
    'student_id' => $formData['student_id'] ?? null,
    'batch_name' => $formData['batch_name'] ?? null,
    'surname' => $formData['surname'] ?? null,
    'given_name' => $formData['given_name'] ?? null,
    'middle_name' => $formData['middle_name'] ?? null,
    'course_year' => $formData['course_year'] ?? null,
    'present_address' => $formData['present_address'] ?? null,
    'contact_number' => $formData['contact_number'] ?? null,
    'email' => $formData['email'] ?? null,
    'birthday' => $formData['birthday'] ?? null,
    'blood_type' => $formData['blood_type'] ?? null,
    'elementary_school' => $formData['elemSchool'] ?? null,
    'elementary_yr' => $formData['elemYear'] ?? null,
    'junior_high_school' => $formData['hsSchool'] ?? null,
    'junior_high_yr' => $formData['hsYear'] ?? null,
    'senior_high_school' => $formData['shSchool'] ?? null,
    'senior_high_yr' => $formData['shYear'] ?? null,
    'tertiary_school' => $formData['degree'] ?? null,
    'tertiary_yr' => $formData['collegeYear'] ?? null,
    'graduate_school' => $formData['gradSchool'] ?? null,
    'graduate_yr' => $formData['gradYear'] ?? null,
    'company_name' => $formData['companyName'] ?? null,
    'position' => $formData['jobTitle'] ?? null,
    'company_address' => $formData['companyAddress'] ?? null,
    'company_contact' => $formData['companyContact'] ?? null,
    'emergency_name' => $formData['emergencyName'] ?? null,
    'emergency_address' => $formData['emergencyAddress'] ?? null,
    'emergency_contact' => $formData['emergencyContact'] ?? null,
];

// --------------------------------------
// 3️⃣ Insert into database
// --------------------------------------
$cols = $placeholders = $params = [];
foreach ($fieldsMap as $col => $val) {
    $cols[] = "`$col`";
    $placeholders[] = ":$col";
    $params[":$col"] = $val;
}
$cols[] = "`status`";
$placeholders[] = ":status";
$params[':status'] = 'pending';

// Automatically set submission (issued) date
$cols[] = "`issued_date`";
$placeholders[] = "CURDATE()";

$sql = "INSERT INTO alumni_ccs (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$insertId = $pdo->lastInsertId();
unset($_SESSION['form_data']);

header("Location: ../pages/thankYou.php?id=" . $insertId);
exit;
