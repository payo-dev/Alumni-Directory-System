<?php
// ==========================================================
// functions/exportReport.php — Filtered Alumni CSV Export
// ==========================================================
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

$pdo = Database::getPDO();

/* ==========================================================
   🧭 Collect filters from POST or GET (supports both)
   ========================================================== */
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
    $where[] = "c.course = :course";
    $params[':course'] = $filterCourse;
}
if ($filterYear !== '') {
    $where[] = "c.year_graduated = :yr";
    $params[':yr'] = $filterYear;
}
if ($filterStatus !== '') {
    $where[] = "a.status = :status";
    $params[':status'] = $filterStatus;
}
if ($filterCity !== '') {
    $where[] = "a.city_municipality = :city";
    $params[':city'] = $filterCity;
}
if ($filterEmp === 'employed') {
    $where[] = "(a.company_name IS NOT NULL AND TRIM(a.company_name) <> '')";
} elseif ($filterEmp === 'unemployed') {
    $where[] = "(a.company_name IS NULL OR TRIM(a.company_name) = '')";
}
if ($dateFrom !== '' && $dateTo !== '') {
    $where[] = "DATE(a.created_at) BETWEEN :from AND :to";
    $params[':from'] = $dateFrom;
    $params[':to']   = $dateTo;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ==========================================================
   📋 Columns (default or selected)
   ========================================================== */
$selectedCols = $_POST['selected_columns'] ?? [
    'a.student_id', 'a.surname', 'a.given_name', 'a.middle_name',
    'c.course', 'c.year_graduated',
    'a.city_municipality', 'a.company_name', 'a.status'
];

// build select list
$colList = implode(', ', array_map(function($c){
    return str_replace(['alumni.', 'ccs_alumni.'], ['a.', 'c.'], $c);
}, $selectedCols));

/* ==========================================================
   📦 Query + Fetch Data
   ========================================================== */
$sql = "
    SELECT $colList
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    $whereSQL
    ORDER BY c.year_graduated DESC, a.surname ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    die("No records found for selected filters.");
}

/* ==========================================================
   💾 CSV Output
   ========================================================== */
$filename = 'WMSU_Alumni_Report_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// add UTF-8 BOM for Excel compatibility
fputs($output, "\xEF\xBB\xBF");

// write headers
fputcsv($output, array_keys($rows[0]));

// write each row
foreach ($rows as $row) {
    // Clean up nulls
    $clean = array_map(fn($v) => $v ?? '', $row);
    fputcsv($output, $clean);
}

fclose($output);
exit;
