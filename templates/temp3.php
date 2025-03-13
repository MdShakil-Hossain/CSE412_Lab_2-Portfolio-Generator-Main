<?php
require_once 'libs/fpdf.php';

class PDF_Temp3 extends FPDF {
    protected $portfolio;

    public function Header() {
        // Full page black background
        $this->SetFillColor(0, 0, 0);
        $this->Rect(0, 0, 210, 297, 'F'); // A4: 210mm x 297mm

        // Header (orange background)
        $this->SetFillColor(244, 162, 97); // #f4a261
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 20);
        $this->SetXY(20, 10);
        $this->Cell(130, 20, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'C', true);
        $this->SetFont('Arial', '', 14);
        $this->SetXY(20, 30);
        $this->Cell(130, 10, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'C', true);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function ChapterTitle($title, $x, $y) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY($x, $y);
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L');
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(20, 20, 20);

        // Left Section (130mm wide, black background)
        $leftWidth = 130;
        $leftX = 20;
        $yPos = 45; // Below header

        // Right Section (gray background, 60mm wide)
        $rightWidth = 60;
        $rightX = 150; // 20mm left margin + 130mm left section
        $this->SetFillColor(51, 51, 51); // #333
        $this->Rect($rightX, 0, $rightWidth, 297, 'F');

        // Right: Photo
        $imageWidth = 40;
        $imageX = $rightX + ($rightWidth - $imageWidth) / 2; // Center in right section
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], $imageX, 20, $imageWidth, $imageWidth);
            $this->SetLineWidth(0.5);
            $this->SetDrawColor(0, 0, 0);
            $this->Rect($imageX, 20, $imageWidth, $imageWidth); // Black border
        }
        $rightY = 65; // Below photo

        // Right: Contact Info
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY($rightX, $rightY);
        $this->Cell($rightWidth, 8, 'CONTACT ME', 0, 1, 'C');
        $rightY += 10;
        $this->SetXY($rightX, $rightY);
        $this->SetTextColor(244, 162, 97); // Orange
        $this->Cell(5, 5, utf8_decode('📍'), 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($rightWidth - 5, 5, 'ADDRESS: ' . ($portfolio['address'] ?? 'Your Street Address'), 0, 1);
        $rightY += 5;
        $this->SetXY($rightX, $rightY);
        $this->SetTextColor(244, 162, 97);
        $this->Cell(5, 5, utf8_decode('🌐'), 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($rightWidth - 5, 5, 'EMAIL: ' . ($portfolio['contact_email'] ?? 'N/A'), 0, 1);
        $rightY += 5;
        $this->SetXY($rightX, $rightY);
        $this->SetTextColor(244, 162, 97);
        $this->Cell(5, 5, utf8_decode('📞'), 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($rightWidth - 5, 5, 'PHONE: ' . ($portfolio['contact_phone'] ?? 'N/A'), 0, 1);

        // Left: Education
        $this->ChapterTitle('EDUCATION', $leftX, $yPos);
        $yPos += 10;
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(255, 255, 255);
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $this->SetXY($leftX, $yPos);
                $this->SetFont('Arial', 'B', 10);
                $this->SetFillColor(244, 162, 97);
                $this->Cell(30, 5, $ab['year'], 0, 0, 'L', true);
                $this->SetFont('Arial', '', 10);
                $this->SetFillColor(0, 0, 0);
                $this->Cell(100, 5, ' ' . $ab['degree'] . ' - ' . $ab['institute'], 0, 1, 'L');
                $yPos += 6;
            }
        } else {
            $this->SetXY($leftX, $yPos);
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(244, 162, 97);
            $this->Cell(30, 5, '2014 - 2016', 0, 0, 'L', true);
            $this->SetFont('Arial', '', 10);
            $this->SetFillColor(0, 0, 0);
            $this->Cell(100, 5, ' Degree / Major Name - University name here', 0, 1, 'L');
            $yPos += 6;
        }
        $yPos += 5;

        // Left: Experience
        if (!empty($work_experiences)) {
            $this->ChapterTitle('EXPERIENCE', $leftX, $yPos);
            $yPos += 10;
            foreach ($work_experiences as $exp) {
                $this->SetXY($leftX, $yPos);
                $this->SetFont('Arial', 'B', 10);
                $this->SetFillColor(244, 162, 97);
                $this->Cell(30, 5, $exp['job_duration'], 0, 0, 'L', true);
                $this->SetFont('Arial', '', 10);
                $this->SetFillColor(0, 0, 0);
                $this->Cell(100, 5, ' ' . $exp['company_name'], 0, 1, 'L');
                $this->SetXY($leftX + 5, $yPos + 5);
                $this->MultiCell(125, 5, $exp['job_responsibilities'], 0, 'L');
                $yPos = $this->GetY() + 2;
            }
            $yPos += 5;
        }

        // Left: Projects
        if (!empty($projects)) {
            $this->ChapterTitle('PROJECTS', $leftX, $yPos);
            $yPos += 10;
            foreach ($projects as $proj) {
                $this->SetXY($leftX, $yPos);
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $proj['project_title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->SetXY($leftX + 5, $yPos + 5);
                $this->MultiCell(125, 5, $proj['project_description'], 0, 'L');
                $yPos = $this->GetY() + 2;
            }
            $yPos += 5;
        }

        // Left: Publications
        if (!empty($publications)) {
            $this->ChapterTitle('PUBLICATIONS', $leftX, $yPos);
            $yPos += 10;
            foreach ($publications as $pub) {
                $this->SetXY($leftX, $yPos);
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $pub['title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->SetXY($leftX + 5, $yPos + 5);
                $this->MultiCell(125, 5, $pub['description'], 0, 'L');
                $yPos = $this->GetY() + 2;
            }
        }
    }
}