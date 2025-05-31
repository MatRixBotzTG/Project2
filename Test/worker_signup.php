<?php
session_start(); // Start the session to handle messages
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Worker Signup | Job Board</title>
    <meta name="description" content="Worker signup page for the job board.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/jobfavicon.png">

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
    /* Google Fonts Import for a modern feel (Poppins, Roboto, or a similar sans-serif) */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

       body {
            background-image: url('assets/img/hback.jpg');
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            background-color: #f0f2f7;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }


       .overlay {
            position: absolute;
            top: 0;
            left: 0;
            background-color: transparent;

            z-index: 1;
        }

        .signup-container {
            /* Corrected from 'left' to 'relative' as 'position: left' is invalid CSS.
            'justify-content: center' already centers it horizontally. */
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            justify-content: center; /* Keeps content centered horizontally */
            align-items: center;
            padding: 20px 0; /* Add some vertical padding for smaller screens */
            color: #000000; /* Set default text color for container to black */
        }

        .signup-box {
            /* Background from your provided style */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px); /* Frosted glass effect */
            padding: 45px 35px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            max-width: 600px; /* Increased max-width to accommodate more fields better */
            width: 100%;
            animation: fadeIn 1s ease;
            color: #000000; /* Main text color of the box set to black */
            margin: 20px; /* Add margin on smaller screens */
        }

        .signup-box h2 {
            text-align: center;
            margin-bottom: 10px;
            font-weight: 700;
            color: #000000; /* Heading text color set to black */
            font-size: 2.5em; /* Adjust heading size */
        }

        .signup-box p {
            text-align: center;
            font-size: 16px; /* Slightly larger paragraph text */
            color: #000000; /* Paragraph text color set to black */
            margin-bottom: 30px; /* More space below sub-heading */
        }

        .signup-box .form-control {
            padding: 12px 18px; /* Added horizontal padding */
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.15); /* Slightly more transparent input */
            color: #000000; /* Input text color set to black */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .signup-box .form-control:not(textarea) {
            height: 50px; /* Consistent height for text inputs */
            border: 0.5px solid #0303035b;
        }

        .signup-box .form-control::placeholder {
            color: #555; /* Darker placeholder for better contrast with black text */
        }

        .signup-box .form-control:focus {
            border: px solid #000000; /* Focus border matches button color */
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.4); /* Glow effect on focus */
            background-color: rgba(255, 255, 255, 0.25); /* Slightly less transparent on focus */
        }

        .signup-box textarea.form-control {
            min-height: 100px; /* Set a minimum height for textareas */
            resize: vertical; /* Allow vertical resizing */
            border: 0.5px solid #0303035b;
        }

        /* No specific .form-label as we're relying on placeholders more now */

        .signup-box button {
            background-color: #00bcd4; /* Primary button color */
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 12px;
            font-weight: 600;
            color: #fff; /* Button text kept white for contrast */
            transition: 0.3s ease;
            font-size: 1.1em; /* Slightly larger button text */
        }

        .signup-box button:hover {
            background-color: #0097a7; /* Darker on hover */
            transform: translateY(-2px); /* Slight lift */
            box-shadow: 0 4px 15px rgba(0, 188, 212, 0.4); /* Subtle shadow on hover */
        }

        .form-controll {
            padding: 5px;
            margin-right: 8px;
        }

        .signup-box .form-check-label {
            font-size: 14px;
            color: #000000; /* Checkbox label color set to black */
        }
        .signup-box .form-check-label {
            font-size: 14px;
            color: #000000; /* Checkbox label color set to black */
        }

        .signup-box .form-check-label a {
            color: #00bcd4; /* Set the 'Terms of Service' link color to blue */
            text-decoration: none; /* Keep it consistent with other links */
            font-weight: 600; /* Keep it bold if desired */
            transition: color 0.3s ease;
        }

        .signup-box .form-check-label a:hover {
            color: #0097a7; /* Darker blue on hover */
            text-decoration: underline;
        }

        .signup-box .text-link {
            text-align: center;
            margin-top: 25px;
            font-size: 16px;
            color: #000000; /* Link wrapper text color set to black */
        }

        .signup-box .text-link a {
            color: #0097a7; /* Link color matches button */
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-box .text-link a:hover {
            color: #0097a7;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .signup-box {
                padding: 35px 25px;
                max-width: 90%; /* Adjust max-width for tablets */
            }
            .signup-box h2 {
                font-size: 2em;
            }
            .signup-box p {
                font-size: 15px;
            }
        }

        @media (max-width: 576px) {
            .signup-box {
                padding: 25px 18px;
                max-width: 95%; /* Even wider on small mobiles */
            }
            .signup-box h2 {
                font-size: 1.8em;
            }
            .signup-box .form-control {
                padding: 10px 15px;
                margin-bottom: 15px;
            }
            .signup-box button {
                padding: 10px;
                font-size: 1em;
            }
            .signup-box .text-link {
                margin-top: 20px;
            }
        }
