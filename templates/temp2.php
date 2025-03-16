<?php


class PDF_Temp2 extends FPDF {
    protected $portfolio;


    public function Header() {
        // Name and Job Title
        $this->SetFont('Arial', 'B', 24);
        $this->SetTextColor(0, 102, 204); // Blue color
        $this->Cell(0, 15, strtoupper($this->portfolio['full_name'] ?? 'FULL NAME'), 0, 1, 'L');
        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(80, 80, 80); // Gray color
        $this->Cell(0, 8, $this->portfolio['job_title'] ?? 'PROFESSIONAL TITLE', 0, 1, 'L');
        $this->SetDrawColor(200, 200, 200);
        $this->Line(20, 45, 190, 45); // Horizontal line
        $this->Ln(5);
    }


    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Phone: ' . ($this->portfolio['contact_phone'] ?? 'N/A') . ' | Email: ' . ($this->portfolio['contact_email'] ?? 'N/A'), 0, 1, 'C');
        $this->Cell(0, 5, 'Address: ' . ($this->portfolio['address'] ?? 'Your Street Address'), 0, 1, 'C');
        $this->SetDrawColor(200, 200, 200);
        $this->Line(20, 272, 190, 272); // Horizontal line above footer
    }


    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 102, 204);
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L');
        $this->SetDrawColor(0, 102, 204);
        $this->Line(20, $this->GetY(), 40, $this->GetY()); // Short blue underline
        $this->SetTextColor(0, 0, 0);
        $this->Ln(5);
    }


    public function generate($portfolio, $academic_backgrounds, $work_experiences, $projects, $publications) {
        $this->portfolio = $portfolio;
        $this->AddPage('P', 'A4');
        $this->SetMargins(20, 20, 20);


        $yPos = $this->GetY();


        // Profile Section
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Profile');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, $portfolio['resume_summary'] ?? $portfolio['short_bio'] ?? 'Lorem ipsum dolor sit amet...', 0, 'J');
        $this->Ln(5);
        $yPos = $this->GetY();


        // Work Experience Section
        if (!empty($work_experiences)) {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Experience');
            $this->SetFont('Arial', '', 10);
            foreach ($work_experiences as $exp) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, $exp['job_duration'] . ' | ' . $exp['company_name'], 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(0, 5, $exp['job_responsibilities'], 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY();
        }


        // Education Section
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
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, '2014 - 2016 | Degree / Major Name', 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'University name here', 0, 1, 'L');
        }
        $yPos = $this->GetY();


        // Skills and Languages in Two Columns
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Skills & Languages');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n- ", "- " . trim($portfolio['technical_skills'] ?? 'Photoshop\nIllustrator\nInDesign\nAfter Effects\nSketch'));
        $languages = str_replace("\n", "\n- ", "- " . trim($portfolio['languages'] ?? 'English\nFrench\nSpanish'));
        $this->SetXY(20, $this->GetY());
        $this->MultiCell(80, 5, $skills, 0, 'L');
        $this->SetXY(110, $this->GetY() - $this->GetY() + $yPos + 20); // Reset Y for languages
        $this->MultiCell(80, 5, $languages, 0, 'L');
        $yPos = max($this->GetY(), $yPos + 40);


        // Projects Section
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


        // Publications Section
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
