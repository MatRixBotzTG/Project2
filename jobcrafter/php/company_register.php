<?php
session_start(); // Start session to carry messages
require_once 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data and sanitize
    $company_name = trim($_POST['company_name'] ?? '');
    $company_mail = trim($_POST['company_mail'] ?? '');
    $company_number = trim($_POST['company_number'] ?? '');
    $company_country = trim($_POST['company_country'] ?? '');
    $company_state = trim($_POST['company_state'] ?? '');
    $location_lat = trim($_POST['location_lat'] ?? '');
    $location_lon = trim($_POST['location_lon'] ?? '');
    $location_address = trim($_POST['location_address'] ?? '');
    $password = $_POST['company_password'] ?? '';
    $reenter_password = $_POST['reenter_company_password'] ?? '';

    $errors = [];

    // 2. Server-side Validation
    if (empty($company_name) || empty($company_mail) || empty($company_number) || empty($company_country) || empty($company_state) || empty($password) || empty($reenter_password) || empty($location_lat) || empty($location_lon) || empty($location_address)) {
        $errors[] = "All fields are required, including selecting a company location.";
    }

    if (!filter_var($company_mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $reenter_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (!preg_match("/^[0-9]{10}$/", $company_number)) {
        $errors[] = "Company phone number must be a 10-digit number.";
    }

    // Validate geographic coordinates (basic check if they are numeric)
    if (!is_numeric($location_lat) || !is_numeric($location_lon)) {
        $errors[] = "Invalid geographic coordinates.";
    }

    // 3. Check for existing company email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM companies WHERE email = ?");
        $stmt->bind_param("s", $company_mail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This company email is already registered. Please use a different email or login.";
        }
        $stmt->close();
    }

    // 4. If no errors, proceed with database insertion
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $status = 'pending'; // New companies are always pending approval

        // Prepare and execute SQL INSERT statement
        $stmt = $conn->prepare("INSERT INTO companies (name, email, phone_number, country, state, location_lat, location_lon, location_address, password, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        // 's' for string, 'i' for integer, 'd' for double (for lat/lon)
        $stmt->bind_param("ssisssdss", $company_name, $company_mail, $company_number, $company_country, $company_state, $location_lat, $location_lon, $location_address, $hashed_password, $status);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Company registration request submitted! It will be reviewed by an admin for approval.";
            header("Location: ../company_login.html"); // Redirect to company login page
            exit();
        } else {
            $errors[] = "Error during company registration. Please try again. (" . $stmt->error . ")";
            $_SESSION['error_messages'] = $errors;
            header("Location: ../company_signup.html");
            exit();
        }
        $stmt->close();
    } else {
        // Validation errors, redirect back to signup page
        $_SESSION['error_messages'] = $errors;
        header("Location: ../company_signup.html");
        exit();
    }

    $conn->close();
} else {
    // If accessed directly, redirect to signup form
    header("Location: ../company_signup.html");
    exit();
}
?>
