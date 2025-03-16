<?php
class PDF_Temp1 extends FPDF {
    protected $portfolio;


    public function Header() {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 15, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 8, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(10);
    }


    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }


    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230, 230, 230);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }


    public function getNumLines($text) {
        $lines = explode("\n", $text);
        return count($lines);
    }


    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(20, 20, 20);


        $this->SetFont('Arial', '', 10);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? 'N/A') . "\n" .
                   "Email: " . ($portfolio['contact_email'] ?? 'N/A') . "\n" .
                   "Address: " . ($portfolio['address'] ?? 'Your Street Address');
        $contactYStart = $this->GetY();
        $this->MultiCell(100, 5, $contact, 0, 'L');
        $contactHeight = $this->GetY() - $contactYStart;


        $imageWidth = 40;
        $imageX = 210 - 20 - $imageWidth;
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], $imageX, $contactYStart, $imageWidth, 40);
            $this->SetLineWidth(0.2);
            $this->SetDrawColor(150, 150, 150);
            $this->Rect($imageX, $contactYStart, $imageWidth, 40);
        }


        $imageHeight = 40;
        $maxHeight = max($contactHeight, $portfolio['photo_path'] && file_exists($portfolio['photo_path']) ? $imageHeight : 0);
        $this->SetY($contactYStart + $maxHeight + 5);


        $yPos = $this->GetY();


        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Profile');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 'J');
        $this->Ln(5);
        $yPos = $this->GetY();


        if (!empty($work_experiences)) {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Experience');
            $this->SetFont('Arial', '', 10);
            foreach ($work_experiences as $exp) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $exp['job_duration'], 0, 1, 'L');
                $this->SetFont('Arial', 'I', 10);
                $this->Cell(0, 5, $exp['company_name'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(0, 5, $exp['job_responsibilities'], 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY();
        }


        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Education');
        $this->SetFont('Arial', '', 10);
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $ab['year'] . ' | ' . $ab['degree'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->Cell(0, 5, $ab['institute'], 0, 1, 'L');
                $this->Ln(2);
            }
        } else {
            $this->Cell(0, 5, '2014 - 2016 | Degree / Major Name', 0, 1, 'L');
            $this->Cell(0, 5, 'University name here', 0, 1, 'L');
        }
        $yPos = $this->GetY();


        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Skills');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n- ", "- " . trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign\nAfter Effects\nSketch'));
        $skills = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $skills);
        $this->MultiCell(0, 5, $skills, 0, 'L');
        $yPos = $this->GetY();


        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Languages');
        $this->SetFont('Arial', '', 10);
        $languages = str_replace("\n", "\n- ", "- " . trim($portfolio['languages'] ?? 'English\nFrench\nSpanish'));
        $languages = str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $languages);
        $this->MultiCell(0, 5, $languages, 0, 'L');
        $yPos = $this->GetY();


        if (!empty($projects)) {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Projects');
            $this->SetFont('Arial', '', 10);
            foreach ($projects as $proj) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $proj['project_title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(0, 5, $proj['project_description'], 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY();
        }


        // New: Publications Section
        if (!empty($publications)) {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Publications');
            $this->SetFont('Arial', '', 10);
            foreach ($publications as $pub) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $pub['title'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(0, 5, $pub['description'], 0, 'J');
                $this->Ln(3);
            }
        }
    }
}
