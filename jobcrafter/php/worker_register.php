<?php
session_start(); // Start session to allow messages to be carried across redirects
require_once 'db_connect.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data and sanitize it
    $name = trim($_POST['name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = trim($_POST['gender'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';
    $reenter_password = $_POST['reenter_password'] ?? '';
    $country = trim($_POST['country'] ?? '');
    $state = trim($_POST['state'] ?? '');

    $errors = [];

    // 2. Server-side Validation
    if (empty($name) || empty($age) || empty($gender) || empty($mobile) || empty($mail) || empty($password) || empty($reenter_password) || empty($country) || empty($state)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $reenter_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 8) { // Minimum password length
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Basic mobile number validation (10 digits)
    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must be a 10-digit number.";
    }

    // Age validation
    if ($age < 16 || $age > 99) { // Example age range
        $errors[] = "Age must be between 16 and 99.";
    }

    // 3. Check for existing email (prevent duplicate registrations)
    if (empty($errors)) { // Only check if no other errors exist
        $stmt = $conn->prepare("SELECT id FROM workers WHERE email = ?");
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered. Please use a different email or login.";
        }
        $stmt->close();
    }

    // 4. If no errors, proceed with database insertion
    if (empty($errors)) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL INSERT statement
        $stmt = $conn->prepare("INSERT INTO workers (name, age, gender, mobile, email, password, country, state, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sissssss", $name, $age, $gender, $mobile, $mail, $hashed_password, $country, $state);

        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['success_message'] = "Registration successful! You can now login.";
            header("Location: ../worker_login.html"); // Redirect to worker login page
            exit();
        } else {
            // Database insertion failed
            $errors[] = "Error during registration. Please try again. (" . $stmt->error . ")";
            $_SESSION['error_messages'] = $errors; // Store errors in session
            header("Location: ../worker_signup.html"); // Redirect back to signup
            exit();
        }
        $stmt->close();
    } else {
        // Validation errors occurred, redirect back to signup page with errors
        $_SESSION['error_messages'] = $errors; // Store errors in session
        header("Location: ../worker_signup.html");
        exit();
    }

    $conn->close(); // Close database connection
} else {
    // If accessed directly without POST request, redirect to signup form
    header("Location: ../worker_signup.html");
    exit();
}
?>
