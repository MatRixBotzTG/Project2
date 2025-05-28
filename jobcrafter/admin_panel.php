<?php
session_start();
require_once 'php/db_connect.php'; // Correct path to db_connect.php

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['admin_error'] = "Access denied. Please login as admin.";
    header("Location: admin_login.html");
    exit();
}

// Fetch pending companies
$pending_companies = [];
$sql_pending = "SELECT id, name, email, phone_number, country, state, location_address, created_at FROM companies WHERE status = 'pending' ORDER BY created_at DESC";
$result_pending = $conn->query($sql_pending);
if ($result_pending && $result_pending->num_rows > 0) {
    while ($row = $result_pending->fetch_assoc()) {
        $pending_companies[] = $row;
    }
}

// Fetch all workers
$all_workers = [];
$sql_workers = "SELECT id, name, age, gender, mobile, email, country, state, status, created_at FROM workers ORDER BY created_at DESC";
$result_workers = $conn->query($sql_workers);
if ($result_workers && $result_workers->num_rows > 0) {
    while ($row = $result_workers->fetch_assoc()) {
        $all_workers[] = $row;
    }
}

// Fetch all companies (approved, banned, rejected)
$all_companies = [];
$sql_companies = "SELECT id, name, email, phone_number, country, state, location_address, status, created_at FROM companies ORDER BY created_at DESC";
$result_companies = $conn->query($sql_companies);
if ($result_companies && $result_companies->num_rows > 0) {
    while ($row = $result_companies->fetch_assoc()) {
        $all_companies[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel - Job Board Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/price_rangs.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #badbcc; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .section { margin-bottom: 40px; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background-color: #fcfcfc; }
        .section h3 { color: #007bff; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #e9ecef; color: #495057; }
        .actions button {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .approve-btn { background-color: #28a745; color: white; }
        .approve-btn:hover { background-color: #218838; }
        .reject-btn, .delete-btn { background-color: #dc3545; color: white; }
        .reject-btn:hover, .delete-btn:hover { background-color: #c82333; }
        .ban-btn { background-color: #ffc107; color: #333; }
        .ban-btn:hover { background-color: #e0a800; }
        .unban-btn { background-color: #17a2b8; color: white; }
        .unban-btn:hover { background-color: #138496; }
        .edit-btn { background-color: #6c757d; color: white; }
        .edit-btn:hover { background-color: #5a6268; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-form .form-group {
            margin-bottom: 15px;
        }
        .modal-form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .modal-form input[type="text"],
        .modal-form input[type="number"],
        .modal-form input[type="email"],
        .modal-form input[type="password"],
        .modal-form select {
            width: calc(100% - 22px); /* Account for padding and border */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .modal-form input[type="radio"] {
            margin-right: 5px;
        }
        .modal-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .modal-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'header.html'; ?>

    <main>
        <div class="dashboard-container">
            <h2>Manage Users and Companies</h2>

            <?php
            if (isset($_SESSION['admin_message'])) {
                echo '<div class="message success">' . htmlspecialchars($_SESSION['admin_message']) . '</div>';
                unset($_SESSION['admin_message']);
            }
            if (isset($_SESSION['admin_error'])) {
                echo '<div class="message error">' . htmlspecialchars($_SESSION['admin_error']) . '</div>';
                unset($_SESSION['admin_error']);
            }
            ?>

            <div class="section">
                <h3>Pending Company Registrations</h3>
                <?php if (empty($pending_companies)): ?>
                    <p>No pending company registrations.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_companies as $company): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($company['id']); ?></td>
                                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                                    <td><?php echo htmlspecialchars($company['email']); ?></td>
                                    <td><?php echo htmlspecialchars($company['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($company['location_address']); ?></td>
                                    <td><?php echo htmlspecialchars($company['created_at']); ?></td>
                                    <td class="actions">
                                        <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                            <input type="hidden" name="type" value="company">
                                            <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                                        </form>
                                        <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                            <input type="hidden" name="type" value="company">
                                            <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h3>All Workers</h3>
                <?php if (empty($all_workers)): ?>
                    <p>No workers registered yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_workers as $worker): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($worker['id']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['name']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['age']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['email']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['country']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['state']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['status']); ?></td>
                                    <td><?php echo htmlspecialchars($worker['created_at']); ?></td>
                                    <td class="actions">
                                        <button class="edit-btn" onclick="openEditModal('worker', <?php echo htmlspecialchars(json_encode($worker)); ?>)">Edit</button>
                                        <?php if ($worker['status'] === 'active'): ?>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($worker['id']); ?>">
                                                <input type="hidden" name="type" value="worker">
                                                <button type="submit" name="action" value="ban" class="ban-btn">Ban</button>
                                            </form>
                                        <?php elseif ($worker['status'] === 'banned'): ?>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($worker['id']); ?>">
                                                <input type="hidden" name="type" value="worker">
                                                <button type="submit" name="action" value="unban" class="unban-btn">Unban</button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="php/admin_actions.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this worker?');">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($worker['id']); ?>">
                                            <input type="hidden" name="type" value="worker">
                                            <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="section">
                <h3>All Companies</h3>
                <?php if (empty($all_companies)): ?>
                    <p>No companies registered yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_companies as $company): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($company['id']); ?></td>
                                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                                    <td><?php echo htmlspecialchars($company['email']); ?></td>
                                    <td><?php echo htmlspecialchars($company['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($company['location_address']); ?></td>
                                    <td><?php echo htmlspecialchars($company['status']); ?></td>
                                    <td><?php echo htmlspecialchars($company['created_at']); ?></td>
                                    <td class="actions">
                                        <button class="edit-btn" onclick="openEditModal('company', <?php echo htmlspecialchars(json_encode($company)); ?>)">Edit</button>
                                        <?php if ($company['status'] === 'approved'): ?>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                                <input type="hidden" name="type" value="company">
                                                <button type="submit" name="action" value="ban" class="ban-btn">Ban</button>
                                            </form>
                                        <?php elseif ($company['status'] === 'banned'): ?>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                                <input type="hidden" name="type" value="company">
                                                <button type="submit" name="action" value="unban" class="unban-btn">Unban</button>
                                            </form>
                                        <?php elseif ($company['status'] === 'pending'): ?>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                                <input type="hidden" name="type" value="company">
                                                <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                                            </form>
                                            <form action="php/admin_actions.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                                <input type="hidden" name="type" value="company">
                                                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="php/admin_actions.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this company?');">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                                            <input type="hidden" name="type" value="company">
                                            <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>

        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeEditModal()">&times;</span>
                <h3 id="modalTitle">Edit User</h3>
                <form id="editForm" class="modal-form" action="php/admin_actions.php" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_type" name="type" value="edit">
                    <input type="hidden" name="action" value="edit">

                    <div class="form-group">
                        <label for="edit_name">Name:</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email:</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>

                    <div id="workerFields" style="display:none;">
                        <div class="form-group">
                            <label for="edit_age">Age:</label>
                            <input type="number" id="edit_age" name="age" class="form-control" min="16" max="99">
                        </div>
                        <div class="form-group">
                            <label>Gender:</label><br>
                            <input type="radio" id="edit_male" name="gender" value="Male">
                            <label for="edit_male">Male</label>
                            <input type="radio" id="edit_female" name="gender" value="Female">
                            <label for="edit_female">Female</label>
                            <input type="radio" id="edit_other" name="gender" value="Other">
                            <label for="edit_other">Other</label>
                        </div>
                        <div class="form-group">
                            <label for="edit_mobile">Mobile Number:</label>
                            <input type="text" id="edit_mobile" name="mobile" class="form-control" pattern="[0-9]{10}" title="Please enter a 10-digit mobile number">
                        </div>
                        <div class="form-group">
                            <label for="edit_worker_country">Country:</label>
                            <input type="text" id="edit_worker_country" name="country" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_worker_state">State:</label>
                            <input type="text" id="edit_worker_state" name="state" class="form-control">
                        </div>
                    </div>

                    <div id="companyFields" style="display:none;">
                        <div class="form-group">
                            <label for="edit_number">Phone Number:</label>
                            <input type="text" id="edit_number" name="number" class="form-control" pattern="[0-9]{10}" title="Please enter a 10-digit phone number">
                        </div>
                        <div class="form-group">
                            <label for="edit_company_country">Country:</label>
                            <input type="text" id="edit_company_country" name="country" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_company_state">State:</label>
                            <input type="text" id="edit_company_state" name="state" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_location_lat">Location Latitude:</label>
                            <input type="text" id="edit_location_lat" name="location_lat" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_location_lon">Location Longitude:</label>
                            <input type="text" id="edit_location_lon" name="location_lon" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_location_address">Address:</label>
                            <input type="text" id="edit_location_address" name="location_address" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_password">New Password (leave blank to keep current):</label>
                        <input type="password" id="edit_password" name="password" class="form-control" minlength="8">
                    </div>

                    <button type="submit" class="btn btn-custom-submit">Save Changes</button>
                </form>
            </div>
        </div>

        <script>
            var editModal = document.getElementById("editModal");
            var span = document.getElementsByClassName("close-btn")[0];

            function openEditModal(type, data) {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_type').value = type;
                document.getElementById('modalTitle').innerText = 'Edit ' + type.charAt(0).toUpperCase() + type.slice(1);

                // Common fields
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_email').value = data.email;

                // Hide all type-specific fields initially
                document.getElementById('workerFields').style.display = 'none';
                document.getElementById('companyFields').style.display = 'none';

                if (type === 'worker') {
                    document.getElementById('workerFields').style.display = 'block';
                    document.getElementById('edit_age').value = data.age;
                    if (data.gender === 'Male') {
                        document.getElementById('edit_male').checked = true;
                    } else if (data.gender === 'Female') {
                        document.getElementById('edit_female').checked = true;
                    } else if (data.gender === 'Other') {
                        document.getElementById('edit_other').checked = true;
                    }
                    document.getElementById('edit_mobile').value = data.mobile;
                    document.getElementById('edit_worker_country').value = data.country;
                    document.getElementById('edit_worker_state').value = data.state;
                } else if (type === 'company') {
                    document.getElementById('companyFields').style.display = 'block';
                    document.getElementById('edit_number').value = data.phone_number;
                    document.getElementById('edit_company_country').value = data.country;
                    document.getElementById('edit_company_state').value = data.state;
                    document.getElementById('edit_location_lat').value = data.location_lat;
                    document.getElementById('edit_location_lon').value = data.location_lon;
                    document.getElementById('edit_location_address').value = data.location_address;
                }

                // Clear password field on opening
                document.getElementById('edit_password').value = '';

                editModal.style.display = "block";
            }

            function closeEditModal() {
                editModal.style.display = "none";
            }

            // Close the modal when the user clicks anywhere outside of the modal content
            window.onclick = function(event) {
                if (event.target == editModal) {
                    editModal.style.display = "none";
                }
            }
        </script>
    </main>

    <?php include 'footer.html'; ?>

    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <script src="./assets/js/price_rangs.js"></script>
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>
    <script src="./assets/js/jquery.scrollUp.min.js"></script>
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
    <script src="./assets/js/jquery.counterup.min.js"></script>
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
