<?php
session_start();
require_once 'config.php'; // Ensure this file exists with database credentials

// Redirect logged-in users
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
}

// Fetch press logos from database
$logos = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT logo_name FROM press_logos ORDER BY display_order");
        $logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching logos: " . $e->getMessage());
    }
}

// Get portfolio count
$portfolio_count = 0;
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM portfolios");
        $result = $stmt->fetch();
        $portfolio_count = $result['total'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error fetching portfolio count: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Generator | Professional Portfolio Builder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="tagline">Fast. Easy. Effective.</div>
            <h1>Portfolio Generator</h1>
            <h2>The Ultimate Professional Portfolio Builder</h2>
            
            <!-- Dynamic portfolio counter -->
            <div class="stats">
                <p>Over <?php echo number_format($portfolio_count); ?> portfolios created!</p>
            </div>

            <p class="header-text">Create a stunning professional portfolio that showcases your skills, experience, and achievements. Impress employers with a polished presentation of your career journey.</p>
            
            <div class="cta-buttons">
                <a href="signup.php" class="btn btn-primary">Create New Portfolio</a>
                <a href="<?php echo isset($_SESSION['user_id']) ? 'portfolio_edit.php' : 'signin.php?redirect=edit'; ?>" 
                   class="btn btn-secondary">
                   Enhance Existing Portfolio
                </a>
            </div>

            <div class="press-logos">
                <span>As seen in</span>
                <div class="logos">
                    <?php if (!empty($logos)): ?>
                        <?php foreach ($logos as $logo): ?>
                            <span><?php echo htmlspecialchars($logo['logo_name']); ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback logos -->
                        <span>Forbes</span>
                        <span>TechCrunch</span>
                        <span>LinkedIn</span>
                        <span>TheVerge</span>
                        <span>BusinessInsider</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="preview-section">
        <div class="container">
            <div class="preview-content">
                <div class="preview-text">
                    <h3>See What You'll Create</h3>
                    <div class="portfolio-sample">
                        <div class="sample-header">
                            <h4>John Doe</h4>
                            <p>Senior Software Developer</p>
                        </div>
                        <div class="sample-section">
                            <h5>Skills</h5>
                            <ul>
                                <li>Full Stack Development</li>
                                <li>Cloud Architecture</li>
                                <li>Team Leadership</li>
                            </ul>
                        </div>
                        <div class="sample-section">
                            <h5>Experience</h5>
                            <p>Tech Lead @ Tech Corp (2019-Present)</p>
                            <p>Senior Developer @ Digital Solutions (2016-2019)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-links">
        <p>Already have an account? <a href="signin.php">Sign In</a></p>
    </div>
</body>
</html>