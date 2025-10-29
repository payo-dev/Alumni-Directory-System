<?php
// ==========================================================
// functions/submitForm.php — Final Clean Version (Linked to alumni only)
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
    // --------------------------------------
    // 1️⃣ Handle 2x2 Picture Upload
    // --------------------------------------
    $picturePath = null;

    if (!empty($formData['2x2_picture_path'])) {
        $tempPath = __DIR__ . '/../' . $formData['2x2_picture_path'];
        $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION));
        $finalDir = __DIR__ . '/../assets/alumni_2x2';
        if (!is_dir($finalDir)) mkdir($finalDir, 0777, true);

        $newName = uniqid('alumni_', true) . '.' . $ext;
        $finalPath = $finalDir . DIRECTORY_SEPARATOR . $newName;

        if (rename($tempPath, $finalPath)) {
            $picturePath = 'assets/alumni_2x2/' . $newName;
            unset($_SESSION['form_data']['2x2_picture_path']);
        }
    } elseif (!empty($_FILES['2x2_picture']['name'])) {
        $ext = strtolower(pathinfo($_FILES['2x2_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $finalDir = __DIR__ . '/../assets/alumni_2x2';
            if (!is_dir($finalDir)) mkdir($finalDir, 0777, true);
            $newName = uniqid('alumni_', true) . '.' . $ext;
            $finalPath = $finalDir . DIRECTORY_SEPARATOR . $newName;
            if (move_uploaded_file($_FILES['2x2_picture']['tmp_name'], $finalPath)) {
                $picturePath = 'assets/alumni_2x2/' . $newName;
            }
        }
    }

    // --------------------------------------
    // 2️⃣ Prepare Data for alumni table
    // --------------------------------------
    $fieldsMap = [
        'student_id' => $formData['student_id'] ?? null,
        'type_of_application' => $_GET['type'] ?? 'New',
        'picture_path' => $picturePath,
        'batch_name' => $formData['batch_name'] ?? null,
        'surname' => $formData['surname'] ?? null,
        'given_name' => $formData['given_name'] ?? null,
        'middle_name' => $formData['middle_name'] ?? null,
        'region' => $formData['region'] ?? null,
        'province' => $formData['province'] ?? null,
        'city_municipality' => $formData['city_municipality'] ?? null,
        'barangay' => $formData['barangay'] ?? null,
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
        'emergency_contact' => $formData['emergencyContact'] ?? null
    ];

    // --------------------------------------
    // 3️⃣ Insert into alumni (no pending_alumni table)
    // --------------------------------------
    $columns = array_keys($fieldsMap);
    $placeholders = array_map(fn($c) => ":$c", $columns);

    $sql = "INSERT INTO alumni (" . implode(',', $columns) . ", status, is_approved, issued_date)
            VALUES (" . implode(',', $placeholders) . ", 'pending', 0, NOW())
            ON DUPLICATE KEY UPDATE
                type_of_application = VALUES(type_of_application),
                picture_path = VALUES(picture_path),
                batch_name = VALUES(batch_name),
                surname = VALUES(surname),
                given_name = VALUES(given_name),
                middle_name = VALUES(middle_name),
                region = VALUES(region),
                province = VALUES(province),
                city_municipality = VALUES(city_municipality),
                barangay = VALUES(barangay),
                contact_number = VALUES(contact_number),
                email = VALUES(email),
                birthday = VALUES(birthday),
                blood_type = VALUES(blood_type),
                elementary_school = VALUES(elementary_school),
                elementary_yr = VALUES(elementary_yr),
                junior_high_school = VALUES(junior_high_school),
                junior_high_yr = VALUES(junior_high_yr),
                senior_high_school = VALUES(senior_high_school),
                senior_high_yr = VALUES(senior_high_yr),
                tertiary_school = VALUES(tertiary_school),
                tertiary_yr = VALUES(tertiary_yr),
                graduate_school = VALUES(graduate_school),
                graduate_yr = VALUES(graduate_yr),
                company_name = VALUES(company_name),
                position = VALUES(position),
                company_address = VALUES(company_address),
                company_contact = VALUES(company_contact),
                emergency_name = VALUES(emergency_name),
                emergency_address = VALUES(emergency_address),
                emergency_contact = VALUES(emergency_contact),
                status = 'pending',
                is_approved = 0";

    $stmt = $pdo->prepare($sql);
    foreach ($fieldsMap as $col => $val) {
        $stmt->bindValue(":$col", $val);
    }
    $stmt->execute();

    // --------------------------------------
    // 4️⃣ Sync with ccs_alumni table
    // --------------------------------------
    $studentId = $formData['student_id'] ?? null;
    $course = $formData['course'] ?? null;
    $yearGrad = $formData['year_graduated'] ?? null;
    $surname = $formData['surname'] ?? null;
    $given_name = $formData['given_name'] ?? null;

    if ($studentId && $course && $yearGrad) {
        $stmtCCS = $pdo->prepare("
            INSERT INTO ccs_alumni (student_id, course, year_graduated, surname, firstname)
            VALUES (:student_id, :course, :year_graduated, :surname, :firstname)
            ON DUPLICATE KEY UPDATE
                course = VALUES(course),
                year_graduated = VALUES(year_graduated),
                surname = VALUES(surname),
                firstname = VALUES(firstname)
        ");
        $stmtCCS->execute([
            ':student_id' => $studentId,
            ':course' => $course,
            ':year_graduated' => $yearGrad,
            ':surname' => $surname,
            ':firstname' => $given_name
        ]);
    }

    // --------------------------------------
    // 5️⃣ Cleanup + Redirect
    // --------------------------------------
    unset($_SESSION['form_data']);
    header("Location: ../pages/thankYou.php?id=" . urlencode($studentId));
    exit;

} catch (Exception $e) {
    echo "<h2 style='color:red;'>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
