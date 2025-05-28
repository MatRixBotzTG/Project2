<?php
// php/var.php
// WARNING: This file is for demonstration purposes only, as requested.
// Storing sensitive data like admin credentials directly in a PHP file is INSECURE for production.
// In a real application, admin credentials should be hashed and stored in a database,
// or managed via secure environment variables.

// Default admin username
$admin_username = "admin";

// Default admin password (hashed) - Change "your_admin_password_here" to a strong password
$admin_password_hash = password_hash("your_admin_password_here", PASSWORD_DEFAULT);
// You can replace "your_admin_password_here" with your desired admin password,
// then regenerate this hash by running password_hash("your_new_password", PASSWORD_DEFAULT)
// in a temporary PHP script and copying the output.
?>
