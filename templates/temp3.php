<?php
class PDF_Temp3 extends FPDF {
    protected $portfolio;

    public function Header() {
        $this->SetFillColor(50, 50, 50);
        $this->Rect(0, 0, 210, 25, 'F');
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(20, 5);
        $this->Cell(0, 8, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'L');
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(200, 200, 200);
        $this->SetXY(20, 15);
        $this->Cell(0, 6, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'L');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(50, 50, 50);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 6, '  ' . strtoupper($title), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(4);
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(20, 25, 20);

        // Contact section with vertical line separator
        $this->SetFont('Arial', '', 9);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
                   "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
                   "Address: " . ($portfolio['address'] ?? 'Your Street Address');
        $this->SetXY(20, 35);
        $this->MultiCell(100, 5, $contact, 0, 'L');
        $this->SetDrawColor(150, 150, 150);
        $this->SetLineWidth(0.2);
        $this->Line(130, 35, 130, 75); // Vertical line

        // Image with circular border approximation
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], 140, 35, 40, 40);
            $this->SetDrawColor(50, 50, 50);
            $this->SetLineWidth(0.5);
            // Simple circle approximation using multiple lines
            $centerX = 160;
            $centerY = 55;
            $radius = 20;
            $steps = 16; // Number of segments
            for ($i = 0; $i < $steps; $i++) {
                $angle1 = 2 * M_PI * $i / $steps;
                $angle2 = 2 * M_PI * ($i + 1) / $steps;
                $x1 = $centerX + $radius * cos($angle1);
                $y1 = $centerY + $radius * sin($angle1);
                $x2 = $centerX + $radius * cos($angle2);
                $y2 = $centerY + $radius * sin($angle2);
                $this->Line($x1, $y1, $x2, $y2);
            }
        }

        // Content starts below
        $this->SetXY(20, 85);
        $contentWidth = 170;

        // Profile
        $this->SectionTitle('Profile');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell($contentWidth, 4, substr($portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 250), 0, 'J');
        $this->Ln(6);

        // Education
        $this->SectionTitle('Education');
        $this->SetFont('Arial', '', 9);
        if (!empty($academic_backgrounds)) {
            $count = 0;
            foreach ($academic_backgrounds as $ab) {
                if ($count++ >= 2) break;
                $this->SetFont('Arial', 'B', 9);
                $this->Cell(0, 5, $ab['year'] . ' - ' . substr($ab['degree'], 0, 50), 0, 1, 'L');
                $this->SetFont('Arial', '', 9);
                $this->Cell(0, 5, substr($ab['institute'], 0, 60), 0, 1, 'L');
                $this->Ln(2);
            }
        }
        $this->Ln(6);

        // Skills
        $this->SectionTitle('Skills');
        $this->SetFont('Arial', '', 9);
        $skills = explode("\n", trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign'));
        $count = 0;
        foreach ($skills as $skill) {
            if ($count++ >= 5) break;
            $this->Cell(50, 4, '• ' . substr($skill, 0, 45), 0, 0);
            if ($count % 3 == 0) $this->Ln(4); // 3 skills per line
        }
        $this->Ln(6);

        // Experience
        if (!empty($work_experiences)) {
            $this->SectionTitle('Experience');
            $this->SetFont('Arial', '', 9);
            $count = 0;
            foreach ($work_experiences as $exp) {
                if ($count++ >= 2) break;
                $this->SetFillColor(245, 245, 245);
                $this->Cell(0, 4, '', 0, 1, 'L', true);
                $this->SetFont('Arial', 'B', 9);
                $this->Cell(60, 5, $exp['job_duration'], 0, 0);
                $this->SetFont('Arial', 'I', 9);
                $this->Cell(0, 5, substr($exp['company_name'], 0, 50), 0, 1);
                $this->SetFont('Arial', '', 9);
                $this->MultiCell($contentWidth, 4, substr($exp['job_responsibilities'], 0, 200), 0, 'J');
                $this->Ln(2);
            }
            $this->Ln(6);
        }

        // Projects
        if (!empty($projects)) {
            $this->SectionTitle('Projects');
            $this->SetFont('Arial', '', 9);
            $count = 0;
            foreach ($projects as $proj) {
                if ($count++ >= 2) break;
                $this->SetFont('Arial', 'B', 9);
                $this->Cell(0, 5, substr($proj['project_title'], 0, 60), 0, 1);
                $this->SetFont('Arial', '', 9);
                $this->MultiCell($contentWidth, 4, substr($proj['project_description'], 0, 200), 0, 'J');
                $this->Ln(2);
            }
            $this->Ln(6);
        }

        // Publications
        if (!empty($publications)) {
            $this->SectionTitle('Publications');
            $this->SetFont('Arial', '', 9);
            $count = 0;
            foreach ($publications as $pub) {
                if ($count++ >= 2) break;
                $this->SetFont('Arial', 'I', 9);
                $this->Cell(0, 5, substr($pub['title'], 0, 60), 0, 1);
                $this->SetFont('Arial', '', 9);
                $this->MultiCell($contentWidth, 4, substr($pub['description'], 0, 200), 0, 'J');
                $this->Ln(2);
            }
            $this->Ln(6);
        }
    }
}