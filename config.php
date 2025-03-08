<?php
$host = 'localhost';
$dbname = 'portfolio_generator';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (empty)

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>