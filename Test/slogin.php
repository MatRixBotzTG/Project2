<?php
session_start();
require_once 'php/db_connect.php'; // Include your database connection

// Check if worker is logged in, otherwise redirect to login page
if (!isset($_SESSION['worker_logged_in']) || $_SESSION['worker_logged_in'] !== true) {
    $_SESSION['error_message_worker'] = "Access denied. Please login.";
    header("Location: slogin.php"); // Redirect to the login page (slogin.php itself or worker_login.html)
    exit();
}

$worker_id = $_SESSION['worker_id'];
$worker_data = [];

// Fetch worker's profile data
$stmt = $conn->prepare("SELECT name, age, gender, mobile, email, country, state, profession, experience, skills, profile_picture_path, cv_path FROM workers WHERE id = ?");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $worker_data = $result->fetch_assoc();
} else {
    // Handle case where worker data is not found (shouldn't happen if logged in)
    $_SESSION['error_message_worker'] = "Worker profile not found.";
    header("Location: slogin.php"); // Redirect to login
    exit();
}
$stmt->close();

// Fetch all companies for the map (initial load)
$companies_for_map = [];
$sql_companies = "SELECT id, name, location_lat, location_lon, location_address FROM companies WHERE status = 'approved'";
$result_companies = $conn->query($sql_companies);
if ($result_companies && $result_companies->num_rows > 0) {
    while ($row = $result_companies->fetch_assoc()) {
        $companies_for_map[] = $row;
    }
}
$conn->close(); // Close DB connection after fetching all necessary data

