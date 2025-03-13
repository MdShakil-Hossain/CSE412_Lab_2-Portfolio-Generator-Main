<?php
class PDF_Temp1 extends FPDF {
    protected $portfolio;

    public function Header() {
        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 20, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'L');
        $this->SetFont('Arial', '', 14);
        $this->Cell(0, 10, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'L');
        $this->Ln(10);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(0, 0, 0);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
    }

    public function getNumLines($text) {
        $lines = explode("\n", $text);
        return count($lines);
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects) {
        $this->portfolio = $portfolio; // Store portfolio data for use in Header
        $this->AddPage('P', 'A4');
        $this->SetMargins(20, 20, 20);

        // Contact Information
        $this->SetFont('Arial', '', 10);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
                   "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
                   "Address: " . ($portfolio['address'] ?? 'Your Street Address');
        $this->MultiCell(0, 5, $contact, 0, 'L');
        $this->Ln(5);

        // Photo (aligned to the right within the single column)
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], 120, $this->GetY(), 50); // Adjusted x position to align right
            $this->Ln(55); // Add space below the photo (50px height + 5px padding)
        }

        // Single-column layout
        $yPos = $this->GetY();

        // Profile
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('PROFILE');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 'J');
        $yPos = $this->GetY();

        // Experience (only render if there is data)
        if (!empty($work_experiences)) {
            $experience = '';
            foreach ($work_experiences as $exp) {
                $experience .= "{$exp['job_duration']}\n{$exp['company_name']}\n{$exp['job_responsibilities']}\n\n";
            }
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('EXPERIENCE');
            $this->MultiCell(0, 5, $experience, 0, 'L');
            $yPos = $this->GetY();
        }

        // Education
        $education = '';
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $education .= "{$ab['year']} | {$ab['degree']}\n{$ab['institute']}\n";
            }
        }
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('EDUCATION');
        $this->MultiCell(0, 5, $education ?: '2014 - 2016 | Degree / Major Name\nUniversity name here', 0, 'L');
        $yPos = $this->GetY();

        // Skills
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('SKILLS');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n* ", "* " . trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign\nAfter Effects\nSketch'));
        // Remove any remaining unwanted characters (if any)
        $skills = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $skills);
        $this->MultiCell(0, 5, $skills, 0, 'L');
        $yPos = $this->GetY();

        // Language
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('LANGUAGE');
        $this->SetFont('Arial', '', 10);
        $languages = str_replace("\n", "\n* ", "* " . trim($portfolio['languages'] ?? 'English\nFrench\nSpanish'));
        // Remove any remaining unwanted characters (if any)
        $languages = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $languages);
        $this->MultiCell(0, 5, $languages, 0, 'L');
        $yPos = $this->GetY();

        // Projects
        $project_content = '';
        if (!empty($projects)) {
            foreach ($projects as $proj) {
                $project_content .= "{$proj['project_title']}\n{$proj['project_description']}\n\n";
            }
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('PROJECTS');
            $this->MultiCell(0, 5, $project_content, 0, 'L');
        }
    }
}