<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
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
    'experience' => '',
    'languages' => '',
    'resume_summary' => ''
];

// Check if portfolio exists
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $portfolio = $result->fetch_assoc();
    $portfolio_id = $portfolio['id'];
} else {
    $stmt = $conn->prepare("INSERT INTO portfolios (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $portfolio_id = $stmt->insert_id;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_draft']) || isset($_POST['generate_pdf'])) {
        $full_name = $_POST['full_name'] ?? '';
        $job_title = $_POST['job_title'] ?? '';
        $contact_phone = $_POST['contact_phone'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';
        $address = $_POST['address'] ?? '';
        $short_bio = $_POST['short_bio'] ?? '';
        $soft_skills = $_POST['soft_skills'] ?? '';
        $technical_skills = $_POST['technical_skills'] ?? '';
        $experience = $_POST['experience'] ?? ''; // Fixed: Use ?? to avoid undefined key
        $languages = $_POST['languages'] ?? '';   // Fixed: Use ?? to avoid undefined key
        $resume_summary = $_POST['resume_summary'] ?? ''; // Fixed: Use ?? to avoid undefined key

        // Handle photo upload
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
                } else {
                    echo "Failed to move uploaded file. Check permissions for uploads directory.";
                }
            }
        }

        $stmt = $conn->prepare("UPDATE portfolios SET full_name = ?, job_title = ?, contact_phone = ?, contact_email = ?, address = ?, photo_path = ?, short_bio = ?, soft_skills = ?, technical_skills = ?, experience = ?, languages = ?, resume_summary = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssssi", $full_name, $job_title, $contact_phone, $contact_email, $address, $portfolio['photo_path'], $short_bio, $soft_skills, $technical_skills, $experience, $languages, $resume_summary, $portfolio_id);
        $stmt->execute();

        if (isset($_POST['generate_pdf'])) {
            header('Location: generate_pdf.php');
            exit;
        }

        // Update portfolio array to reflect saved values
        $portfolio['full_name'] = $full_name;
        $portfolio['job_title'] = $job_title;
        $portfolio['contact_phone'] = $contact_phone;
        $portfolio['contact_email'] = $contact_email;
        $portfolio['address'] = $address;
        $portfolio['short_bio'] = $short_bio;
        $portfolio['soft_skills'] = $soft_skills;
        $portfolio['technical_skills'] = $technical_skills;
        $portfolio['experience'] = $experience;
        $portfolio['languages'] = $languages;
        $portfolio['resume_summary'] = $resume_summary;
    }

    // Handle academic background (separate submission)
    if (isset($_POST['add_academic'])) {
        $institute = $_POST['institute'] ?? '';
        $degree = $_POST['degree'] ?? '';
        $year = $_POST['year'] ?? '';
        $grade = $_POST['grade'] ?? '';
        if ($institute && $degree) { // Ensure required fields are filled
            $stmt = $conn->prepare("INSERT INTO academic_backgrounds (portfolio_id, institute, degree, year, grade) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $portfolio_id, $institute, $degree, $year, $grade);
            $stmt->execute();
        }

        // Preserve other form values
        $portfolio['full_name'] = $_POST['full_name'] ?? $portfolio['full_name'];
        $portfolio['job_title'] = $_POST['job_title'] ?? $portfolio['job_title'];
        $portfolio['contact_phone'] = $_POST['contact_phone'] ?? $portfolio['contact_phone'];
        $portfolio['contact_email'] = $_POST['contact_email'] ?? $portfolio['contact_email'];
        $portfolio['address'] = $_POST['address'] ?? $portfolio['address'];
        $portfolio['short_bio'] = $_POST['short_bio'] ?? $portfolio['short_bio'];
        $portfolio['soft_skills'] = $_POST['soft_skills'] ?? $portfolio['soft_skills'];
        $portfolio['technical_skills'] = $_POST['technical_skills'] ?? $portfolio['technical_skills'];
        $portfolio['experience'] = $_POST['experience'] ?? $portfolio['experience'];
        $portfolio['languages'] = $_POST['languages'] ?? $portfolio['languages'];
        $portfolio['resume_summary'] = $_POST['resume_summary'] ?? $portfolio['resume_summary'];
    }

    // Handle work experience
    if (isset($_POST['add_experience'])) {
        $company_name = $_POST['company_name'] ?? '';
        $job_duration = $_POST['job_duration'] ?? '';
        $job_responsibilities = $_POST['job_responsibilities'] ?? '';
        if ($company_name) { // Ensure required field is filled
            $stmt = $conn->prepare("INSERT INTO work_experiences (portfolio_id, company_name, job_duration, job_responsibilities) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $portfolio_id, $company_name, $job_duration, $job_responsibilities);
            $stmt->execute();
        }

        // Preserve other form values
        $portfolio['full_name'] = $_POST['full_name'] ?? $portfolio['full_name'];
        $portfolio['job_title'] = $_POST['job_title'] ?? $portfolio['job_title'];
        $portfolio['contact_phone'] = $_POST['contact_phone'] ?? $portfolio['contact_phone'];
        $portfolio['contact_email'] = $_POST['contact_email'] ?? $portfolio['contact_email'];
        $portfolio['address'] = $_POST['address'] ?? $portfolio['address'];
        $portfolio['short_bio'] = $_POST['short_bio'] ?? $portfolio['short_bio'];
        $portfolio['soft_skills'] = $_POST['soft_skills'] ?? $portfolio['soft_skills'];
        $portfolio['technical_skills'] = $_POST['technical_skills'] ?? $portfolio['technical_skills'];
        $portfolio['experience'] = $_POST['experience'] ?? $portfolio['experience'];
        $portfolio['languages'] = $_POST['languages'] ?? $portfolio['languages'];
        $portfolio['resume_summary'] = $_POST['resume_summary'] ?? $portfolio['resume_summary'];
    }

    // Handle projects
    if (isset($_POST['add_project'])) {
        $project_title = $_POST['project_title'] ?? '';
        $project_description = $_POST['project_description'] ?? '';
        if ($project_title) { // Ensure required field is filled
            $stmt = $conn->prepare("INSERT INTO projects (portfolio_id, project_title, project_description) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $portfolio_id, $project_title, $project_description);
            $stmt->execute();
        }

        // Preserve other form values
        $portfolio['full_name'] = $_POST['full_name'] ?? $portfolio['full_name'];
        $portfolio['job_title'] = $_POST['job_title'] ?? $portfolio['job_title'];
        $portfolio['contact_phone'] = $_POST['contact_phone'] ?? $portfolio['contact_phone'];
        $portfolio['contact_email'] = $_POST['contact_email'] ?? $portfolio['contact_email'];
        $portfolio['address'] = $_POST['address'] ?? $portfolio['address'];
        $portfolio['short_bio'] = $_POST['short_bio'] ?? $portfolio['short_bio'];
        $portfolio['soft_skills'] = $_POST['soft_skills'] ?? $portfolio['soft_skills'];
        $portfolio['technical_skills'] = $_POST['technical_skills'] ?? $portfolio['technical_skills'];
        $portfolio['experience'] = $_POST['experience'] ?? $portfolio['experience'];
        $portfolio['languages'] = $_POST['languages'] ?? $portfolio['languages'];
        $portfolio['resume_summary'] = $_POST['resume_summary'] ?? $portfolio['resume_summary'];
    }
}

// Fetch existing entries
$academic_backgrounds = $conn->query("SELECT * FROM academic_backgrounds WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$work_experiences = $conn->query("SELECT * FROM work_experiences WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
$projects = $conn->query("SELECT * FROM projects WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Portfolio</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2><?php echo $portfolio['id'] ? 'Edit' : 'Create'; ?> Portfolio</h2>
    <form method="POST" enctype="multipart/form-data">
        <h3>Personal Information</h3>
        <label>Full Name:</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>" required><br>
        <label>Job Title:</label><input type="text" name="job_title" value="<?php echo htmlspecialchars($portfolio['job_title'] ?? ''); ?>"><br>
        <label>Contact Phone:</label><input type="text" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone'] ?? ''); ?>"><br>
        <label>Contact Email:</label><input type="email" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email'] ?? ''); ?>"><br>
        <label>Address:</label><input type="text" name="address" value="<?php echo htmlspecialchars($portfolio['address'] ?? ''); ?>"><br>
        <label>Photo (.jpg/.png):</label><input type="file" name="photo"><br>
        <?php if ($portfolio['photo_path']) echo "<img src='{$portfolio['photo_path']}' width='100'>"; ?>
        <label>Short Bio:</label><textarea name="short_bio"><?php echo htmlspecialchars($portfolio['short_bio'] ?? ''); ?></textarea><br>

        <h3>Skills</h3>
        <label>Soft Skills:</label><textarea name="soft_skills"><?php echo htmlspecialchars($portfolio['soft_skills'] ?? ''); ?></textarea><br>
        <label>Technical Skills:</label><textarea name="technical_skills"><?php echo htmlspecialchars($portfolio['technical_skills'] ?? ''); ?></textarea><br>

        <h3>Languages (Optional)</h3>
        <label>Languages:</label><textarea name="languages"><?php echo htmlspecialchars($portfolio['languages'] ?? ''); ?></textarea><br>

        <h3>Resume Summary/Objective (Optional)</h3>
        <label>Summary/Objective:</label><textarea name="resume_summary"><?php echo htmlspecialchars($portfolio['resume_summary'] ?? ''); ?></textarea><br>

        <br><br>
        <button type="submit" name="save_draft">Save Draft</button>
        <button type="submit" name="generate_pdf">Generate PDF</button>
    </form>

    <!-- Separate form for Academic Background -->
    <h3>Academic Background</h3>
    <?php foreach ($academic_backgrounds as $ab): ?>
        <p><?php echo htmlspecialchars("{$ab['institute']}, {$ab['degree']}, {$ab['year']}, {$ab['grade']}"); ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <!-- Hidden fields to preserve other form values -->
        <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>">
        <input type="hidden" name="job_title" value="<?php echo htmlspecialchars($portfolio['job_title'] ?? ''); ?>">
        <input type="hidden" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone'] ?? ''); ?>">
        <input type="hidden" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email'] ?? ''); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($portfolio['address'] ?? ''); ?>">
        <input type="hidden" name="short_bio" value="<?php echo htmlspecialchars($portfolio['short_bio'] ?? ''); ?>">
        <input type="hidden" name="soft_skills" value="<?php echo htmlspecialchars($portfolio['soft_skills'] ?? ''); ?>">
        <input type="hidden" name="technical_skills" value="<?php echo htmlspecialchars($portfolio['technical_skills'] ?? ''); ?>">
        <input type="hidden" name="languages" value="<?php echo htmlspecialchars($portfolio['languages'] ?? ''); ?>">
        <input type="hidden" name="resume_summary" value="<?php echo htmlspecialchars($portfolio['resume_summary'] ?? ''); ?>">

        <label>Institute:</label><input type="text" name="institute" required><br>
        <label>Degree:</label><input type="text" name="degree" required><br>
        <label>Year:</label><input type="text" name="year"><br>
        <label>Grade:</label><input type="text" name="grade"><br>
        <button type="submit" name="add_academic">Add Academic Background</button>
    </form>

    <!-- Separate form for Work Experience -->
    <h3>Experience (Optional)</h3>
    <?php foreach ($work_experiences as $exp): ?>
        <p><?php echo htmlspecialchars("{$exp['company_name']}, {$exp['job_duration']}: {$exp['job_responsibilities']}"); ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <!-- Hidden fields to preserve other form values -->
        <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>">
        <input type="hidden" name="job_title" value="<?php echo htmlspecialchars($portfolio['job_title'] ?? ''); ?>">
        <input type="hidden" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone'] ?? ''); ?>">
        <input type="hidden" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email'] ?? ''); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($portfolio['address'] ?? ''); ?>">
        <input type="hidden" name="short_bio" value="<?php echo htmlspecialchars($portfolio['short_bio'] ?? ''); ?>">
        <input type="hidden" name="soft_skills" value="<?php echo htmlspecialchars($portfolio['soft_skills'] ?? ''); ?>">
        <input type="hidden" name="technical_skills" value="<?php echo htmlspecialchars($portfolio['technical_skills'] ?? ''); ?>">
        <input type="hidden" name="languages" value="<?php echo htmlspecialchars($portfolio['languages'] ?? ''); ?>">
        <input type="hidden" name="resume_summary" value="<?php echo htmlspecialchars($portfolio['resume_summary'] ?? ''); ?>">

        <label>Company Name:</label><input type="text" name="company_name"><br>
        <label>Job Duration:</label><input type="text" name="job_duration"><br>
        <label>Job Responsibilities:</label><textarea name="job_responsibilities"></textarea><br>
        <button type="submit" name="add_experience">Add Experience</button>
    </form>

    <!-- Separate form for Projects -->
    <h3>Projects (Optional)</h3>
    <?php foreach ($projects as $proj): ?>
        <p><?php echo htmlspecialchars("{$proj['project_title']}: {$proj['project_description']}"); ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <!-- Hidden fields to preserve other form values -->
        <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>">
        <input type="hidden" name="job_title" value="<?php echo htmlspecialchars($portfolio['job_title'] ?? ''); ?>">
        <input type="hidden" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone'] ?? ''); ?>">
        <input type="hidden" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email'] ?? ''); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($portfolio['address'] ?? ''); ?>">
        <input type="hidden" name="short_bio" value="<?php echo htmlspecialchars($portfolio['short_bio'] ?? ''); ?>">
        <input type="hidden" name="soft_skills" value="<?php echo htmlspecialchars($portfolio['soft_skills'] ?? ''); ?>">
        <input type="hidden" name="technical_skills" value="<?php echo htmlspecialchars($portfolio['technical_skills'] ?? ''); ?>">
        <input type="hidden" name="languages" value="<?php echo htmlspecialchars($portfolio['languages'] ?? ''); ?>">
        <input type="hidden" name="resume_summary" value="<?php echo htmlspecialchars($portfolio['resume_summary'] ?? ''); ?>">

        <label>Project Title:</label><input type="text" name="project_title"><br>
        <label>Project Description:</label><textarea name="project_description"></textarea><br>
        <button type="submit" name="add_project">Add Project</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>