?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Worker Dashboard | JobCrafter</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/jobfavicon.png">

    <!-- CSS here -->
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
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        /* Custom styles for the dashboard */
        .dashboard-container {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            color: #00bcd4;
            font-weight: 700;
        }
        .profile-section, .job-section, .company-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-pic-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #00bcd4;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-info h3 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        .profile-info p {
            margin: 5px 0 0;
            color: #555;
            font-size: 0.95em;
        }
        .profile-details p {
            margin-bottom: 10px;
            font-size: 1.05em;
            color: #444;
        }
        .profile-details strong {
            color: #000;
        }
        .btn-custom {
            background-color: #00bcd4;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .btn-custom:hover {
            background-color: #0097a7;
            transform: translateY(-2px);
        }
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: #00bcd4;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 15px 20px;
        }
        .modal-header .close {
            color: white;
            opacity: 1;
        }
        .modal-body .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }
        .modal-body .form-group label {
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
        }
        .modal-body .form-check-label {
            color: #555;
        }
        #map {
            height: 450px;
            width: 100%;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .search-map-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allow wrapping on small screens */
        }
        .search-map-controls input, .search-map-controls button {
            flex-grow: 1;
            min-width: 150px; /* Ensure elements don't get too small */
        }
        .alert-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* Specific styles for file inputs */
        .file-input-group {
            margin-bottom: 15px;
        }
        .file-input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        .file-input-group input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fdfdfd;
            cursor: pointer;
        }
        .file-input-group input[type="file"]::file-selector-button {
            background-color: #00bcd4;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }
        .file-input-group input[type="file"]::file-selector-button:hover {
            background-color: #0097a7;
        }
    </style>
</head>
<body>
    <?php include 'header.html'; ?>

    <main>
        <div class="dashboard-container">
            <h2 class="dashboard-header">Welcome, <?php echo htmlspecialchars($worker_data['name']); ?>!</h2>

            <?php
            // Display session messages
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert-message alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message_worker'])) {
                echo '<div class="alert-message alert-danger">' . htmlspecialchars($_SESSION['error_message_worker']) . '</div>';
                unset($_SESSION['error_message_worker']);
            }
            ?>

            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-pic-container">
                        <img src="<?php echo !empty($worker_data['profile_picture_path']) ? htmlspecialchars($worker_data['profile_picture_path']) : 'assets/img/default_profile.png'; ?>" alt="Profile Picture" class="profile-pic">
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($worker_data['name']); ?></h3>
                        <p><?php echo htmlspecialchars($worker_data['profession']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($worker_data['email']); ?></p>
                    </div>
                </div>

                <div class="profile-details row">
                    <div class="col-md-6">
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($worker_data['age']); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($worker_data['gender']); ?></p>
                        <p><strong>Mobile:</strong> <?php echo htmlspecialchars($worker_data['mobile']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($worker_data['country']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($worker_data['state']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($worker_data['experience']); ?></p>
                        <p><strong>Skills:</strong> <?php echo nl2br(htmlspecialchars($worker_data['skills'])); ?></p>
                        <p><strong>CV:</strong>
                            <?php if (!empty($worker_data['cv_path'])): ?>
                                <a href="<?php echo htmlspecialchars($worker_data['cv_path']); ?>" target="_blank" class="text-info">View CV</a>
                            <?php else: ?>
                                Not uploaded
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <button class="btn-custom mt-3" data-toggle="modal" data-target="#editProfileModal">Edit Profile</button>
            </div>

            <!-- Job Search Section -->
            <div class="job-section">
                <h3 class="text-center mb-4" style="color: #00bcd4;">Find Your Next Opportunity</h3>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search for jobs (e.g., 'Software Engineer', 'Marketing Manager')" aria-label="Job search input">
                    <div class="input-group-append">
                        <button class="btn-custom" type="button">Search Jobs</button>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn-custom">Browse All Jobs</a>
                    <a href="#" class="btn-custom ml-3">View My Applications</a>
                </div>
            </div>

            <!-- Company Search & Map Section -->
            <div class="company-section">
                <h3 class="text-center mb-4" style="color: #00bcd4;">Explore Companies Near You</h3>
                <div class="search-map-controls">
                    <input type="text" class="form-control" id="companySearchInput" placeholder="Search company by location (e.g., 'New York', 'Bangalore')">
                    <button class="btn-custom" id="searchCompanyBtn">Search Company</button>
                    <button class="btn-custom" id="searchNearMeBtn">Search Near Me</button>
                </div>
                <div id="map"></div>
            </div>

        </div>
    </main>

    <?php include 'footer.html'; ?>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editProfileModalLabel">Edit Your Profile</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="editProfileForm" action="php/worker_profile_update.php" method="POST" enctype="multipart/form-data">
              <div class="modal-body">
                  <input type="hidden" name="worker_id" value="<?php echo htmlspecialchars($worker_id); ?>">

                  <div class="form-group">
                      <label for="edit_full_name">Full Name:</label>
                      <input type="text" class="form-control" id="edit_full_name" name="full_name" value="<?php echo htmlspecialchars($worker_data['name']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="edit_email">Email Address:</label>
                      <input type="email" class="form-control" id="edit_email" name="email" value="<?php echo htmlspecialchars($worker_data['email']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="edit_mobile">Phone Number:</label>
                      <input type="tel" class="form-control" id="edit_mobile" name="phone_number" value="<?php echo htmlspecialchars($worker_data['mobile']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="edit_profession">Profession/Desired Role:</label>
                      <input type="text" class="form-control" id="edit_profession" name="profession" value="<?php echo htmlspecialchars($worker_data['profession']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="edit_age">Age:</label>
                      <input type="number" class="form-control" id="edit_age" name="age" min="16" max="100" value="<?php echo htmlspecialchars($worker_data['age']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label class="d-block mb-2">Gender:</label>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="gender" id="edit_genderMale" value="Male" <?php echo ($worker_data['gender'] == 'Male') ? 'checked' : ''; ?> required>
                          <label class="form-check-label" for="edit_genderMale">Male</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="gender" id="edit_genderFemale" value="Female" <?php echo ($worker_data['gender'] == 'Female') ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="edit_genderFemale">Female</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="gender" id="edit_genderOther" value="Other" <?php echo ($worker_data['gender'] == 'Other') ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="edit_genderOther">Other</label>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="edit_country">Country:</label>
                              <select class="form-control nice-select" id="edit_country" name="country" required>
                                  <option value="">Select Country</option>
                                  <!-- Populated by JS -->
                              </select>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="edit_state">State:</label>
                              <select class="form-control nice-select" id="edit_state" name="state" required disabled>
                                  <option value="">Select State</option>
                                  <!-- Populated by JS -->
                              </select>
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="edit_experience">Years of Experience:</label>
                      <input type="number" class="form-control" id="edit_experience" name="experience" min="0" value="<?php echo htmlspecialchars($worker_data['experience']); ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="edit_skills">Key Skills:</label>
                      <textarea class="form-control" id="edit_skills" name="skills" rows="3" required><?php echo htmlspecialchars($worker_data['skills']); ?></textarea>
                  </div>

                  <div class="form-group">
                      <label for="edit_password">New Password (leave blank to keep current):</label>
                      <input type="password" class="form-control" id="edit_password" name="password" placeholder="Enter new password (min 8 chars)">
                  </div>
                  <div class="form-group">
                      <label for="edit_confirm_password">Confirm New Password:</label>
                      <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password" placeholder="Confirm new password">
                  </div>

                  <div class="file-input-group">
                      <label for="edit_profile_picture">Upload Profile Picture:</label>
                      <input type="file" id="edit_profile_picture" name="profile_picture" accept="image/*">
                      <?php if (!empty($worker_data['profile_picture_path'])): ?>
                          <small class="form-text text-muted">Current: <a href="<?php echo htmlspecialchars($worker_data['profile_picture_path']); ?>" target="_blank">View Current Picture</a></small>
                      <?php endif; ?>
                  </div>

                  <div class="file-input-group">
                      <label for="edit_cv_upload">Upload CV/Resume (PDF, DOCX):</label>
                      <input type="file" id="edit_cv_upload" name="cv_upload" accept=".pdf,.doc,.docx">
                      <?php if (!empty($worker_data['cv_path'])): ?>
                          <small class="form-text text-muted">Current: <a href="<?php echo htmlspecialchars($worker_data['cv_path']); ?>" target="_blank">View Current CV</a></small>
                      <?php endif; ?>
                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-custom">Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    <!-- End Edit Profile Modal -->


    <!-- JS here -->
    <script src="assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/animated.headline.js"></script>
    <script src="assets/js/jquery.magnific-popup.js"></script>
    <script src="assets/js/gijgo.min.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/jquery.sticky.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Data for Country/State dropdowns (same as signup page)
        const countryStateData = {
            "India": ["Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"],
            "Afghanistan": ["Badakhshan", "Badghis", "Baghlan", "Balkh", "Bamyan", "Daykundi", "Farah", "Faryab", "Ghazni", "Ghor", "Helmand", "Herat", "Jowzjan", "Kabul", "Kandahar", "Kapisa", "Khost", "Kunar", "Kunduz", "Laghman", "Logar", "Nangarhar", "Nimroz", "Nuristan", "Paktia", "Paktika", "Panjshir", "Parwan", "Samangan", "Sar-e Pol", "Takhar", "Urozgan", "Wardak", "Zabul"],
            "United States": ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"],
            "Canada": ["Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Nova Scotia", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan"],
            "Australia": ["New South Wales", "Victoria", "Queensland", "Western Australia", "South Australia", "Tasmania", "Northern Territory", "Australian Capital Territory"]
        };

        $(document).ready(function() {
            // Initialize nice-select for the edit modal dropdowns
            $('#edit_country').niceSelect();
            $('#edit_state').niceSelect();

            // Function to populate states dropdown (used for edit modal)
            function populateStates(countrySelectId, stateSelectId, selectedCountry, selectedState) {
                const countrySelect = $(countrySelectId);
                const stateSelect = $(stateSelectId);

                stateSelect.empty();
                stateSelect.append($('<option>', { value: '', text: 'Select State' }));

                if (selectedCountry && countryStateData[selectedCountry]) {
                    countryStateData[selectedCountry].forEach(function(state) {
                        stateSelect.append($('<option>', {
                            value: state,
                            text: state,
                            selected: (state === selectedState) // Pre-select if matches
                        }));
                    });
                    stateSelect.prop('disabled', false);
                } else {
                    stateSelect.prop('disabled', true);
                }
                stateSelect.niceSelect('update');
            }

            // Populate countries for edit modal on page load
            for (const country in countryStateData) {
                $('#edit_country').append($('<option>', {
                    value: country,
                    text: country,
                    selected: (country === '<?php echo htmlspecialchars($worker_data['country']); ?>') // Pre-select current country
                }));
            }
            $('#edit_country').niceSelect('update');

            // Trigger state population for current country on page load
            populateStates('#edit_country', '#edit_state', '<?php echo htmlspecialchars($worker_data['country']); ?>', '<?php echo htmlspecialchars($worker_data['state']); ?>');


            // Event listener for country change in edit modal
            $('#edit_country').on('change', function() {
                const selectedCountry = $(this).val();
                populateStates('#edit_country', '#edit_state', selectedCountry, ''); // Clear state selection on country change
            });


            // --- Leaflet Map Initialization ---
            var map = L.map('map').setView([20.5937, 78.9629], 5); // Default to India

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var companyMarkers = L.layerGroup().addTo(map); // Layer group for company markers

            // Function to add markers to the map
            function addCompanyMarkers(companies) {
                companyMarkers.clearLayers(); // Clear existing markers
                if (companies.length === 0) {
                    // Optionally display a message if no companies found
                    return;
                }

                let bounds = L.latLngBounds(); // To fit map to markers

                companies.forEach(function(company) {
                    if (company.location_lat && company.location_lon) {
                        const lat = parseFloat(company.location_lat);
                        const lon = parseFloat(company.location_lon);
                        if (!isNaN(lat) && !isNaN(lon)) {
                            const marker = L.marker([lat, lon])
                                .bindPopup(`<b>${company.name}</b><br>${company.location_address || 'Location not specified'}`);
                            companyMarkers.addLayer(marker);
                            bounds.extend([lat, lon]);
                        }
                    }
                });

                if (companyMarkers.getLayers().length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] }); // Fit map to all markers
                } else {
                    map.setView([20.5937, 78.9629], 5); // Reset to default view if no markers
                }
            }

            // Initial load of companies
            const initialCompanies = <?php echo json_encode($companies_for_map); ?>;
            addCompanyMarkers(initialCompanies);

            // Search Company by Location Input
            $('#searchCompanyBtn').on('click', function() {
                const searchLocation = $('#companySearchInput').val().trim();
                if (searchLocation) {
                    fetchCompanies(searchLocation);
                } else {
                    alert('Please enter a location to search.');
                }
            });

            // Search Near Me (Geolocation)
            $('#searchNearMeBtn').on('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        // Reverse geocode to get address for display/search
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                            .then(response => response.json())
                            .then(data => {
                                const address = data.display_name || 'Your current location';
                                $('#companySearchInput').val(address); // Set input for user clarity
                                fetchCompanies('', lat, lon); // Fetch companies near this lat/lon
                            })
                            .catch(error => {
                                console.error('Error getting address from coordinates:', error);
                                alert('Could not determine address from your location. Searching by coordinates.');
                                fetchCompanies('', lat, lon); // Fallback to just coordinates
                            });
                    }, function(error) {
                        console.error('Geolocation error:', error);
                        alert('Geolocation failed: ' + error.message);
                    });
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            });

            // AJAX function to fetch companies
            function fetchCompanies(locationQuery = '', lat = '', lon = '') {
                $.ajax({
                    url: 'php/get_companies.php',
                    method: 'GET',
                    data: {
                        location: locationQuery,
                        lat: lat,
                        lon: lon
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            addCompanyMarkers(response.companies);
                            if (response.companies.length === 0) {
                                alert('No companies found for the given criteria.');
                            }
                        } else {
                            alert('Error fetching companies: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        alert('An error occurred while fetching companies.');
                    }
                });
            }
        });
    </script>
</body>
</html>
