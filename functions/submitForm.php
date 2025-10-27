<?php
// ==========================================================
// functions/submitForm.php — Handles final alumni form submission
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../index.php");
        exit;
    }

    $formData = $_SESSION['form_data'] ?? [];

    if (empty($formData)) {
        throw new Exception("No form data found. Please complete the form again.");
    }

    $pdo = Database::getPDO();

    $fieldsMap = [
        'type_of_application' => 'type_of_application',
        'student_id'          => 'student_id',
        'batch_name'          => 'batch_name',
        'surname'             => 'surname',
        'given_name'          => 'given_name',
        'middle_name'         => 'middle_name',
        'course_year'         => 'course_year',
        'present_address'     => 'presentAddress',
        'contact_number'      => 'contactNumber',
        'email'               => 'emailAddress',
        'birthday'            => 'dateOfBirth',
        'elementary_school'   => 'elemSchool',
        'elementary_yr'       => 'elemYear',
        'junior_high_school'  => 'hsSchool',
        'junior_high_yr'      => 'hsYear',
        'tertiary_school'     => 'degree',
        'tertiary_yr'         => 'collegeYear',
        'company_name'        => 'companyName',
        'position'            => 'jobTitle',
        'company_address'     => 'companyAddress',
        'emergency_name'      => 'emergencyName',
        'emergency_address'   => 'emergencyAddress',
        'emergency_contact'   => 'emergencyContact',
    ];

    $insertCols = [];
    $insertVals = [];
    $params = [];

    foreach ($fieldsMap as $dbCol => $formKey) {
        if (!empty($formData[$formKey])) {
            $insertCols[] = "`$dbCol`";
            $insertVals[] = ":$dbCol";
            $params[":$dbCol"] = trim($formData[$formKey]);
        }
    }

    $insertCols[] = "`status`";
    $insertVals[] = ":status";
    $params[":status"] = "pending";

    $sql = "INSERT INTO pending_alumni (" . implode(',', $insertCols) . ")
            VALUES (" . implode(',', $insertVals) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $insertId = $pdo->lastInsertId();

    unset($_SESSION['form_data']);

    // ✅ FIXED: Correct redirect path (since thankYou.php is inside /pages)
    header("Location: ../pages/thankYou.php?id=" . $insertId);
    exit;

} catch (Exception $e) {
    echo "<h2 style='color:red;'>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
