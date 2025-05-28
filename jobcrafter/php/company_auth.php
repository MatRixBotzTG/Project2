<?php
session_start(); // Start session to manage login state
require_once 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize input
    $mail = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    // 2. Server-side Validation
    if (empty($mail) || empty($password)) {
        $errors[] = "Email and password are required.";
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // 3. Authenticate company if no immediate errors
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password, status FROM companies WHERE email = ?");
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $hashed_password, $status);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && password_verify($password, $hashed_password)) {
            // Check company status
            if ($status === 'approved') {
                // Login successful and approved
                $_SESSION['company_logged_in'] = true;
                $_SESSION['company_id'] = $id;
                $_SESSION['company_name'] = $name;
                $_SESSION['company_email'] = $mail;

                // Redirect to the company's home page
                header("Location: ../home.php"); // Changed to home.php
                exit();
            } elseif ($status === 'pending') {
                $_SESSION['warning_message_company'] = "Your company registration is pending admin approval. Please wait.";
                header("Location: ../company_login.html");
                exit();
            } elseif ($status === 'banned') {
                $_SESSION['error_message_company'] = "Your company account has been banned. Please contact administration.";
                header("Location: ../company_login.html");
                exit();
            } else {
                // Should not happen with proper status values in DB
                $_SESSION['error_message_company'] = "Your account status is unknown. Please contact support.";
                header("Location: ../company_login.html");
                exit();
            }
        } else {
            // Invalid credentials
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }

    // 4. If login failed or had errors, redirect back to login page with messages
    if (!empty($errors)) {
        $_SESSION['error_message_company'] = implode("<br>", $errors);
        header("Location: ../company_login.html");
        exit();
    }

    $conn->close();
} else {
    // If accessed directly, redirect to login page
    header("Location: ../company_login.html");
    exit();
}
?>
