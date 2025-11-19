<?php
// ==========================================================
// functions/submitForm.php — Simplified & Compatible Version
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['form_data'])) {
    header("Location: ../index.php");
    exit;
}

$formData = $_SESSION['form_data'];
$pdo = Database::getPDO();

try {
    Database::begin();

    // --------------------------------------
    // 1️⃣ Handle 2x2 Picture Upload
    // --------------------------------------
    $picturePath = null;
    $uploadDir = __DIR__ . '/../assets/alumni_2x2';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (!empty($_FILES['2x2_picture']['name'])) {
        $ext = strtolower(pathinfo($_FILES['2x2_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $newName = uniqid('alumni_', true) . '.' . $ext;
            $finalPath = $uploadDir . '/' . $newName;
            if (move_uploaded_file($_FILES['2x2_picture']['tmp_name'], $finalPath)) {
                $picturePath = 'assets/alumni_2x2/' . $newName;
            }
        }
    }

    $studentId = $formData['student_id'] ?? null;

    // --------------------------------------
    // 2️⃣ Insert into colleges_alumni (main)
    // --------------------------------------
    $sqlMain = "INSERT INTO colleges_alumni 
        (student_id, college_name, course, year_graduated, surname, firstname)
        VALUES
        (:student_id, 'College of Computing Studies', :course, :year_graduated, :surname, :given_name)
        ON DUPLICATE KEY UPDATE
            course = VALUES(course),
            year_graduated = VALUES(year_graduated),
            surname = VALUES(surname),
            firstname = VALUES(firstname)";

    Database::run($sqlMain, [
        ':student_id' => $studentId,
        ':course' => $formData['course'] ?? null,
        ':year_graduated' => $formData['year_graduated'] ?? null,
        ':surname' => $formData['surname'] ?? null,
        ':given_name' => $formData['given_name'] ?? null,
    ]);

    // --------------------------------------
    // 3️⃣ alumni_info
    // --------------------------------------
    Database::run("REPLACE INTO alumni_info 
        (student_id, province, city_municipality, barangay, birthday, blood_type, picture_path, status, created_at)
        VALUES (:student_id, :province, :city_municipality, :barangay, :birthday, :blood_type, :picture_path, 'pending', NOW())", [
        ':student_id' => $studentId,
        ':province' => $formData['province'] ?? null,
        ':city_municipality' => $formData['city_municipality'] ?? null,
        ':barangay' => $formData['barangay'] ?? null,
        ':birthday' => $formData['birthday'] ?? null,
        ':blood_type' => $formData['blood_type'] ?? null,
        ':picture_path' => $picturePath,
    ]);

    // --------------------------------------
    // 4️⃣ alumni_edu_bg
    // --------------------------------------
    Database::run("REPLACE INTO alumni_edu_bg 
        (student_id, elementary_school, elementary_yr, junior_high_school, junior_high_yr,
         senior_high_school, senior_high_yr, tertiary_school, tertiary_yr)
        VALUES
        (:student_id, :elemSchool, :elemYear, :hsSchool, :hsYear, :shSchool, :shYear,
         :degree, :collegeYear)", [
        ':student_id' => $studentId,
        ':elemSchool' => $formData['elemSchool'] ?? null,
        ':elemYear' => $formData['elemYear'] ?? null,
        ':hsSchool' => $formData['hsSchool'] ?? null,
        ':hsYear' => $formData['hsYear'] ?? null,
        ':shSchool' => $formData['shSchool'] ?? null,
        ':shYear' => $formData['shYear'] ?? null,
        ':degree' => $formData['degree'] ?? null,
        ':collegeYear' => $formData['collegeYear'] ?? null,
    ]);

    // --------------------------------------
    // 5️⃣ alumni_emp_record
    // --------------------------------------
    Database::run("REPLACE INTO alumni_emp_record 
        (student_id, company_name, position, company_address, company_contact)
        VALUES
        (:student_id, :company_name, :position, :company_address, :company_contact)", [
        ':student_id' => $studentId,
        ':company_name' => $formData['companyName'] ?? null,
        ':position' => $formData['jobTitle'] ?? null,
        ':company_address' => $formData['companyAddress'] ?? null,
        ':company_contact' => $formData['companyContact'] ?? null,
    ]);

    // --------------------------------------
    // 6️⃣ alumni_emer_contact
    // --------------------------------------
    Database::run("REPLACE INTO alumni_emer_contact
        (student_id, emergency_name, emergency_address, emergency_contact)
        VALUES
        (:student_id, :emergency_name, :emergency_address, :emergency_contact)", [
        ':student_id' => $studentId,
        ':emergency_name' => $formData['emergencyName'] ?? null,
        ':emergency_address' => $formData['emergencyAddress'] ?? null,
        ':emergency_contact' => $formData['emergencyContact'] ?? null,
    ]);

    // --------------------------------------
    // 7️⃣ Commit + Redirect
    // --------------------------------------
    Database::commit();
    unset($_SESSION['form_data']);

    header("Location: ../pages/thankYou.php?id=" . urlencode($studentId));
    exit;

} catch (Exception $e) {
    Database::rollback();
    echo "<h2 style='color:red;'>Database Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
