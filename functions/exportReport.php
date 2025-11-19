<?php
// ==========================================================
// functions/exportReport.php — CSV Export (Unified Schema)
// ==========================================================
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();

$filterCourse  = trim($_REQUEST['filter_course'] ?? '');
$filterYear    = trim($_REQUEST['filter_year'] ?? '');
$filterStatus  = trim($_REQUEST['filter_status'] ?? '');
$filterCity    = trim($_REQUEST['filter_city'] ?? '');
$filterEmp     = trim($_REQUEST['filter_employment'] ?? '');
$dateFrom      = trim($_REQUEST['date_from'] ?? '');
$dateTo        = trim($_REQUEST['date_to'] ?? '');

$where = [];
$params = [];

if ($filterCourse !== '') {
    $where[] = "ca.course = :course";
    $params[':course'] = $filterCourse;
}
if ($filterYear !== '') {
    $where[] = "ca.year_graduated = :yr";
    $params[':yr'] = $filterYear;
}
if ($filterStatus !== '') {
    $where[] = "ai.status = :status";
    $params[':status'] = $filterStatus;
}
if ($filterCity !== '') {
    $where[] = "ai.city_municipality = :city";
    $params[':city'] = $filterCity;
}
if ($filterEmp === 'employed') {
    $where[] = "(er.company_name IS NOT NULL AND TRIM(er.company_name) <> '')";
} elseif ($filterEmp === 'unemployed') {
    $where[] = "(er.company_name IS NULL OR TRIM(er.company_name) = '')";
}
if ($dateFrom !== '' && $dateTo !== '') {
    $where[] = "DATE(ai.created_at) BETWEEN :from AND :to";
    $params[':from'] = $dateFrom;
    $params[':to']   = $dateTo;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$selectedCols = $_POST['selected_columns'] ?? [
    'ca.student_id', 'ca.surname', 'ca.firstname',
    'ca.course', 'ca.year_graduated',
    'ai.city_municipality', 'er.company_name', 'ai.status'
];

$colList = implode(', ', $selectedCols);

$sql = "
    SELECT $colList
    FROM colleges_alumni ca
    LEFT JOIN alumni_info ai ON ai.student_id = ca.student_id
    LEFT JOIN alumni_emp_record er ON er.student_id = ca.student_id
    $whereSQL
    ORDER BY ca.year_graduated DESC, ca.surname ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    die('No records found for selected filters.');
}

// ==========================================================
// CSV OUTPUT
// ==========================================================
$filename = 'CCS_Alumni_Report_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");
fputcsv($output, array_keys($rows[0]));

foreach ($rows as $r) {
    fputcsv($output, array_map(fn($v) => $v ?? '', $r));
}
fclose($output);
exit;
