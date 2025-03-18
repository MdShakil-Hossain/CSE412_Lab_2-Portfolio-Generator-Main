<?php
class PDF_Temp2 extends FPDF {
    protected $portfolio;

    public function Header() {
        // No header content as we'll handle it in generate()
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(130, 8, strtoupper($title), 0, 1, 'L');
        // Add navy blue line
        $this->SetLineWidth(0.5); // Bold line
        $this->SetDrawColor(0, 51, 102); // Navy blue
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 130, $this->GetY());
        $this->SetTextColor(0, 0, 0);
        $this->Ln(4); // Increased spacing after line
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(15, 15, 15);

        // Right side - Navy Blue background (30% = 63mm of 210mm)
        $this->SetFillColor(0, 51, 102);
        $this->Rect(147, 0, 63, 297, 'F'); // Right 30% of A4

        // Left side - White background (70% = 147mm of 210mm)
        $this->SetFillColor(255, 255, 255);
        $this->Rect(0, 0, 147, 297, 'F');

        // Right side content (30% width)
        $rightX = 147; // Starting X for right column
        $rightWidth = 63; // Width of right column
        $yPos = 15;

        // Profile image - Centered in right column
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $imageWidth = 35;
            $imageX = $rightX + ($rightWidth - $imageWidth) / 2;
            $this->Image($portfolio['photo_path'], $imageX, $yPos, $imageWidth, 35);
            $this->SetLineWidth(0.2);
            $this->SetDrawColor(255, 255, 255);
            $this->Rect($imageX, $yPos, $imageWidth, 35);
            $yPos += 40;
        }

        // Full Name
        $this->SetXY($rightX, $yPos);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(255, 255, 255);
        $this->MultiCell($rightWidth, 8, strtoupper($portfolio['full_name'] ?? 'FULL NAME'), 0, 'C');
        $yPos = $this->GetY() + 5;

        // Job Title
        $this->SetXY($rightX, $yPos);
        $this->SetFont('Arial', 'I', 11);
        $this->SetTextColor(200, 200, 200);
        $this->MultiCell($rightWidth, 6, $portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 'C');
        $yPos = $this->GetY() + 10;

        // Contact Info
        $this->SetXY($rightX, $yPos);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(255, 255, 255);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
                  "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
                  "Address: " . ($portfolio['address'] ?? 'Your Street Address');
        $this->MultiCell($rightWidth, 5, $contact, 0, 'L');

        // Left side content (70% width)
        $leftX = 15; // Starting X for left column
        $yPos = 15;

        // Short Bio/Profile
        $this->SetXY($leftX, $yPos);
        $this->ChapterTitle('Profile');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(130, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 'J');
        $yPos = $this->GetY() + 5;

        // Work Experience
        if (!empty($work_experiences)) {
            $this->SetXY($leftX, $yPos);
            $this->ChapterTitle('Experience');
            $this->SetFont('Arial', '', 10);
            foreach ($work_experiences as $exp) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(130, 5, $exp['job_duration'], 0, 1, 'L');
                $this->SetFont('Arial', 'I', 10);
                $this->Cell(130, 5, $exp['company_name'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(130, 5, $exp['job_responsibilities'], 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY() + 5;
        }

        // Education
        $this->SetXY($leftX, $yPos);
        $this->ChapterTitle('Education');
        $this->SetFont('Arial', '', 10);
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(130, 5, $ab['year'] . ' | ' . $ab['degree'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->Cell(130, 5, $ab['institute'], 0, 1, 'L');
                $this->Ln(2);
            }
        } else {
            $this->Cell(130, 5, '2014 - 2016 | Degree / Major Name', 0, 1, 'L');
            $this->Cell(130, 5, 'University name here', 0, 1, 'L');
        }
        $yPos = $this->GetY() + 5;

        // Skills
        $this->SetXY($leftX, $yPos);
        $this->ChapterTitle('Skills');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n- ", "- " . trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign\nAfter Effects\nSketch'));
        $skills = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $skills);
        $this->MultiCell(130, 5, $skills, 0, 'L');
        $yPos = $this->GetY() + 5;

        // Languages
        $this->SetXY($leftX, $yPos);
        $this->ChapterTitle('Languages');
        $this->SetFont('Arial', '', 10);
        $languages = str_replace("\n", "\n- ", "- " . trim($portfolio['languages'] ?? 'English\nFrench\nSpanish'));
        $languages = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $languages);
        $this->MultiCell(130, 5, $languages, 0, 'L');
        $yPos = $this->GetY() + 5;

        // Projects
        if (!empty($projects)) {
            $this->SetXY($leftX, $yPos);
            $this->ChapterTitle('Projects');
            $this->SetFont('Arial', '', 10);
            foreach ($projects as $proj) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(130, 5, $proj['project_title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(130, 5, $proj['project_description'], 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY() + 5;
        }

        // Publications
        if (!empty($publications)) {
            $this->SetXY($leftX, $yPos);
            $this->ChapterTitle('Publications');
            $this->SetFont('Arial', '', 10);
            foreach ($publications as $pub) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(130, 5, $pub['title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(130, 5, $pub['description'], 0, 'J');
                $this->Ln(3);
            }
        }
    }
}