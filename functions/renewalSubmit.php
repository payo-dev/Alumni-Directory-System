<?php
// ==========================================================
// functions/renewalSubmit.php — Renewal update handler (transactional, history-aware)
// ==========================================================
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['student_id'])) {
    header("Location: ../index.php");
    exit;
}

$pdo = Database::getPDO();
$form = $_POST;
$studentId = $form['student_id'];

// -----------------------------------------------------------
// Helper function — Check if column exists before referencing
// -----------------------------------------------------------
function columnExists(PDO $pdo, string $table, string $column): bool {
    $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = :table 
              AND COLUMN_NAME = :column";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':table' => $table, ':column' => $column]);
    return (bool) $stmt->fetchColumn();
}

try {
    Database::begin();

    // ==========================================================
    // 1️⃣ Update surname and renewal status in colleges_alumni
    // ==========================================================
    if (isset($form['surname']) && trim($form['surname']) !== '') {
        $cols = [];

        if (columnExists($pdo, 'colleges_alumni', 'surname')) {
            $cols[] = "surname = :surname";
        }
        if (columnExists($pdo, 'colleges_alumni', 'renewal_status')) {
            $cols[] = "renewal_status = 'pending'";
        }

        if (!empty($cols)) {
            $sql = "UPDATE colleges_alumni 
                    SET " . implode(', ', $cols) . " 
                    WHERE student_id = :student_id";
            Database::run($sql, [
                ':surname' => $form['surname'],
                ':student_id' => $studentId
            ]);
        }
    } else {
        if (columnExists($pdo, 'colleges_alumni', 'renewal_status')) {
            Database::run("UPDATE colleges_alumni 
                           SET renewal_status = 'pending' 
                           WHERE student_id = :student_id", 
                           [':student_id' => $studentId]);
        }
    }

    // ==========================================================
    // 2️⃣ Update alumni_info (address, contact, optional surname)
    // ==========================================================
    $infoCols = [];
    $infoParams = [':student_id' => $studentId];

    $updateFields = [
        'contact_number' => $form['contact_number'] ?? null,
        'region' => $form['region'] ?? null,
        'province' => $form['province'] ?? null,
        'city_municipality' => $form['city_municipality'] ?? null,
        'barangay' => $form['barangay'] ?? null,
    ];

    foreach ($updateFields as $col => $val) {
        if ($val !== null && trim((string)$val) !== '') {
            $infoCols[] = "$col = :$col";
            $infoParams[":$col"] = $val;
        }
    }

    if (isset($form['surname']) && trim($form['surname']) !== '' && columnExists($pdo, 'alumni_info', 'surname')) {
        $infoCols[] = "surname = :surname";
        $infoParams[':surname'] = $form['surname'];
    }

    if (columnExists($pdo, 'alumni_info', 'renewal_status')) {
        $infoCols[] = "renewal_status = 'pending'";
    }

    if (!empty($infoCols)) {
        $sql = "UPDATE alumni_info 
                SET " . implode(', ', $infoCols) . " 
                WHERE student_id = :student_id";
        Database::run($sql, $infoParams);
    }

    // ==========================================================
    // 3️⃣ Employment Record — historical or single update
    // ==========================================================
    $company = trim((string)($form['company_name'] ?? ''));
    $position = trim((string)($form['position'] ?? ''));
    $companyAddress = trim((string)($form['company_address'] ?? ''));
    $companyContact = trim((string)($form['company_contact'] ?? ''));

    $hasEmploymentData = ($company || $position || $companyAddress || $companyContact);

    if ($hasEmploymentData) {
        $hasId = columnExists($pdo, 'alumni_emp_record', 'id');

        if ($hasId) {
            // Mark old employment inactive if applicable
            if (columnExists($pdo, 'alumni_emp_record', 'status')) {
                Database::run("UPDATE alumni_emp_record 
                               SET status = 'inactive' 
                               WHERE student_id = :student_id 
                                 AND status = 'active'", 
                               [':student_id' => $studentId]);
            }

            // Add new active record
            Database::run("INSERT INTO alumni_emp_record 
                           (student_id, company_name, position, company_address, company_contact, status) 
                           VALUES (:student_id, :company_name, :position, :company_address, :company_contact, 'active')", [
                ':student_id' => $studentId,
                ':company_name' => $company ?: null,
                ':position' => $position ?: null,
                ':company_address' => $companyAddress ?: null,
                ':company_contact' => $companyContact ?: null
            ]);
        } else {
            // fallback single-row update
            $updated = Database::run("UPDATE alumni_emp_record 
                                      SET company_name=:company_name, position=:position, company_address=:company_address, 
                                          company_contact=:company_contact, status='active' 
                                      WHERE student_id=:student_id", [
                ':company_name' => $company ?: null,
                ':position' => $position ?: null,
                ':company_address' => $companyAddress ?: null,
                ':company_contact' => $companyContact ?: null,
                ':student_id' => $studentId
            ]);

            if ($updated->rowCount() === 0) {
                Database::run("INSERT INTO alumni_emp_record 
                               (student_id, company_name, position, company_address, company_contact, status) 
                               VALUES (:student_id, :company_name, :position, :company_address, :company_contact, 'active')", [
                    ':student_id' => $studentId,
                    ':company_name' => $company ?: null,
                    ':position' => $position ?: null,
                    ':company_address' => $companyAddress ?: null,
                    ':company_contact' => $companyContact ?: null
                ]);
            }
        }
    }

    // ==========================================================
    // 4️⃣ Emergency Contact — REPLACE INTO (safe upsert)
    // ==========================================================
    $emName = trim((string)($form['emergency_name'] ?? ''));
    $emAddr = trim((string)($form['emergency_address'] ?? ''));
    $emPhone = trim((string)($form['emergency_contact'] ?? ''));

    if ($emName || $emAddr || $emPhone) {
        Database::run("REPLACE INTO alumni_emer_contact 
                       (student_id, emergency_name, emergency_address, emergency_contact) 
                       VALUES (:student_id, :ename, :eaddr, :ephone)", [
            ':student_id' => $studentId,
            ':ename' => $emName ?: null,
            ':eaddr' => $emAddr ?: null,
            ':ephone' => $emPhone ?: null
        ]);
    }

    // ==========================================================
    // 5️⃣ Add new tertiary/college degree if provided
    // ==========================================================
    $newCollege = trim((string)$form['new_tertiary_school'] ?? '');
    $newYear = trim((string)$form['new_tertiary_yr'] ?? '');

    if ($newCollege && preg_match('/^\d{4}$/', $newYear)) {
        Database::run("INSERT INTO alumni_edu_bg 
                       (student_id, tertiary_school, tertiary_yr) 
                       VALUES (:student_id, :tschool, :tyr)", [
            ':student_id' => $studentId,
            ':tschool' => $newCollege,
            ':tyr' => $newYear
        ]);
    }

    // ==========================================================
    // 6️⃣ Ensure renewal_status = 'pending' everywhere
    // ==========================================================
    if (columnExists($pdo, 'alumni_info', 'renewal_status')) {
        Database::run("UPDATE alumni_info 
                       SET renewal_status = 'pending' 
                       WHERE student_id = :student_id", [':student_id' => $studentId]);
    }
    if (columnExists($pdo, 'colleges_alumni', 'renewal_status')) {
        Database::run("UPDATE colleges_alumni 
                       SET renewal_status = 'pending' 
                       WHERE student_id = :student_id", [':student_id' => $studentId]);
    }

    // ==========================================================
    // ✅ Commit + Redirect
    // ==========================================================
    Database::commit();
    unset($_SESSION['form_data']);
    header("Location: ../pages/thankYou.php?type=renewal&student_id=" . urlencode($studentId));
    exit;

} catch (Exception $e) {
    Database::rollback();
    echo "<h2 style='color:red;'>Renewal Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
