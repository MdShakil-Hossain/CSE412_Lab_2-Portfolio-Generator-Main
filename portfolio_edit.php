<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$portfolio = ['full_name' => '', 'contact_phone' => '', 'contact_email' => '', 'photo_path' => '', 'short_bio' => '', 'soft_skills' => '', 'technical_skills' => ''];

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
        $full_name = $_POST['full_name'];
        $contact_phone = $_POST['contact_phone'];
        $contact_email = $_POST['contact_email'];
        $short_bio = $_POST['short_bio'];
        $soft_skills = $_POST['soft_skills'];
        $technical_skills = $_POST['technical_skills'];

        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $file = $_FILES['photo'];
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $file['size'] <= 2000000) { // 2MB limit
                $photo_path = "uploads/portfolio_$portfolio_id.$ext";
                move_uploaded_file($file['tmp_name'], $photo_path);
                $portfolio['photo_path'] = $photo_path;
            }
        }

        $stmt = $conn->prepare("UPDATE portfolios SET full_name = ?, contact_phone = ?, contact_email = ?, photo_path = ?, short_bio = ?, soft_skills = ?, technical_skills = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $full_name, $contact_phone, $contact_email, $portfolio['photo_path'], $short_bio, $soft_skills, $technical_skills, $portfolio_id);
        $stmt->execute();

        if (isset($_POST['generate_pdf'])) {
            header('Location: generate_pdf.php');
            exit;
        }
    }

    // Handle academic background
    if (isset($_POST['add_academic'])) {
        $institute = $_POST['institute'];
        $degree = $_POST['degree'];
        $year = $_POST['year'];
        $grade = $_POST['grade'];
        $stmt = $conn->prepare("INSERT INTO academic_backgrounds (portfolio_id, institute, degree, year, grade) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $portfolio_id, $institute, $degree, $year, $grade);
        $stmt->execute();
    }

    // Similar logic for work_experiences and projects (add, edit, delete) can be implemented here
}

// Fetch existing entries
$academic_backgrounds = $conn->query("SELECT * FROM academic_backgrounds WHERE portfolio_id = $portfolio_id")->fetch_all(MYSQLI_ASSOC);
// Add similar queries for work_experiences and projects
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
        <label>Full Name:</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($portfolio['full_name']); ?>"><br>
        <label>Contact Phone:</label><input type="text" name="contact_phone" value="<?php echo htmlspecialchars($portfolio['contact_phone']); ?>"><br>
        <label>Contact Email:</label><input type="email" name="contact_email" value="<?php echo htmlspecialchars($portfolio['contact_email']); ?>"><br>
        <label>Photo (.jpg/.png):</label><input type="file" name="photo"><br>
        <?php if ($portfolio['photo_path']) echo "<img src='{$portfolio['photo_path']}' width='100'>"; ?>
        <label>Short Bio:</label><textarea name="short_bio"><?php echo htmlspecialchars($portfolio['short_bio']); ?></textarea><br>

        <h3>Skills</h3>
        <label>Soft Skills:</label><textarea name="soft_skills"><?php echo htmlspecialchars($portfolio['soft_skills']); ?></textarea><br>
        <label>Technical Skills:</label><textarea name="technical_skills"><?php echo htmlspecialchars($portfolio['technical_skills']); ?></textarea><br>

        <h3>Academic Background</h3>
        <?php foreach ($academic_backgrounds as $ab): ?>
            <p><?php echo htmlspecialchars("$ab[institute], $ab[degree], $ab[year], $ab[grade]"); ?></p>
        <?php endforeach; ?>
        <label>Institute:</label><input type="text" name="institute"><br>
        <label>Degree:</label><input type="text" name="degree"><br>
        <label>Year:</label><input type="text" name="year"><br>
        <label>Grade:</label><input type="text" name="grade"><br>
        <button type="submit" name="add_academic">Add Academic Background</button>

        <!-- Add similar sections for Work Experience and Projects -->

        <br><br>
        <button type="submit" name="save_draft">Save Draft</button>
        <button type="submit" name="generate_pdf">Generate PDF</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>