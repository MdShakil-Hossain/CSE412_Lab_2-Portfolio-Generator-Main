<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
header('Location: signin.php');
exit;
}
$user_id = $_SESSION['user_id'];
// Store the selected template in the session if provided
if (isset($_POST['template'])) {
$_SESSION['selected_template'] = $_POST['template'];
}
$portfolio = [
'full_name' => '',
'job_title' => '',
'contact_phone' => '',
'contact_email' => '',
'address' => '',
'photo_path' => '',
'short_bio' => '',
'soft_skills' => '',
'technical_skills' => '',
'languages' => '',
'resume_summary' => '',
'template_name' => 'temp1' // Default template
];
// Check if portfolio exists
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
$portfolio = $result->fetch_assoc();
$portfolio_id = $portfolio['id'];
if (!isset($_SESSION['selected_template']) && !empty($portfolio['template_name'])) {
$_SESSION['selected_template'] = $portfolio['template_name'];
}
} else {
$stmt = $conn->prepare("INSERT INTO portfolios (user_id) VALUES (?)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$portfolio_id = $stmt->insert_id;
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE id = ?");
$stmt->bind_param("i", $portfolio_id);
$stmt->execute();
$result = $stmt->get_result();
$portfolio = $result->fetch_assoc();
}
if (!isset($_SESSION['selected_template'])) {
header('Location: dashboard.php');
exit;
}
function sanitizeInput($input) {
return str_replace(['æç', 'æ', 'ç', 'â€¢'], '', $input);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['save_draft']) || isset($_POST['generate_pdf'])) {
$full_name = sanitizeInput($_POST['full_name'] ?? '');
$job_title = sanitizeInput($_POST['job_title'] ?? '');
$contact_phone = sanitizeInput($_POST['contact_phone'] ?? '');
$contact_email = sanitizeInput($_POST['contact_email'] ?? '');
$address = sanitizeInput($_POST['address'] ?? '');
$short_bio = sanitizeInput($_POST['short_bio'] ?? '');
$soft_skills = sanitizeInput($_POST['soft_skills'] ?? '');
$technical_skills = sanitizeInput($_POST['technical_skills'] ?? '');
$languages = sanitizeInput($_POST['languages'] ?? '');
$resume_summary = sanitizeInput($_POST['resume_summary'] ?? '');
$template_name = $_SESSION['selected_template'];
if (!empty($_FILES['photo']['name'])) {
$file = $_FILES['photo'];
$allowed = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (in_array($ext, $allowed) && $file['size'] <= 2000000) {
if (!file_exists('uploads')) {
mkdir('uploads', 0777, true);
}
$photo_path = "uploads/portfolio_$portfolio_id.$ext";
if (move_uploaded_file($file['tmp_name'], $photo_path)) {
$portfolio['photo_path'] = $photo_path;
}
}
}
$stmt = $conn->prepare("UPDATE portfolios SET full_name = ?, job_title = ?, contact_phone = ?, contact_email = ?, address = ?, photo_path = ?, short_bio = ?, soft_skills = ?, technical_skills = ?, languages = ?, resume_summary = ?, template_name = ? WHERE id = ?");
$stmt->bind_param("ssssssssssssi", $full_name, $job_title, $contact_phone, $contact_email, $address, $portfolio['photo_path'], $short_bio, $soft_skills, $technical_skills, $languages, $resume_summary, $template_name, $portfolio_id);
$stmt->execute();
if (isset($_POST['generate_pdf'])) {
header('Location: generate_pdf.php');
exit;
}
$portfolio['full_name'] = $full_name;
$portfolio['job_title'] = $job_title;
$portfolio['contact_phone'] = $contact_phone;
$portfolio['contact_email'] = $contact_email;
$portfolio['address'] = $address;
$portfolio['short_bio'] = $short_bio;
$portfolio['soft_skills'] = $soft_skills;
$portfolio['technical_skills'] = $technical_skills;
$portfolio['languages'] = $languages;
$portfolio['resume_summary'] = $resume_summary;
$portfolio['template_name'] = $template_name;
}
if (isset($_POST['add_academic'])) {
$institute = sanitizeInput($_POST['institute'] ?? '');
$degree = sanitizeInput($_POST['degree'] ?? '');
$year = sanitizeInput($_POST['year'] ?? '');
$grade = sanitizeInput($_POST['grade'] ?? '');
if ($institute && $degree) {
$stmt = $conn->prepare("INSERT INTO academic_backgrounds (portfolio_id, institute, degree, year, grade) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $portfolio_id, $institute, $degree, $year, $grade);
$stmt->execute();
}
}
if (isset($_POST['add_experience'])) {
$company_name = sanitizeInput($_POST['company_name'] ?? '');
$job_duration = sanitizeInput($_POST['job_duration'] ?? '');
$job_responsibilities = sanitizeInput($_POST['job_responsibilities'] ?? '');
if ($company_name) {
$stmt = $conn->prepare("INSERT INTO work_experiences (portfolio_id, company_name, job_duration, job_responsibilities) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $portfolio_id, $company_name, $job_duration, $job_responsibilities);
$stmt->execute();
}
}
if (isset($_POST['add_project'])) {
$project_title = sanitizeInput($_POST['project_title'] ?? '');
$project_description = sanitizeInput($_POST['project_description'] ?? '');
if ($project_title) {
$stmt = $conn->prepare("INSERT INTO projects (portfolio_id, project_title, project_description) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $portfolio_id, $project_title, $project_description);
$stmt->execute();
}
}
if (isset($_POST['add_publication'])) {
$publication_title = sanitizeInput($_POST['publication_title'] ?? '');
$publication_description = sanitizeInput($_POST['publication_description'] ?? '');
if ($publication_title) {
$stmt = $conn->prepare("INSERT INTO publications (portfolio_id, title, description) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $portfolio_id, $publication_title, $publication_description);
$stmt->execute();
}
}
}
$academic_backgrounds = $conn->query("SELECT * FROM academic_backgrounds WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$work_experiences = $conn->query("SELECT * FROM work_experiences WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$projects = $conn->query("SELECT * FROM projects WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$publications = $conn->query("SELECT * FROM publications WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portfolio Editor</title>
<link rel="stylesheet" href="css/portfolio-style.css">
</head>
<body>
<div class="portfolio-editor">
<div class="editor-header">
<h1><?php echo !empty($portfolio['id']) ? 'Edit' : 'Create'; ?> Portfolio</h1>
<div class="template-tag">
Template: <?php echo htmlspecialchars($_SESSION['selected_template']); ?>
</div>
</div>
<form class="editor-form" method="POST" enctype="multipart/form-data">
<!-- Personal Information Section -->
<section class="editor-section">
<h2>Personal Information</h2>
<div class="form-grid">
<div class="input-group">
<label>Full Name</label>
<input type="text" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>" required>
</div>
<div class="input-group">
<label>Job Title</label>
<input type="text" name="job_title" value="<?php echo htmlspecialchars($portfolio['job_title'] ?? ''); ?>">
</div>
<div class="input-group">
<label>Contact Phone</label>
<input type="text" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone'] ?? ''); ?>">
</div>
<div class="input-group">
<label>Contact Email</label>
<input type="email" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email'] ?? ''); ?>">
</div>
<div class="input-group">
<label>Address</label>
<input type="text" name="address" value="<?php echo htmlspecialchars($portfolio['address'] ?? ''); ?>">
</div>
<div class="input-group full-width">
<label>Photo Upload</label>
<div class="photo-uploader">
<input type="file" name="photo">
<?php if ($portfolio['photo_path']): ?>
<img src="<?php echo $portfolio['photo_path']; ?>" class="photo-preview">
<?php endif; ?>
</div>
</div>
<div class="input-group full-width">
<label>Short Bio</label>
<textarea name="short_bio"><?php echo htmlspecialchars($portfolio['short_bio'] ?? ''); ?></textarea>
</div>
</div>
</section>
<!-- Skills Section -->
<section class="editor-section">
<h2>Skills & Qualifications</h2>
<div class="form-columns">
<div class="input-group">
<label>Soft Skills</label>
<textarea name="soft_skills"><?php echo htmlspecialchars($portfolio['soft_skills'] ?? ''); ?></textarea>
</div>
<div class="input-group">
<label>Technical Skills</label>
<textarea name="technical_skills"><?php echo htmlspecialchars($portfolio['technical_skills'] ?? ''); ?></textarea>
</div>
<div class="input-group">
<label>Languages</label>
<textarea name="languages"><?php echo htmlspecialchars($portfolio['languages'] ?? ''); ?></textarea>
</div>
<div class="input-group">
<label>Resume Summary</label>
<textarea name="resume_summary"><?php echo htmlspecialchars($portfolio['resume_summary'] ?? ''); ?></textarea>
</div>
</div>
</section>
<!-- Academic Background Section -->
<section class="editor-section">
<h2>Academic Background</h2>
<?php foreach ($academic_backgrounds as $ab): ?>
<div class="entry-item">
<?php echo htmlspecialchars("{$ab['institute']}, {$ab['degree']}, {$ab['year']}, {$ab['grade']}"); ?>
</div>
<?php endforeach; ?>
<div class="form-grid">
<div class="input-group">
<input type="text" name="institute" placeholder="Institute">
</div>
<div class="input-group">
<input type="text" name="degree" placeholder="Degree">
</div>
<div class="input-group">
<input type="text" name="year" placeholder="Year">
</div>
<div class="input-group">
<input type="text" name="grade" placeholder="Grade">
</div>
</div>
<button type="submit" name="add_academic" class="form-button">Add Academic Background</button>
</section>
<!-- Work Experience Section -->
<section class="editor-section">
<h2>Work Experience</h2>
<?php foreach ($work_experiences as $exp): ?>
<div class="entry-item">
<?php echo htmlspecialchars("{$exp['company_name']}, {$exp['job_duration']}: {$exp['job_responsibilities']}"); ?>
</div>
<?php endforeach; ?>
<div class="form-grid">
<div class="input-group">
<input type="text" name="company_name" placeholder="Company Name">
</div>
<div class="input-group">
<input type="text" name="job_duration" placeholder="Job Duration">
</div>
</div>
<div class="input-group">
<textarea name="job_responsibilities" placeholder="Job Responsibilities"></textarea>
</div>
<button type="submit" name="add_experience" class="form-button">Add Experience</button>
</section>
<!-- Projects Section -->
<section class="editor-section">
<h2>Projects</h2>
<?php foreach ($projects as $proj): ?>
<div class="entry-item">
<?php echo htmlspecialchars("{$proj['project_title']}: {$proj['project_description']}"); ?>
</div>
<?php endforeach; ?>
<div class="form-grid">
<div class="input-group">
<input type="text" name="project_title" placeholder="Project Title">
</div>
</div>
<div class="input-group">
<textarea name="project_description" placeholder="Project Description"></textarea>
</div>
<button type="submit" name="add_project" class="form-button">Add Project</button>
</section>
<!-- Publications Section -->
<section class="editor-section">
<h2>Publications</h2>
<?php foreach ($publications as $pub): ?>
<div class="entry-item">
<?php echo htmlspecialchars("{$pub['title']}: {$pub['description']}"); ?>
</div>
<?php endforeach; ?>
<div class="form-grid">
<div class="input-group">
<input type="text" name="publication_title" placeholder="Publication Title">
</div>
</div>
<div class="input-group">
<textarea name="publication_description" placeholder="Publication Description"></textarea>
</div>
<button type="submit" name="add_publication" class="form-button">Add Publication</button>
</section>
<!-- Form Actions -->
<div class="form-actions">
<button type="submit" name="save_draft" class="primary-button">Save Draft</button>
<button type="submit" name="generate_pdf" class="secondary-button">Generate PDF</button>
<a href="dashboard.php" class="cancel-button">Back to Dashboard</a>
</div>
</form>
</div>
</body>
</html>

