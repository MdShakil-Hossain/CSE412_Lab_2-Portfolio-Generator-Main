<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$has_portfolio = $stmt->num_rows > 0;
if ($has_portfolio) {
    $stmt->bind_result($portfolio_id);
    $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Welcome</h2>
    <?php if ($has_portfolio): ?>
        <p><a href="portfolio_edit.php">Edit Portfolio</a></p>
        <p><a href="generate_pdf.php">Generate PDF</a></p>
    <?php else: ?>
        <p><a href="portfolio_edit.php">Create Portfolio</a></p>
    <?php endif; ?>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>