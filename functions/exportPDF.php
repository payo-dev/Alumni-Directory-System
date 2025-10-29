<?php
// ==========================================================
// functions/exportPDF.php — Generate PDF Report for Alumni
// ==========================================================
require_once __DIR__ . '/../classes/auth.php';
require_once __DIR__ . '/../classes/database.php';
Auth::restrict();

require_once __DIR__ . '/../lib/tcpdf/tcpdf.php'; // ✅ Make sure TCPDF is placed here

$pdo = Database::getPDO();

// Collect filters (from POST or GET)
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

$stmt = $pdo->prepare("
    SELECT a.student_id, a.surname, a.given_name, a.middle_name,
           a.city_municipality, a.company_name, a.status,
           c.course, c.year_graduated
    FROM alumni a
    LEFT JOIN ccs_alumni c ON c.student_id = a.student_id
    $whereSQL
    ORDER BY c.year_graduated DESC, a.surname ASC
");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================================
// PDF SETTINGS
// ==========================================================
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('WMSU Alumni Directory');
$pdf->SetAuthor('WMSU Alumni System');
$pdf->SetTitle('WMSU Alumni Report');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// ==========================================================
// HEADER SECTION
// ==========================================================
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Western Mindanao State University', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(0, 7, 'Alumni Directory Report', 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Generated on: ' . date('F j, Y, g:i A'), 0, 1, 'R');
$pdf->Cell(0, 6, 'Prepared by: ' . ($_SESSION['admin_fullname'] ?? 'Administrator'), 0, 1, 'R');
$pdf->Ln(4);

// ==========================================================
// FILTER SUMMARY
// ==========================================================
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 8, 'Report Filters:', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$filters = [
    "Course" => $filterCourse ?: 'Any',
    "Year Graduated" => $filterYear ?: 'Any',
    "Status" => $filterStatus ?: 'Any',
    "Employment" => $filterEmp ?: 'Any',
    "City" => $filterCity ?: 'Any',
    "Date Range" => ($dateFrom && $dateTo) ? "$dateFrom – $dateTo" : 'All Time',
];
foreach ($filters as $k => $v) {
    $pdf->Cell(60, 6, "$k:", 0, 0);
    $pdf->Cell(0, 6, $v, 0, 1);
}
$pdf->Ln(6);

// ==========================================================
// TABLE HEADER
// ==========================================================
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(220, 53, 69); // WMSU Red
$pdf->SetTextColor(255);
$pdf->Cell(25, 8, 'Student ID', 1, 0, 'C', 1);
$pdf->Cell(50, 8, 'Full Name', 1, 0, 'C', 1);
$pdf->Cell(35, 8, 'Course', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Year Graduated', 1, 0, 'C', 1);
$pdf->Cell(45, 8, 'City / Municipality', 1, 0, 'C', 1);
$pdf->Cell(55, 8, 'Company / Employer', 1, 0, 'C', 1);
$pdf->Cell(25, 8, 'Status', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0);
$fill = false;

foreach ($rows as $r) {
    $pdf->SetFillColor(255, 240, 240);
    $pdf->Cell(25, 7, $r['student_id'], 1, 0, 'C', $fill);
    $pdf->Cell(50, 7, $r['surname'] . ', ' . $r['given_name'] . ' ' . substr($r['middle_name'], 0, 1) . '.', 1, 0, 'L', $fill);
    $pdf->Cell(35, 7, $r['course'], 1, 0, 'C', $fill);
    $pdf->Cell(30, 7, $r['year_graduated'], 1, 0, 'C', $fill);
    $pdf->Cell(45, 7, $r['city_municipality'], 1, 0, 'L', $fill);
    $pdf->Cell(55, 7, $r['company_name'], 1, 0, 'L', $fill);
    $pdf->Cell(25, 7, ucfirst($r['status']), 1, 1, 'C', $fill);
    $fill = !$fill;
}

$pdf->Ln(6);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->Cell(0, 6, 'Total Records: ' . count($rows), 0, 1, 'R');

// ==========================================================
// OUTPUT PDF (Preview or Download)
// ==========================================================
// If preview=true, show inline; otherwise, force download
$preview = isset($_GET['preview']) && $_GET['preview'] == '1';
$pdf->Output('WMSU_Alumni_Report.pdf', $preview ? 'I' : 'D');
exit;
