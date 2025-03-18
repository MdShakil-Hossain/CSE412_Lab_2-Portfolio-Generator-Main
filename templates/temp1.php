<?php
class PDF_Temp1 extends FPDF {
    protected $portfolio;

    public function Header() {
        // No header content as we'll handle it in generate()
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Times', 'I', 8);
        $this->SetTextColor(150, 150, 150); // Gray color for footer
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function ChapterTitle($title) {
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetTextColor(0, 0, 128); // Navy blue for titles
        $this->Cell(80, 8, strtoupper($title), 0, 1, 'L'); // Left-aligned
        $this->SetLineWidth(0.5); // Bold line
        $this->SetDrawColor(0, 0, 128); // Navy blue line
        $this->Line(68, $this->GetY(), 210 - 15, $this->GetY()); // Line spans full 70% width (68mm to 195mm)
        $this->Ln(2); // Spacing after line
    }

    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(15, 15, 15);

        // Left side - Lilac background (approximated RGB 200, 162, 200)
        $this->SetFillColor(200, 162, 200);
        $this->Rect(0, 0, 63, 297, 'F');

        // Right side - Cream white background (approximated RGB 245, 245, 220)
        $this->SetFillColor(245, 245, 220);
        $this->Rect(63, 0, 147, 297, 'F');

        // Left side content (30% width) - Personal Info
        $leftX = 5;
        $leftWidth = 53;
        $yPos = 15;

        // Profile image - Centered in left column
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $imageWidth = 35;
            $imageX = $leftX + ($leftWidth - $imageWidth) / 2;
            $this->Image($portfolio['photo_path'], $imageX, $yPos, $imageWidth, 35);
            $this->SetLineWidth(0.2);
            $this->SetDrawColor(255, 255, 255);
            $this->Rect($imageX, $yPos, $imageWidth, 35);
            $yPos += 40;
        }

        // Full Name
        $this->SetXY($leftX, $yPos);
        $this->SetFont('Times', 'B', 20);
        $this->SetTextColor(128, 128, 128); // Gray color for left side text
        $this->MultiCell($leftWidth, 8, strtoupper($portfolio['full_name'] ?? 'HAMZA'), 0, 'C');
        $yPos = $this->GetY() + 5;

        // Job Title
        $this->SetXY($leftX, $yPos);
        $this->SetFont('Times', 'I', 12);
        $this->SetTextColor(128, 128, 128); // Gray color for left side text
        $this->MultiCell($leftWidth, 6, $portfolio['job_title'] ?? 'PLAYER', 0, 'C');
        $yPos = $this->GetY() + 10;

        // Contact Info
        $this->SetXY($leftX, $yPos);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(128, 128, 128); // Gray color for left side text
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? '1234567809') . "\n" .
                  "Email: " . ($portfolio['contact_email'] ?? 'hamza@gmail.com') . "\n" .
                  "Address: " . ($portfolio['address'] ?? 'feni, bangladesh');
        $this->MultiCell($leftWidth, 5, $contact, 0, 'L');
        $yPos = $this->GetY() + 10; // Add some space after the address

        // Short Bio
        $this->SetXY($leftX, $yPos);
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(128, 128, 128); // Gray color for left side text
        $yPos = $this->GetY() + 2;

        $this->SetXY($leftX, $yPos);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(128, 128, 128); // Gray color for left side text
        $this->MultiCell($leftWidth, 5, $portfolio['short_bio'] ?? 'No short bio provided.', 0, 'L');
        $yPos = $this->GetY() + 10; // Add some space after the Short Bio

        // Right side content (70% width) - Details
        $rightX = 68; // Starting X for left side of right section
        $rightWidth = 137; // Usable width
        $sectionWidth = 80; // Width for each section
        $yPos = 15;

        // Short Bio/Profile (Right Side)
        $this->SetXY($rightX, $yPos); // Start at left of right section
        $this->ChapterTitle('Profile');
        $this->SetFont('Times', '', 11);
        $this->SetTextColor(0, 0, 128); // Navy blue for right side text
        $this->SetXY($rightX, $this->GetY()); // Reset X for subtext
        $this->MultiCell($sectionWidth, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'will beat india', 0, 'L');
        $yPos = $this->GetY() + 5;

        // Work Experience
        if (!empty($work_experiences)) {
            $this->SetXY($rightX, $yPos);
            $this->ChapterTitle('Experience');
            $this->SetFont('Times', '', 11);
            $this->SetTextColor(0, 0, 128); // Navy blue for right side text
            $this->SetXY($rightX, $this->GetY());
            $this->MultiCell($sectionWidth, 5, 'sa nono assa', 0, 'L');
            $yPos = $this->GetY() + 5;
        }

        // Education
        $this->SetXY($rightX, $yPos);
        $this->ChapterTitle('Education');
        $this->SetFont('Times', '', 11);
        $this->SetTextColor(0, 0, 128); // Navy blue for right side text
        $this->SetXY($rightX, $this->GetY());
        $this->MultiCell($sectionWidth, 5, '2025 | bsc ewu', 0, 'L');
        $yPos = $this->GetY() + 5;

        // Skills
        $this->SetXY($rightX, $yPos);
        $this->ChapterTitle('Skills');
        $this->SetFont('Times', '', 11);
        $this->SetTextColor(0, 0, 128); // Navy blue for right side text
        $skills = str_replace("\n", "\n- ", "- " . trim($portfolio['technical_skills'] ?? '- defense'));
        $skills = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $skills);
        $this->SetXY($rightX, $this->GetY());
        $this->MultiCell($sectionWidth, 5, $skills, 0, 'L');
        $yPos = $this->GetY() + 5;

        // Languages
        $this->SetXY($rightX, $yPos);
        $this->ChapterTitle('Languages');
        $this->SetFont('Times', '', 11);
        $this->SetTextColor(0, 0, 128); // Navy blue for right side text
        $languages = str_replace("\n", "\n- ", "- " . trim($portfolio['languages'] ?? '- bangla, english'));
        $languages = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $languages);
        $this->SetXY($rightX, $this->GetY());
        $this->MultiCell($sectionWidth, 5, $languages, 0, 'L');
        $yPos = $this->GetY() + 5;

        // Projects
        if (!empty($projects)) {
            $this->SetXY($rightX, $yPos);
            $this->ChapterTitle('Projects');
            $this->SetFont('Times', '', 11);
            $this->SetTextColor(0, 0, 128); // Navy blue for right side text
            $this->SetXY($rightX, $this->GetY());
            $this->MultiCell($sectionWidth, 5, 'assss dasa', 0, 'L');
            $yPos = $this->GetY() + 5;
        }

        // Publications
        if (!empty($publications)) {
            $this->SetXY($rightX, $yPos);
            $this->ChapterTitle('Publications');
            $this->SetFont('Times', '', 11);
            $this->SetTextColor(0, 0, 128); // Navy blue for right side text
            $this->SetXY($rightX, $this->GetY());
            $this->MultiCell($sectionWidth, 5, 'dsads dasdas prothon alo qbkamdbs', 0, 'L');
            $yPos = $this->GetY() + 5;
        }
    }
}