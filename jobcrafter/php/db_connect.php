<?php
// php/db_connect.php

// Database connection details
$servername = "localhost"; // Usually 'localhost' for local development
$username = "root";        // Your MySQL username (e.g., 'root' for XAMPP/WAMP)
$password = "";            // Your MySQL password (often empty for local XAMPP/WAMP)
$dbname = "jobcrafter"; // **IMPORTANT: Replace with your actual database name**

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to UTF-8 for proper handling of special characters
$conn->set_charset("utf8mb4");
?>
