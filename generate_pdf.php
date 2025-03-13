<?php
session_start();
require 'config.php';
require 'libs/fpdf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();
$portfolio_id = $portfolio['id'];
$template_name = $portfolio['template_name'] ?? 'temp1'; // Use stored template name, default to temp1

// Debug: Check the template name (optional, can be removed after testing)
// var_dump($template_name); exit;

$template_file = "templates/{$template_name}.php";

// Validate template file
if (!file_exists($template_file)) {
    die("Template not found: {$template_name}");
}

// Include the selected template
require_once $template_file;

// Create PDF instance based on template
$class_name = 'PDF_' . str_replace('temp', 'Temp', $template_name);
if (!class_exists($class_name)) {
    die("Template class not found: {$class_name}");
}

$academic_backgrounds = $conn->query("SELECT * FROM academic_backgrounds WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$work_experiences = $conn->query("SELECT * FROM work_experiences WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$projects = $conn->query("SELECT * FROM projects WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);

$pdf = new $class_name();
$pdf->generate($portfolio, $academic_backgrounds, $work_experiences, $projects);
$pdf->Output('D', 'portfolio.pdf');