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

// Check if a portfolio exists for the user and get the template name
$has_portfolio = false;
$selected_template = 'temp1'; // Default template
$stmt = $conn->prepare("SELECT template_name FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $has_portfolio = true;
    $portfolio = $result->fetch_assoc();
    $selected_template = $portfolio['template_name'] ?? 'temp1';
}
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
            <form method="POST" action="portfolio_edit.php">
                <div class="create-portfolio-card">
                    <div class="card-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h2>Select a Template</h2>
                    <p class="card-description">Choose a template to start building your portfolio.</p>
                    <select name="template" class="form-control" style="margin-bottom: 1rem; width: 100%; max-width: 300px;">
                        <option value="temp1" <?php echo $selected_template === 'temp1' ? 'selected' : ''; ?>>Template 1 (Laura Parker Style)</option>
                        <option value="temp2" <?php echo $selected_template === 'temp2' ? 'selected' : ''; ?>>Template 2 (Single Column)</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-plus"></i> Create New Portfolio
                    </button>
                </div>
            </form>

            <?php if ($has_portfolio): ?>
                <form method="POST" action="portfolio_edit.php">
                    <div class="enhance-portfolio-card">
                        <div class="card-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h2>Enhance Existing Portfolio</h2>
                        <p class="card-description">Update or refine your existing portfolio with new details.</p>
                        <select name="template" class="form-control" style="margin-bottom: 1rem; width: 100%; max-width: 300px;">
                            <option value="temp1" <?php echo $selected_template === 'temp1' ? 'selected' : ''; ?>>Template 1 (Laura Parker Style)</option>
                            <option value="temp2" <?php echo $selected_template === 'temp2' ? 'selected' : ''; ?>>Template 2 (Single Column)</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-large">
                            <i class="fas fa-pen"></i> Enhance Existing Portfolio
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p><a href="logout.php" class="text-link"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
            <p>© <?php echo date('Y'); ?> Portfolio Generator. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>