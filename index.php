<?php
session_start();
// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Portfolio Generator</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Welcome to Portfolio Generator</h2>
    <p>Create and manage your professional portfolio with ease.</p>
    <p>
        <a href="signin.php">Sign In</a> | 
        <a href="signup.php">Sign Up</a>
    </p>
</body>
</html>