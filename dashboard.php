<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

// Get user email for display
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_email);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portfolio Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($user_email); ?></h1>
            <p class="header-text">Let's build your professional portfolio</p>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="container">
            <div class="create-portfolio-card">
                <div class="card-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2>Create Your Portfolio</h2>
                <p class="card-description">Start from scratch and build a professional portfolio that showcases your skills and experience.</p>
                <a href="portfolio_edit.php" class="btn btn-primary btn-large">
                    <i class="fas fa-plus"></i> Create New Portfolio
                </a>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p><a href="logout.php" class="text-link"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
            <p>&copy; <?php echo date('Y'); ?> Portfolio Generator. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>