</style>
</head>
<body style="background-image: url('assets/img/hback.jpg'); background-size: cover; background-position: center center; background-attachment: fixed; background-color: #f0f2f7; min-height: 100vh; margin: 0; padding: 0; font-family: 'Poppins', sans-serif;">
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/joblogo (1) (1).png" alt="Job Board Logo">
                </div>
            </div>
        </div>
    </div>
    <header>
        <div class="header-area header-transparrent">
            <div class="headder-top header-sticky">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-2">
                            <div class="logo">
                                <a href="index.html"><img src="assets/img/joblogotr.png" style="height: 60px; width: 200px;" alt="Job Board Logo"></a>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9">
                            <div class="menu-wrapper">
                                <div class="main-menu">
                                    <nav class="d-none d-lg-block">
                                        <ul id="navigation">
                                            <li><a href="index.html">Home</a></li>
                                            <li><a href="job_listing.html">Find a Jobs </a></li>
                                            <li><a href="about.html">About</a></li>
                                            <li><a href="#">Page</a>
                                                <ul class="submenu">
                                                    <li><a href="blog.html">Blog</a></li>
                                                    <li><a href="single-blog.html">Blog Details</a></li>
                                                    <li><a href="elements.html">Elements</a></li>
                                                    <li><a href="job_details.html">job Details</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="contact.html">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <div class="header-btn d-none f-right d-lg-block">
                                    <a href="#" class="btn head-btn1">Register</a>
                                    <a href="#" class="btn head-btn2">Login</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </header>
    <main>
        <section class="signup-section">
            <div class="container">
                <div class="signup-container">
                    <body>
    <div class="overlay"></div>

    <div class="signup-container">
        <div class="signup-box">
            <h2>Worker Sign Up</h2>
            <p>Create your profile and start finding jobs!</p>

            <?php
            // Display error messages
            if (isset($_SESSION['error_message_worker'])) {
                echo '<div class="alert alert-danger text-center" role="alert">' . htmlspecialchars($_SESSION['error_message_worker']) . '</div>';
                unset($_SESSION['error_message_worker']); // Clear the message after displaying
            }

            // Display success messages
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success text-center" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']); // Clear the message after displaying
            }
            ?>

            <form action="php/worker_register_process.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession/Desired Role">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <input type="number" class="form-control" id="age" name="age" min="16" max="100" placeholder="Your Age" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="d-block mb-2" style="color: #000000;">Gender:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" required>
                                <label class="form-check-label" for="genderMale" style="color: #000000;">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female">
                                <label class="form-check-label" for="genderFemale" style="color: #000000;">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other">
                                <label class="form-check-label" for="genderOther" style="color: #000000;">Other</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control nice-select" id="country" name="country" required>
                            <option value="">Select Country</option>
                            </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control nice-select" id="state" name="state" required disabled>
                            <option value="">Select State</option>
                            </select>
                    </div>
                </div>

                <input type="number" class="form-control" id="experience" name="experience" min="0" placeholder="Years of Experience ">

                <textarea class="form-control" id="skills" name="skills" rows="4" placeholder="Key Skills"></textarea>
                <p style="position: relative;">Upload CV/Resume</p>
                <input type="file" class="form-controll" id="cv_upload" name="cv_upload" accept=".pdf,.doc,.docx" placeholder="Upload Your CV/Resume (PDF, DOCX only)">


                <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="termsCheck" required>
                <label class="form-check-label" for="termsCheck">I agree to the <a href="#"
                        style="font-size: 14px;">Terms of Service</a></label>
                </div>
                <button type="submit">Register Now</button>
            </form>

            <div class="text-link mt-4">
                <p>Already have an account? <a href="slogin.php">Login</a></p>
            </div>
        </div>

                </div>
            </div>
        </section>
        </main>

    <footer>
        <div class="footer-area footer-bg footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                       <div class="single-footer-caption mb-50">
                           <div class="single-footer-caption mb-30">
                               <div class="footer-tittle">
                                   <h4>About Us</h4>
                                   <div class="footer-pera">
                                       <p>Heaven frucvitful doesn't cover lesser dvsays appear creeping seasons so behold.</p>
                                   </div>
                               </div>
                           </div>
                       </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Contact Info</h4>
                                <ul>
                                    <li>
                                    <p>Address :Your address goes here, your demo address.</p>
                                    </li>
                                    <li><a href="#">Phone : +8880 44338899</a></li>
                                    <li><a href="#">Email : info@colorlib.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        </div>
                </div>
            </div>
        </div>
        </footer>

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

    <script>
        const countryStateData = {
            "India": ["Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"],
            "Afghanistan": ["Badakhshan", "Badghis", "Baghlan", "Balkh", "Bamyan", "Daykundi", "Farah", "Faryab", "Ghazni", "Ghor", "Helmand", "Herat", "Jowzjan", "Kabul", "Kandahar", "Kapisa", "Khost", "Kunar", "Kunduz", "Laghman", "Logar", "Nangarhar", "Nimroz", "Nuristan", "Paktia", "Paktika", "Panjshir", "Parwan", "Samangan", "Sar-e Pol", "Takhar", "Urozgan", "Wardak", "Zabul"],
            "United States": ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"],
            "Canada": ["Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Nova Scotia", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan"],
            "Australia": ["New South Wales", "Victoria", "Queensland", "Western Australia", "South Australia", "Tasmania", "Northern Territory", "Australian Capital Territory"]
        };

        $(document).ready(function() {
            const countrySelect = $('#country');
            const stateSelect = $('#state');

            // Populate countries on page load
            for (const country in countryStateData) {
                countrySelect.append($('<option>', {
                    value: country,
                    text: country
                }));
            }
            countrySelect.niceSelect('update'); // Update nice-select plugin display

            // Event listener for country change
            countrySelect.on('change', function() {
                const selectedCountry = $(this).val();
                stateSelect.empty(); // Clear previous states
                stateSelect.append($('<option>', {
                    value: '',
                    text: 'Select State'
                })); // Add default option

                if (selectedCountry && countryStateData[selectedCountry]) {
                    countryStateData[selectedCountry].forEach(function(state) {
                        stateSelect.append($('<option>', {
                            value: state,
                            text: state
                        }));
                    });
                    stateSelect.prop('disabled', false); // Enable state select
                } else {
                    stateSelect.prop('disabled', true); // Disable if no country selected or data missing
                }
                stateSelect.niceSelect('update'); // Update nice-select plugin display
            });
        });
    </script>

</body>
                </html>
