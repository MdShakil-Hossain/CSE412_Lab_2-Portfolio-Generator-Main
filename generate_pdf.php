<?php
session_start();
require 'config.php';
require 'libs/fpdf.php';

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
$work_experiences = $conn->query("SELECT * FROM work_experiences WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$projects = $conn->query("SELECT * FROM projects WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);

class PDF extends FPDF {
    function Header() {
        // Header with name and title
        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 20, strtoupper($portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'L');
        $this->SetFont('Arial', '', 14);
        $this->Cell(0, 10, $portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'L');
        $this->Ln(10);
    }

    function Footer() {
        // Footer with page number
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(0, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
    }

    function TwoColumnLayout($leftContent, $rightContent) {
        $this->SetFont('Arial', '', 10);
        $columnWidth = 90;
        $this->MultiCell($columnWidth, 5, $leftContent, 0, 'L');
        $this->SetXY($columnWidth + 20, $this->GetY() - $this->getNumLines($leftContent) * 5);
        $this->MultiCell($columnWidth, 5, $rightContent, 0, 'L');
        $this->Ln(5);
    }

    function getNumLines($text) {
        $lines = explode("\n", $text);
        return count($lines);
    }
}

$pdf = new PDF();
$pdf->AddPage('P', 'A4');
$pdf->SetMargins(20, 20, 20);

// Contact Information
$pdf->SetFont('Arial', '', 10);
$contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
           "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
           "Address: " . ($portfolio['address'] ?? 'Your Street Address');
$pdf->MultiCell(0, 5, $contact, 0, 'L');
$pdf->Ln(5);

// Photo
if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
    $pdf->Image($portfolio['photo_path'], 140, 30, 50);
}

// Two-column layout sections
$yStart = $pdf->GetY();

// Profile
$pdf->SetXY(20, $yStart);
$pdf->ChapterTitle('PROFILE');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(120, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? '', 0, 'J');

// Education
$yEdu = $pdf->GetY();
$pdf->SetXY(20, $yEdu);
$education = '';
if (!empty($academic_backgrounds)) {
    foreach ($academic_backgrounds as $ab) {
        $education .= "{$ab['year']} | {$ab['degree']}\n{$ab['institute']}\n";
    }
}
$pdf->ChapterTitle('EDUCATION');
$pdf->MultiCell(120, 5, $education ?: '', 0, 'L');

// Experience
$experience = '';
if (!empty($work_experiences)) {
    foreach ($work_experiences as $exp) {
        $experience .= "{$exp['job_duration']}\n{$exp['company_name']}\n{$exp['job_responsibilities']}\n\n";
    }
}
$pdf->SetXY(140, $yEdu);
$pdf->ChapterTitle('EXPERIENCE');
$pdf->MultiCell(60, 5, $experience ?: "\n", 0, 'L');

// Language
$languages = str_replace("\n", "\n• ", "• " . trim($portfolio['languages'] ?? ''));
$pdf->SetXY(140, $pdf->GetY());
$pdf->ChapterTitle('LANGUAGE');
$pdf->MultiCell(60, 5, $languages, 0, 'L');

// Skills
$skills = str_replace("\n", "\n• ", "• " . trim($portfolio['technical_skills'] ?? ''));
$pdf->SetXY(140, $pdf->GetY());
$pdf->ChapterTitle('SKILLS');
$pdf->MultiCell(60, 5, $skills, 0, 'L');

// Projects (below Education)
$project_content = '';
if (!empty($projects)) {
    foreach ($projects as $proj) {
        $project_content .= "{$proj['project_title']}\n{$proj['project_description']}\n\n";
    }
}
if ($project_content) {
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->ChapterTitle('PROJECTS');
    $pdf->MultiCell(120, 5, $project_content, 0, 'L');
}

// References (placeholder)
$references = "Jhon Anderson\nCompany name here | Senior Designer\n\nJhon Smith\nCompany name here | Senior Designer";
$pdf->SetXY(20, $pdf->GetY());
$pdf->ChapterTitle('REFERENCE');
$pdf->MultiCell(180, 5, $references, 0, 'L');

$pdf->Output('D', 'portfolio.pdf');
?>