<?php
class PDF_Temp4 extends FPDF {
    protected $portfolio;

    public function Header() {
        $this->SetFillColor(0, 51, 102);
        $this->Rect(0, 0, 210, 30, 'F');
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(15, 8);
        $this->Cell(0, 8, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetXY(15, 18);
        $this->Cell(0, 6, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'L');
    }

    public function Footer() {
        // Removed footer to save space
    }

    public function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 7, strtoupper($title), 'B', 1, 'L');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(3); // Reduced from 4 to save space
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(15, 30, 15);

        // Contact section
        $this->SetFont('Arial', '', 10);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
                   "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
                   "Address: " . ($portfolio['address'] ?? 'Your Street Address');
        $this->SetXY(15, 35);
        $this->SetFillColor(245, 245, 245);
        $this->Rect(15, 35, 115, 50, 'F'); // Adjusted width to 115 to accommodate image at 150
        $this->SetXY(17, 37);
        $this->MultiCell(111, 5, $contact, 0, 'L'); // Adjusted width accordingly

        // Image on right side with left margin at 150mm
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], 150, 35, 40, 40); // Changed from 180 to 150
            $this->SetDrawColor(0, 51, 102);
            $this->SetLineWidth(0.3);
            $this->Rect(150, 35, 40, 40); // Changed from 180 to 150
        }

        // Content starts below contact/photo
        $this->SetXY(15, 90);
        $contentWidth = 180; // Adjusted for right margin

        // Profile
        $this->SectionTitle('Profile');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell($contentWidth, 4, substr($portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 200) . '...', 0, 'J');
        $this->Ln(6); // Added space between sections

        // Education
        $this->SectionTitle('Education');
        $this->SetFont('Arial', '', 9);
        if (!empty($academic_backgrounds)) {
            $count = 0;
            foreach ($academic_backgrounds as $ab) {
                if ($count++ >= 2) break;
                $this->Cell(30, 5, $ab['year'], 0, 0);
                $this->SetFont('Arial', 'B', 9);
                $this->Cell(0, 5, substr($ab['degree'], 0, 50), 0, 1);
                $this->SetFont('Arial', '', 9);
                $this->Cell(0, 5, substr($ab['institute'], 0, 60), 0, 1);
            }
        }
        $this->Ln(6); // Added space between sections

        // Skills
        $this->SectionTitle('Skills');
        $this->SetFont('Arial', '', 9);
        $skills = explode("\n", trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign'));
        $count = 0;
        foreach ($skills as $skill) {
            if ($count++ >= 5) break;
            $this->Cell(0, 4, '• ' . substr($skill, 0, 50), 0, 1);
        }
        $this->Ln(6); // Added space between sections

        // Experience
        if (!empty($work_experiences)) {
            $this->SectionTitle('Experience');
            $this->SetFont('Arial', '', 9);
            $count = 0;
            foreach ($work_experiences as $exp) {
                if ($count++ >= 2) break;
                $this->SetFont('Arial', 'B', 9);
                $this->Cell(0, 5, $exp['job_duration'], 0, 1);
                $this->SetFont('Arial', 'I', 9);
                $this->Cell(0, 5, substr($exp['company_name'], 0, 60), 0, 1);
                $this->SetFont('Arial', '', 9);
                $this->MultiCell($contentWidth, 4, substr($exp['job_responsibilities'], 0, 150) . '...', 0, 'J');
            }
            $this->Ln(6); // Added space between sections
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
                $this->MultiCell($contentWidth, 4, substr($proj['project_description'], 0, 150) . '...', 0, 'J');
            }
            $this->Ln(6); // Added space between sections
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
                $this->MultiCell($contentWidth, 4, substr($pub['description'], 0, 150) . '...', 0, 'J');
            }
            $this->Ln(6); // Added space between sections
        }
    }
}