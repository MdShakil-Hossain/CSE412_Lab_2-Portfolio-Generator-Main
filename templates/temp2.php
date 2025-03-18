<?php
class PDF_Temp2 extends FPDF {
    protected $portfolio;

    public function Header() {
        // No header content since the image doesn't have a repeating header
    }

    public function Footer() {
        // No footer in the provided design
    }

    public function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 102, 204);
        $this->Cell(0, 8, strtoupper($title), 0, 1, 'L');
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

        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, strtoupper($this->portfolio['full_name'] ?? 'BENJAMIN SHAH'), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 10);
        $contact = "Phone: " . ($portfolio['contact_phone'] ?? '123-456-7890') . "\n" .
                   "Email: " . ($portfolio['contact_email'] ?? 'hello@reallygreatsite.com') . "\n" .
                   "Website: " . ($portfolio['website'] ?? 'www.reallygreatsite.com');
        $contactYStart = $this->GetY();
        $this->SetXY(20, $contactYStart);
        $this->MultiCell(100, 6, $contact, 0, 'L');
        $contactHeight = $this->GetY() - $contactYStart;

        $imageWidth = 30;
        $imageX = 210 - 20 - $imageWidth;
        if ($portfolio['photo_path'] && file_exists($portfolio['photo_path'])) {
            $this->Image($portfolio['photo_path'], $imageX, $contactYStart - 10, $imageWidth, 40);
        }

        $imageHeight = 40;
        $maxHeight = max($contactHeight, $portfolio['photo_path'] && file_exists($portfolio['photo_path']) ? $imageHeight : 0);
        $this->SetY($contactYStart + $maxHeight + 5);

        $yPos = $this->GetY();
        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Summary');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, $portfolio['resume_summary'] ?? 'Results-oriented Mechanical and Mechatronics Engineer...', 0, 'J');
        $this->Ln(5);
        $yPos = $this->GetY();

        if (!empty($work_experiences)) {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Work Experience');
            $this->SetFont('Arial', '', 10);
            foreach ($work_experiences as $exp) {
                $this->SetFont('Arial', 'B', 10);
                $jobTitle = $exp['job_title'] ?? 'Unknown Job Title';
                $companyName = $exp['company_name'] ?? 'Unknown Company';
                $this->Cell(0, 5, $jobTitle . ' | ' . $companyName, 0, 1, 'L');
                $this->SetFont('Arial', 'I', 10);
                $this->Cell(0, 5, $exp['job_duration'] ?? 'Unknown Duration', 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->MultiCell(0, 5, '- ' . ($exp['job_responsibilities'] ?? 'No responsibilities provided'), 0, 'J');
                $this->Ln(3);
            }
            $yPos = $this->GetY();
        } else {
            $this->SetXY(20, $yPos);
            $this->ChapterTitle('Work Experience');
            $this->SetFont('Arial', '', 10);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, 'Mechatronics Engineer | Borcole Technologies', 0, 1, 'L');
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 5, 'Jan 2023 - Present', 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(0, 5, '- Led development of an advanced automation system...', 0, 'J');
            $this->Ln(3);
            // ... (rest of the default data)
            $yPos = $this->GetY();
        }

        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Education');
        $this->SetFont('Arial', '', 10);
        if (!empty($academic_backgrounds)) {
            foreach ($academic_backgrounds as $ab) {
                $this->SetFont('Arial', 'B', 10);
                $this->Cell(0, 5, ($ab['degree'] ?? 'Unknown Degree') . ' | ' . ($ab['institute'] ?? 'Unknown Institute'), 0, 1, 'L');
                $this->SetFont('Arial', '', 10);
                $this->Cell(0, 5, $ab['year'] ?? 'Unknown Year', 0, 1, 'L');
                $this->Ln(2);
            }
        } else {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, 'Bachelor of Mechatronics Engineering with Honours | University of Engineering Excellence', 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Aug 2016 - Oct 2019', 0, 1, 'L');
            $this->Ln(2);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, 'Diploma in Mechanical Engineering | Engineering University', 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'May 2014 - May 2016', 0, 1, 'L');
            $this->Ln(2);
        }
        $yPos = $this->GetY();

        $this->SetXY(20, $yPos);
        $this->ChapterTitle('Additional Information');
        $this->SetFont('Arial', '', 10);
        $skills = str_replace("\n", "\n- ", "- " . trim($portfolio['technical_skills'] ?? 'Management, System Integration, Automotive Engineering, Technical Writing, Robotics and Automation, CAD for Mechatronics'));
        $this->MultiCell(0, 5, $skills, 0, 'L');
        $this->Ln(3);
        $languages = str_replace("\n", "\n- ", "- " . trim($portfolio['languages'] ?? 'English, Malay, Japan'));
        $this->MultiCell(0, 5, $languages, 0, 'L');
        $this->Ln(3);
        $certifications = str_replace("\n", "\n- ", "- " . trim($portfolio['certifications'] ?? 'Professional Engineer (PE) License, Project Management Professional (PMP)'));
        $this->MultiCell(0, 5, $certifications, 0, 'L');
        $this->Ln(3);
        $this->MultiCell(0, 5, '- Awards/Activities: ' . ($portfolio['awards_activities'] ?? 'Actively participated in the "Innovation For Tomorrow" community outreach program, promoting STEM education and inspiring local students.'), 0, 'L');
    }
}