<?php
session_start();

// Check if a worker or company is logged in
$is_worker_logged_in = isset($_SESSION['worker_logged_in']) && $_SESSION['worker_logged_in'] === true;
$is_company_logged_in = isset($_SESSION['company_logged_in']) && $_SESSION['company_logged_in'] === true;

// Redirect to index if not logged in
if (!$is_worker_logged_in && !$is_company_logged_in) {
    header("Location: index.html");
    exit();
}

$user_type = '';
$user_name = '';
$user_email = '';

if ($is_worker_logged_in) {
    $user_type = 'Worker';
    $user_name = $_SESSION['worker_name'];
    $user_email = $_SESSION['worker_email'];
} elseif ($is_company_logged_in) {
    $user_type = 'Company';
    $user_name = $_SESSION['company_name'];
    $user_email = $_SESSION['company_email'];
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Home - Job Board Platform</title>
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
            max-width: 900px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .dashboard-section h3 {
            color: #007bff;
            margin-bottom: 15px;
        }
        .dashboard-section ul {
            list-style: none;
            padding: 0;
        }
        .dashboard-section ul li {
            background-color: #e9ecef;
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dashboard-section ul li strong {
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-area header-transparrent">
            <div class="headder-top header-sticky">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-2">
                            <div class="logo">
                                <a href="index.html"><img src="assets/img/logo/logo.png" alt=""></a>
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
                                    <span style="color: white; margin-right: 15px;">Logged in as: <?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($user_type); ?>)</span>
                                    <a href="php/logout.php" class="btn head-btn2">Logout</a>
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
        <div class="dashboard-container">
            <h2>Hello, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p>This is your personalized home page. Here you can find relevant information and actions based on your account type.</p>

            <?php if ($is_worker_logged_in): ?>
                <div class="dashboard-section">
                    <h3>Worker Dashboard</h3>
                    <p>As a worker, you can:</p>
                    <ul>
                        <li>Browse available jobs (feature to be implemented)</li>
                        <li>Update your profile (feature to be implemented)</li>
                        <li>View your application status (feature to be implemented)</li>
                    </ul>
                    <p>Your Email: <strong><?php echo htmlspecialchars($user_email); ?></strong></p>
                </div>
            <?php elseif ($is_company_logged_in): ?>
                <div class="dashboard-section">
                    <h3>Company Dashboard</h3>
                    <p>As a company, you can:</p>
                    <ul>
                        <li>Post new jobs (feature to be implemented)</li>
                        <li>Manage your posted jobs (feature to be implemented)</li>
                        <li>View applications from workers (feature to be implemented)</li>
                        <li>Update company profile (feature to be implemented)</li>
                    </ul>
                    <p>Your Company Email: <strong><?php echo htmlspecialchars($user_email); ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="dashboard-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Support</a> (Link to support page)</li>
                    <li><a href="#">FAQ</a> (Link to FAQ page)</li>
                    <li><a href="#">Contact Us</a> (Link to contact page)</li>
                </ul>
            </div>
        </div>
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
                                        <p>The Job Board Platform is dedicated to connecting talented individuals with leading companies, fostering career growth and efficient recruitment.</p>
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
                                        <p>Address : Your Company Address, 1234 Main St, City, Country</p>
                                    </li>
                                    <li><a href="#">Phone : +8880 4433 123</a></li>
                                    <li><a href="#">Email : info@jobboard.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Important Link</h4>
                                <ul>
                                    <li><a href="#"> View Project</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                    <li><a href="#">Testimonial</a></li>
                                    <li><a href="#">Proparties</a></li>
                                    <li><a href="#">Support</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Newsletter</h4>
                                <div class="footer-pera footer-pera2">
                                    <p>Heaven fruitful doesn't over les idays appear only.</p>
                                </div>
                                <div class="footer-form" >
                                    <div id="mc_embed_signup">
                                        <form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880bb9d963a7638c92&amp;id=d20d2b4ef3"
                                        method="get" class="subscribe_form relative mail_part">
                                            <input type="email" name="email" id="newsletter-form-email" placeholder="Email Address"
                                            class="placeholder-no-fix">
                                            <div class="form-icon">
                                                <button type="submit" name="submit" id="newsletter-submit"
                                                class="email_icon newsletter-submit button-contactForm"><img src="assets/img/icon/form.png" alt=""></button>
                                            </div>
                                            <div class="mt-10 info"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row footer-bottom d-flex justify-content-between align-items-center">
                    <div class="col-xl-9 col-lg-9">
                        <div class="footer-copy-right">
                            <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3">
                        <div class="footer-social f-right">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-globe"></i></a>
                            <a href="#"><i class="fab fa-behance"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </footer>

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
