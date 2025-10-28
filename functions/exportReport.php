<?php
// functions/exportReport.php
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict(); // ensure only admin can generate reports

$pdo = Database::getPDO();

// POST validation
$selected = $_POST['selected_columns'] ?? [];
$filterCourse = trim($_POST['filter_course'] ?? '');
$filterYear = trim($_POST['filter_year'] ?? '');
$filterStatus = trim($_POST['filter_status'] ?? '');
$search = trim($_POST['query_search'] ?? '');

// ensure selected is array
if (!is_array($selected)) $selected = [];

// Default columns if none selected
if (empty($selected)) {
    $selected = ['alumni.student_id','alumni.surname','alumni.given_name','alumni.email'];
}

// Build allowed columns whitelist
$allowed = [];

// alumni columns
$stmt = $pdo->query("SHOW COLUMNS FROM alumni");
while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $allowed["alumni." . $col['Field']] = true;
}

// ccs_alumni columns
$stmt = $pdo->query("SHOW COLUMNS FROM ccs_alumni");
while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $allowed["ccs_alumni." . $col['Field']] = true;
}

// Validate and sanitize selected columns
$finalCols = [];
foreach ($selected as $col) {
    if (isset($allowed[$col])) {
        [$table, $field] = explode('.', $col, 2);
        $finalCols[] = "`$table`.`$field` AS `" . str_replace('.', '_', $col) . "`";
    }
}

if (empty($finalCols)) {
    die("No valid columns selected.");
}

// Build SQL query
$sql = "SELECT " . implode(', ', $finalCols) . " 
        FROM alumni 
        LEFT JOIN ccs_alumni ON ccs_alumni.student_id = alumni.student_id";

$where = [];
$params = [];

// Filters
if ($filterCourse !== '') {
    $where[] = "ccs_alumni.course = :course";
    $params[':course'] = $filterCourse;
}
if ($filterYear !== '') {
    $where[] = "ccs_alumni.year_graduated = :y";
    $params[':y'] = $filterYear;
}
if ($filterStatus !== '') {
    $where[] = "alumni.status = :status";
    $params[':status'] = $filterStatus;
}
if ($search !== '') {
    $where[] = "(alumni.student_id LIKE :s OR alumni.surname LIKE :s OR alumni.given_name LIKE :s OR alumni.email LIKE :s)";
    $params[':s'] = "%{$search}%";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY alumni.created_at DESC LIMIT 10000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build headers
if (!empty($rows)) {
    $headers = array_keys($rows[0]);
} else {
    // Build header from selected columns if no rows
    $headers = [];
    foreach ($finalCols as $c) {
        preg_match('/AS\s+`?([^`]+)`?/i', $c, $m);
        $headers[] = $m[1] ?? $c;
    }
}

// Prepare filename
$stamp = date('Ymd_His');
$coursePart = $filterCourse ? "_course_" . preg_replace('/\W+/', '', $filterCourse) : '';
$yearPart = $filterYear ? "_year_" . preg_replace('/\W+/', '', $filterYear) : '';
$statusPart = $filterStatus ? "_status_" . preg_replace('/\W+/', '', $filterStatus) : '';
$filename = "report{$coursePart}{$yearPart}{$statusPart}_{$stamp}.csv";

// Send download headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Output headers
fputcsv($output, $headers);

// Output rows
foreach ($rows as $r) {
    // remove newlines and extra spaces
    foreach ($r as &$val) {
        $val = str_replace(["\r", "\n"], ' ', trim($val));
    }
    fputcsv($output, $r);
}

fclose($output);
exit;
