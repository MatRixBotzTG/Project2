<?php
session_start();
require_once 'db_connect.php'; // Include database connection

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['admin_error'] = "Access denied. Please login as admin.";
    header("Location: ../admin_login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and validate general action parameters
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $type = trim($_POST['type'] ?? ''); // 'worker' or 'company'
    $action = trim($_POST['action'] ?? ''); // 'approve', 'reject', 'ban', 'unban', 'edit', 'delete'

    if ($id <= 0 || !in_array($type, ['worker', 'company']) || empty($action)) {
        $_SESSION['admin_error'] = "Invalid action parameters.";
        header("Location: ../admin_panel.php");
        exit();
    }

    $stmt = null;
    $success = false;
    $message = "";
    $error = "";

    try {
        $conn->begin_transaction(); // Start a database transaction for atomic operations

        switch ($action) {
            case 'approve':
                if ($type === 'company') {
                    $stmt = $conn->prepare("UPDATE companies SET status = 'approved' WHERE id = ? AND status = 'pending'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Company approved successfully.";
                    } else {
                        $error = "Failed to approve company or company already processed.";
                    }
                } else {
                    $error = "Invalid type for approval action.";
                }
                break;

            case 'reject':
                if ($type === 'company') {
                    // You might choose to set status to 'rejected' instead of deleting
                    $stmt = $conn->prepare("DELETE FROM companies WHERE id = ? AND status = 'pending'");
                    // Or: $stmt = $conn->prepare("UPDATE companies SET status = 'rejected' WHERE id = ? AND status = 'pending'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Company registration rejected.";
                    } else {
                        $error = "Failed to reject company or company already processed.";
                    }
                } else {
                    $error = "Invalid type for rejection action.";
                }
                break;

            case 'ban':
                if ($type === 'worker') {
                    $stmt = $conn->prepare("UPDATE workers SET status = 'banned' WHERE id = ? AND status = 'active'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Worker banned successfully.";
                    } else {
                        $error = "Failed to ban worker or worker already banned/inactive.";
                    }
                } elseif ($type === 'company') {
                    $stmt = $conn->prepare("UPDATE companies SET status = 'banned' WHERE id = ? AND status = 'approved'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Company banned successfully.";
                    } else {
                        $error = "Failed to ban company or company already banned/pending.";
                    }
                } else {
                    $error = "Invalid type for ban action.";
                }
                break;

            case 'unban':
                if ($type === 'worker') {
                    $stmt = $conn->prepare("UPDATE workers SET status = 'active' WHERE id = ? AND status = 'banned'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Worker unbanned successfully.";
                    } else {
                        $error = "Failed to unban worker or worker already active.";
                    }
                } elseif ($type === 'company') {
                    $stmt = $conn->prepare("UPDATE companies SET status = 'approved' WHERE id = ? AND status = 'banned'");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $success = true;
                        $message = "Company unbanned successfully.";
                    } else {
                        $error = "Failed to unban company or company already approved/pending.";
                    }
                } else {
                    $error = "Invalid type for unban action.";
                }
                break;

            case 'delete':
                if ($type === 'worker') {
                    $stmt = $conn->prepare("DELETE FROM workers WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $success = true;
                        $message = "Worker deleted permanently.";
                    } else {
                        $error = "Failed to delete worker.";
                    }
                } elseif ($type === 'company') {
                    $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $success = true;
                        $message = "Company deleted permanently.";
                    } else {
                        $error = "Failed to delete company.";
                    }
                } else {
                    $error = "Invalid type for delete action.";
                }
                break;

            case 'edit':
                if ($type === 'worker') {
                    // Collect and sanitize worker specific edit data
                    $name = trim($_POST['name'] ?? '');
                    $age = (int)($_POST['age'] ?? 0);
                    $gender = trim($_POST['gender'] ?? '');
                    $mobile = trim($_POST['mobile'] ?? '');
                    $email = trim($_POST['email'] ?? '');
                    $country = trim($_POST['country'] ?? '');
                    $state = trim($_POST['state'] ?? '');
                    $password = $_POST['password'] ?? ''; // Optional new password

                    // Basic validation for edit fields
                    if (empty($name) || empty($age) || empty($gender) || empty($mobile) || empty($email) || empty($country) || empty($state)) {
                        $error = "All worker fields must be filled for editing.";
                        break;
                    }
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error = "Invalid email format.";
                        break;
                    }
                    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
                        $error = "Mobile number must be a 10-digit number.";
                        break;
                    }

                    $sql = "UPDATE workers SET name = ?, age = ?, gender = ?, mobile = ?, email = ?, country = ?, state = ?";
                    $params = [$name, $age, $gender, $mobile, $email, $country, $state];
                    $types = "sisssss";

                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql .= ", password = ?";
                        $params[] = $hashed_password;
                        $types .= "s";
                    }
                    $sql .= " WHERE id = ?";
                    $params[] = $id;
                    $types .= "i";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$params); // PHP 5.6+ splat operator
                    if ($stmt->execute()) {
                        $success = true;
                        $message = "Worker data updated successfully.";
                    } else {
                        $error = "Failed to update worker data: " . $stmt->error;
                    }
                } elseif ($type === 'company') {
                    // Collect and sanitize company specific edit data
                    $name = trim($_POST['name'] ?? '');
                    $mail = trim($_POST['mail'] ?? '');
                    $number = trim($_POST['number'] ?? '');
                    $country = trim($_POST['country'] ?? '');
                    $state = trim($_POST['state'] ?? '');
                    $location_lat = trim($_POST['location_lat'] ?? '');
                    $location_lon = trim($_POST['location_lon'] ?? '');
                    $location_address = trim($_POST['location_address'] ?? '');
                    $password = $_POST['password'] ?? ''; // Optional new password

                    // Basic validation for edit fields
                    if (empty($name) || empty($mail) || empty($number) || empty($country) || empty($state) || empty($location_lat) || empty($location_lon) || empty($location_address)) {
                        $error = "All company fields must be filled for editing.";
                        break;
                    }
                    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                        $error = "Invalid email format.";
                        break;
                    }
                    if (!preg_match("/^[0-9]{10}$/", $number)) {
                        $error = "Phone number must be a 10-digit number.";
                        break;
                    }
                    if (!is_numeric($location_lat) || !is_numeric($location_lon)) {
                        $error = "Invalid geographic coordinates.";
                        break;
                    }


                    $sql = "UPDATE companies SET name = ?, email = ?, phone_number = ?, country = ?, state = ?, location_lat = ?, location_lon = ?, location_address = ?";
                    $params = [$name, $mail, $number, $country, $state, $location_lat, $location_lon, $location_address];
                    $types = "ssisddss";

                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql .= ", password = ?";
                        $params[] = $hashed_password;
                        $types .= "s";
                    }
                    $sql .= " WHERE id = ?";
                    $params[] = $id;
                    $types .= "i";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    if ($stmt->execute()) {
                        $success = true;
                        $message = "Company data updated successfully.";
                    } else {
                        $error = "Failed to update company data: " . $stmt->error;
                    }
                } else {
                    $error = "Invalid type for edit action.";
                }
                break;

            default:
                $error = "Unknown action.";
                break;
        }

        if ($success) {
            $conn->commit(); // Commit the transaction if all operations were successful
            $_SESSION['admin_message'] = $message;
        } else {
            $conn->rollback(); // Rollback the transaction if any operation failed
            if (empty($error)) { // Default error if not set by specific case
                $error = "An unexpected error occurred during the action.";
            }
            $_SESSION['admin_error'] = $error;
        }

    } catch (Exception $e) {
        $conn->rollback(); // Ensure rollback on exceptions
        $_SESSION['admin_error'] = "Database transaction error: " . $e->getMessage();
    } finally {
        if ($stmt) {
            $stmt->close(); // Close the statement
        }
        $conn->close(); // Close the database connection
    }

    // Redirect back to the admin panel after any action
    header("Location: ../admin_panel.php");
    exit();
} else {
    // If accessed directly without POST request, redirect to admin panel
    header("Location: ../admin_panel.php");
    exit();
}
?>
