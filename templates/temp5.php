<?php
class PDF_Temp5 extends FPDF {
    protected $portfolio;
    private $leftWidth = 63; // 30% of 210mm (A4 width)
    private $rightWidth = 147; // 70% of 210mm
    private $margin = 10; // Uniform margin of 10mm on left, right, and top

    public function Header() {
        // No header content
    }

    public function Footer() {
        // No footer content
    }

    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 102, 204); // Blue for section titles
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins($this->margin, $this->margin, $this->margin); // Set uniform margins

        // Left Column (30%) - Personal Information
        $leftX = $this->margin; // Start at left margin
        $leftY = $this->margin; // Start at top margin

        // Place image at top-left of left column
        if ($this->portfolio['photo_path'] && file_exists($this->portfolio['photo_path'])) {
            $imageWidth = 30;
            $imageHeight = 40;
            $this->Image($this->portfolio['photo_path'], $leftX, $leftY, $imageWidth, $imageHeight);
            $this->SetLineWidth(0.2);
            $this->SetDrawColor(150, 150, 150);
            $this->Rect($leftX, $leftY, $imageWidth, $imageHeight);
            $leftY += $imageHeight + 5; // Move down after image
        }

        // Full Name and Job Title
        $this->SetXY($leftX, $leftY);
        $this->SetFont('Arial', 'B', 16);
        $this->MultiCell($this->leftWidth, 8, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 'L');
        $this->SetFont('Arial', 'I', 12);
        $this->MultiCell($this->leftWidth, 6, $this->portfolio['job_title'] ?? 'JOB TITLE', 0, 'L');
        $leftY = $this->GetY() + 10; // Add 10mm space after job title

        // Contact Phone, Email, and Address
        $this->SetXY($leftX, $leftY);
        $this->SetFont('Arial', '', 10);
        $contact = "Phone: " . ($this->portfolio['contact_phone'] ?? 'N/A') . "\n" .
                   "Email: " . ($this->portfolio['contact_email'] ?? 'N/A') . "\n" .
                   "Address: " . ($this->portfolio['address'] ?? 'N/A');
        $this->MultiCell($this->leftWidth, 6, $contact, 0, 'L');

        // Right Column (70%) - All Content Sections
        $yPosRight = $this->margin; // Start at top-right with margin
        $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight); // 5mm gap between columns

        // Resume Summary/Objective
        $this->ChapterTitle('Summary');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell($this->rightWidth, 5, $this->portfolio['resume_summary'] ?? $this->portfolio['short_bio'] ?? 'No summary provided.', 0, 'J');
        $this->Ln(5);
        $yPosRight = $this->GetY();

        // Academic Background
        $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
        $this->ChapterTitle('Education');
        $this->SetFont('Arial', '', 10);
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell($this->rightWidth, 5, ($ab['degree'] ?? 'Unknown Degree') . ' | ' . ($ab['institute'] ?? 'Unknown Institute'), 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->Cell($this->rightWidth, 5, $ab['year'] ?? 'Unknown Year', 0, 1, 'L');
                $this->Ln(2);
            }
        } else {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell($this->rightWidth, 5, 'Degree | Institute', 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell($this->rightWidth, 5, 'Year', 0, 1, 'L');
        }
        $yPosRight = $this->GetY();

        // Work Experience
        if (!empty($work_experiences)) {
            $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
            $this->ChapterTitle('Experience');
            $this->SetFont('Arial', '', 10);
            foreach ($work_experiences as $exp) {
                $this->SetFont('Arial', 'B', 10);
                $companyName = $exp['company_name'] ?? 'Unknown Company';
                $this->Cell($this->rightWidth, 5, $companyName, 0, 1, 'L');
                $this->SetFont('Arial', 'I', 10);
                $this->Cell($this->rightWidth, 5, $exp['job_duration'] ?? 'Unknown Duration', 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell($this->rightWidth, 5, '- ' . ($exp['job_responsibilities'] ?? 'No responsibilities provided'), 0, 'J');
                $this->Ln(3);
            }
            $yPosRight = $this->GetY();
        }

        // Skills
        $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
        $this->ChapterTitle('Skills');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n- ", "- " . trim($this->portfolio['technical_skills'] ?? $this->portfolio['soft_skills'] ?? 'No skills provided'));
        $this->MultiCell($this->rightWidth, 5, $skills, 0, 'L');
        $yPosRight = $this->GetY();

        // Languages
        $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
        $this->ChapterTitle('Languages');
        $this->SetFont('Arial', '', 10);
        $languages = str_replace("\n", "\n- ", "- " . trim($this->portfolio['languages'] ?? 'No languages provided'));
        $this->MultiCell($this->rightWidth, 5, $languages, 0, 'L');
        $yPosRight = $this->GetY();

        // Projects (Optional)
        if (!empty($projects)) {
            $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
            $this->ChapterTitle('Projects');
            $this->SetFont('Arial', '', 10);
            foreach ($projects as $proj) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell($this->rightWidth, 5, $proj['project_title'] ?? 'Unknown Project', 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell($this->rightWidth, 5, $proj['project_description'] ?? 'No description provided', 0, 'J');
                $this->Ln(3);
            }
            $yPosRight = $this->GetY();
        }

        // Publications (Optional)
        if (!empty($publications)) {
            $this->SetXY($this->margin + $this->leftWidth + 5, $yPosRight);
            $this->ChapterTitle('Publications');
            $this->SetFont('Arial', '', 10);
            foreach ($publications as $pub) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell($this->rightWidth, 5, $pub['title'] ?? 'Unknown Publication', 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell($this->rightWidth, 5, $pub['description'] ?? 'No description provided', 0, 'J');
                $this->Ln(3);
            }
        }
    }
}