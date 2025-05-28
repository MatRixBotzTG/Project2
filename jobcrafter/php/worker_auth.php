<?php
session_start(); // Start session to manage login state
require_once 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize input
    $mail = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? ''; // Keep raw for password_verify()

    $errors = [];

    // 2. Server-side Validation
    if (empty($mail) || empty($password)) {
        $errors[] = "Email and password are required.";
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // 3. Authenticate user if no immediate errors
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password, status FROM workers WHERE email = ?");
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $hashed_password, $status);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && password_verify($password, $hashed_password)) {
            // Check worker status
            if ($status === 'active') {
                // Login successful and active
                $_SESSION['worker_logged_in'] = true;
                $_SESSION['worker_id'] = $id;
                $_SESSION['worker_name'] = $name;
                $_SESSION['worker_email'] = $mail; // Store email for possible future use

                // Redirect to the worker's home page
                header("Location: ../home.php"); // Changed to home.php
                exit();
            } else if ($status === 'banned') {
                $errors[] = "Your account has been banned. Please contact administration.";
            } else {
                $errors[] = "Your account status is not recognized. Please contact administration.";
            }
        } else {
            // Invalid credentials
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }

    // 4. If login failed or had errors, redirect back to login page with messages
    if (!empty($errors)) {
        $_SESSION['error_message_worker'] = implode("<br>", $errors);
        header("Location: ../worker_login.html");
        exit();
    }

    $conn->close();
} else {
    // If accessed directly, redirect to login page
    header("Location: ../worker_login.html");
    exit();
}
?>
