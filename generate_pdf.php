<?php
session_start();
require 'config.php';
require 'libs/fpdf.php'; // Download FPDF from fpdf.org and place in /libs

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();
$portfolio_id = $portfolio['id'];

$academic_backgrounds = $conn->query("SELECT * FROM academic_backgrounds WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
// Add similar queries for work_experiences and projects

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $portfolio['full_name'], 0, 1, 'C');
if ($portfolio['photo_path']) {
    $pdf->Image($portfolio['photo_path'], 150, 10, 30);
}
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Phone: " . $portfolio['contact_phone'], 0, 1);
$pdf->Cell(0, 10, "Email: " . $portfolio['contact_email'], 0, 1);
$pdf->MultiCell(0, 10, "Bio: " . $portfolio['short_bio']);
$pdf->Ln();
$pdf->Cell(0, 10, "Soft Skills: " . $portfolio['soft_skills'], 0, 1);
$pdf->Cell(0, 10, "Technical Skills: " . $portfolio['technical_skills'], 0, 1);
$pdf->Ln();
$pdf->Cell(0, 10, "Academic Background:", 0, 1);
foreach ($academic_backgrounds as $ab) {
    $pdf->Cell(0, 10, "$ab[institute], $ab[degree], $ab[year], $ab[grade]", 0, 1);
}
// Add similar loops for work_experiences and projects
$pdf->Output('D', 'portfolio.pdf');
?>