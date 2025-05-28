<?php
session_start(); // Start session for admin login state
require_once 'var.php'; // Include the file with admin credentials (WARNING: for demo only)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $error_message = "";

    // 2. Authenticate against the stored admin credentials from var.php
    // In a real application, you'd fetch the hashed password from a database 'admins' table
    if ($username === $admin_username && password_verify($password, $admin_password_hash)) {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        // Redirect to the admin control panel
        header("Location: ../admin_panel.php");
        exit();
    } else {
        // Invalid credentials
        $error_message = "Invalid username or password.";
        $_SESSION['error_message_admin'] = $error_message; // Store error in session
        header("Location: ../admin_login.html"); // Redirect back to admin login
        exit();
    }
} else {
    // If accessed directly, redirect to admin login page
    header("Location: ../admin_login.html");
    exit();
}
?>
