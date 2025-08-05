<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export & Import Data - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome for icons in form -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

        :root {
            --poppins: 'Poppins', sans-serif;
            --lato: 'Lato', sans-serif;
            --light: #F9F9F9;
            --blue: #3C91E6;
            --light-blue: #CFE8FF;
            --grey: #eee;
            --dark-grey: #AAAAAA;
            --dark: #342E37;
            --red: #DB504A;
            --yellow: #FFCE26;
            --light-yellow: #FFF2C6;
            --orange: #FD7238;
            --light-orange: #FFE0D3;
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
        }

        html {
            overflow-x: hidden;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
            --dark-grey: #FFFFFF;
            --light-blue: #1A3C6D;
            --light-yellow: #4D3E00;
            --light-orange: #4D2C1A;
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
            font-family: var(--lato);
            color: var(--dark);
        }

        /* SIDEBAR */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: var(--light);
            z-index: 2000;
            transition: .3s ease;
            overflow-x: hidden;
            scrollbar-width: none;
        }

        #sidebar::-webkit-scrollbar {
            display: none;
        }

        #sidebar.hide {
            width: 60px;
        }

        #sidebar.hide .business-name,
        #sidebar.hide .made-by {
            display: none;
        }

        #sidebar .brand {
            font-size: 24px;
            height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            position: sticky;
            top: 0;
            left: 0;
            background: var(--light);
            z-index: 500;
            padding-bottom: 10px;
            box-sizing: content-box;
            text-align: center;
        }

        #sidebar .brand .business-name {
            font-weight: 700;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientAnimation 6s ease infinite;
        }

        #sidebar .brand .made-by {
            font-size: 10px;
            color: var(--dark-grey);
            margin-top: 4px;
        }

        #sidebar .brand .bx {
            min-width: 60px;
            display: flex;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 5px;
        }

        #sidebar .side-menu {
            width: 100%;
            margin-top: 20px;
        }

        #sidebar .side-menu li {
            height: 48px;
            background: transparent;
            margin-left: 6px;
            border-radius: 48px 0 0 48px;
            padding: 4px;
        }

        #sidebar .side-menu li.active {
            background: var(--grey);
            position: relative;
        }

        #sidebar .side-menu li.active::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: -40px;
            right: 0;
            box-shadow: 20px 20px 0 var(--grey);
            z-index: -1;
        }

        #sidebar .side-menu li.active::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            bottom: -40px;
            right: 0;
            box-shadow: 20px -20px 0 var(--grey);
            z-index: -1;
        }

        #sidebar .side-menu li a {
            width: 100%;
            height: 100%;
            background: var(--light);
            display: flex;
            align-items: center;
            border-radius: 48px;
            font-size: 16px;
            color: var(--dark);
            white-space: nowrap;
            overflow-x: hidden;
        }

        #sidebar .side-menu.top li.active a {
            color: var(--blue);
        }

        #sidebar.hide .side-menu li a {
            width: calc(48px - (4px * 2));
            transition: width .3s ease;
        }

        #sidebar .side-menu li a.logout {
            color: var(--red);
        }

        #sidebar .side-menu.top li a:hover {
            color: var(--blue);
        }

        #sidebar .side-menu li a .bx {
            min-width: calc(60px - ((4px + 6px) * 2));
            display: flex;
            justify-content: center;
            color: var(--dark);
        }

        /* CONTENT */
        #content {
            position: relative;
            width: calc(100% - 280px);
            left: 280px;
            transition: .3s ease;
        }

        #sidebar.hide~#content {
            width: calc(100% - 60px);
            left: 60px;
        }

        /* NAVBAR */
        #content nav {
            height: 70px;
            background: var(--light);
            padding: 0 24px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
            position: sticky;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        #content nav::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            bottom: -40px;
            left: 0;
            border-radius: 50%;
            box-shadow: -20px -20px 0 var(--light);
        }

        #content nav a {
            color: var(--dark);
        }

        #content nav .bx.bx-menu {
            cursor: pointer;
            color: var(--dark);
            font-size: 24px;
        }

        #content nav .nav-link {
            font-size: 16px;
            transition: .3s ease;
        }

        #content nav .nav-link:hover {
            color: var(--blue);
        }

        #content nav form {
            max-width: 400px;
            width: 100%;
            margin-right: auto;
        }

        #content nav form .form-input {
            display: flex;
            align-items: center;
            height: 36px;
        }

        #content nav form .form-input input {
            flex-grow: 1;
            padding: 0 16px;
            height: 100%;
            border: none;
            background: var(--grey);
            border-radius: 36px 0 0 36px;
            outline: none;
            width: 100%;
            color: var(--dark);
        }

        #content nav form .form-input button {
            width: 36px;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--blue);
            color: var(--light);
            font-size: 18px;
            border: none;
            outline: none;
            border-radius: 0 36px 36px 0;
            cursor: pointer;
        }

        #content nav .notification {
            font-size: 20px;
            position: relative;
        }

        #content nav .notification .num {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--light);
            background: var(--red);
            color: var(--light);
            font-weight: 700;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #content nav .fullscreen {
            font-size: 20px;
            cursor: pointer;
            color: var(--dark);
        }

        #content nav .fullscreen:hover {
            color: var(--blue);
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-btn img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--blue);
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            background: var(--light);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 220px;
            padding: 10px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1001;
            border: 1px solid var(--grey);
        }

        .profile-dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 10px 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:hover {
            background: var(--grey);
        }

        .dropdown-item i {
            font-size: 18px;
            color: var(--dark);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--grey);
            margin: 5px 0;
        }

        .upload-profile {
            display: none;
        }

        #content nav .switch-mode {
            display: block;
            min-width: 50px;
            height: 25px;
            border-radius: 25px;
            background: var(--grey);
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5px;
        }

        #content nav .switch-mode i {
            font-size: 16px;
            color: var(--dark);
        }

        #content nav .switch-mode .sun-icon {
            opacity: 1;
        }

        #content nav .switch-mode .moon-icon {
            opacity: 0;
        }

        #content nav #switch-mode:checked+.switch-mode .sun-icon {
            opacity: 0;
        }

        #content nav #switch-mode:checked+.switch-mode .moon-icon {
            opacity: 1;
        }

        #content nav .switch-mode::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            bottom: 2px;
            width: calc(25px - 4px);
            background: var(--blue);
            border-radius: 50%;
            transition: all .3s ease;
        }

        #content nav #switch-mode:checked+.switch-mode::before {
            left: calc(100% - (25px - 4px) - 2px);
        }

        /* MAIN CONTENT */
        main {
            width: 100%;
            padding: 36px 24px;
            font-family: var(--poppins);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }

        .head-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            grid-gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .head-title .left h1 {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            grid-gap: 16px;
        }

        .breadcrumb li {
            color: var(--dark);
        }

        .breadcrumb li a {
            color: var(--dark-grey);
            pointer-events: none;
        }

        .breadcrumb li a.active {
            color: var(--blue);
            pointer-events: unset;
        }

        /* Form Container */
        .form-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--grey);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-section h2 {
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group select,
        .form-group input[type="file"],
        .form-group button {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--grey);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--light);
            color: var(--dark);
        }

        .form-group select:focus,
        .form-group input[type="file"]:focus,
        .form-group button:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }

        .form-group button {
            background: var(--primary-color);
            color: var(--light);
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .form-group button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .form-group button:disabled {
            background: var(--dark-grey);
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            #sidebar {
                width: 200px;
            }

            #content {
                width: calc(100% - 60px);
                left: 200px;
            }

            #content nav .nav-link {
                display: none;
            }

            .head-title .left h1 {
                font-size: 28px;
            }
        }

        @media screen and (max-width: 576px) {
            #content nav form .form-input input {
                display: none;
            }

            #content nav form .form-input button {
                width: auto;
                height: auto;
                background: transparent;
                border-radius: none;
                color: var(--dark);
            }

            #content nav form.show .form-input input {
                display: block;
                width: 100%;
            }

            #content nav form.show .form-input button {
                width: 36px;
                height: 100%;
                border-radius: 0 36px 36px 0;
                color: var(--light);
                background: var(--blue);
            }

            #content nav form.show~.notification,
            #content nav form.show~.fullscreen,
            #content nav form.show~.profile {
                display: none;
            }

            main {
                padding: 24px 16px;
            }
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <div class="brand">
            <img src="../image/logo/icon2.png" height="20" alt="">
            <div class="business-name" id="businessName">SkyLink Main</div>
            <div class="made-by">Made by SkyLink</div>
        </div>
        <ul class="side-menu top">
            <li>
                <a href="clientDashboard.php">
                    <i class='bx bx-home-alt'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="addVacancy.php">
                    <i class='bx bx-briefcase'></i>
                    <span class="text">Add Job & Country</span>
                </a>
            </li>
            <li>
                <a href="viewVacancy.php">
                    <i class='bx bx-file'></i>
                    <span class="text">View Job & Country</span>
                </a>
            </li>
            <li>
                <a href="addCandidate.php">
                    <i class='bx bx-user-plus'></i>
                    <span class="text">Add Candidate</span>
                </a>
            </li>
            <li>
                <a href="candidateDatabase.php">
                    <i class='bx bx-data'></i>
                    <span class="text">Candidate Database</span>
                </a>
            </li>
            <li>
                <a href="addCities.php">
                    <i class='bx bx-file-blank'></i>
                    <span class="text">CV</span>
                </a>
            </li>
            <li>
                <a href="addCities.php">
                    <i class='bx bx-search-alt'></i>
                    <span class="text">Search CV</span>
                </a>
            </li>
            <li class="active">
                <a href="adminuserManagement.php">
                    <i class='bx bx-transfer'></i>
                    <span class="text">Export & Import</span>
                </a>
            </li>
            <li>
                <a href="addAdmin.php">
                    <i class='bx bx-user-plus'></i>
                    <span class="text">Add Admin</span>
                </a>
            </li>
            <li>
                <a href="addTask.php">
                    <i class='bx bx-money'></i>
                    <span class="text">Billing Payment</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class='bx bx-log-out'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode">
                <i class='bx bx-sun sun-icon'></i>
                <i class='bx bx-moon moon-icon'></i>
            </label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a>
            <a href="#" class="fullscreen">
                <i class='bx bx-fullscreen'></i>
            </a>
            <div class="profile-dropdown">
                <div class="profile-btn">
                    <img src="../image/logo/Management.png" id="profileImage">
                </div>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item">
                        <i class='bx bxs-cloud-upload'></i>
                        <span>Upload Profile</span>
                    </a>
                    <input type="file" id="uploadProfile" class="upload-profile" accept="image/*">
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class='bx bxs-user'></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class='bx bxs-cog'></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class='bx bxs-log-out-circle'></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN CONTENT -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Export & Import Data</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Export & Import</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-container">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        Error: <?php echo htmlspecialchars($_GET['error']); ?>
                        <?php if (isset($_GET['message'])): ?>
                            <br><?php echo htmlspecialchars($_GET['message']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        Success: <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>

                <div class="form-section">
                    <h2><i class="fas fa-download me-2"></i>Export Database</h2>
                    <form id="exportForm" action="export_import_process.php" method="post">
                        <input type="hidden" name="action" value="export">
                        <div class="form-group">
                            <label for="export_data">Select Data to Export</label>
                            <select name="export_data" id="export_data" required>
                                <option value="all">All Data</option>
                                <option value="candidates">Candidates</option>
                                <option value="vacancies">Vacancies</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="export_format">Export Format</label>
                            <select name="export_format" id="export_format" required>
                                <option value="csv">CSV</option>
                                <option value="sql">SQL</option>
                                <option value="json">JSON</option>
                                <option value="zip">ZIP (with files)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit"><i class="fas fa-file-export me-2"></i>Export Data</button>
                        </div>
                    </form>
                </div>

                <div class="form-section">
                    <h2><i class="fas fa-upload me-2"></i>Import Database</h2>
                    <form id="importForm" action="export_import_process.php" method="post"
                        enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import">
                        <div class="form-group">
                            <label for="import_file">Upload File</label>
                            <input type="file" name="import_file" id="import_file" accept=".csv,.sql,.json,.zip"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="import_data_type">Data Type</label>
                            <select name="import_data_type" id="import_data_type" required>
                                <option value="candidates">Candidates</option>
                                <option value="vacancies">Vacancies</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit"><i class="fas fa-file-import me-2"></i>Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

    </section>

    <script> // Toggle sidebar
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');

        menuBar.addEventListener('click', function () {
            sidebar.classList.toggle('hide');
        });

        // Profile dropdown
        const profileDropdown = document.getElementById('profileDropdown');

        profileDropdown.addEventListener('click', function (e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            profileDropdown.classList.remove('active');
        });

        // Theme switcher
        const switchMode = document.getElementById('switch-mode');
        switchMode.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });

        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            switchMode.checked = true;
        }

        // Search functionality for mobile
        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', function (e) {
            if (window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                if (searchForm.classList.contains('show')) {
                    searchButtonIcon.classList.replace('bx-search', 'bx-x');
                } else {
                    searchButtonIcon.classList.replace('bx-x', 'bx-search');
                }
            }
        });

        // Responsive adjustments
        if (window.innerWidth < 768) {
            sidebar.classList.add('hide');
        }

        window.addEventListener('resize', function () {
            if (this.innerWidth > 576) {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
            }
        });

        // Profile image upload
        const uploadProfile = document.getElementById('uploadProfile');
        const profileImage = document.getElementById('profileImage');

        uploadProfile.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    profileImage.src = event.target.result;
                    // Here you would typically upload the image to your server
                };
                reader.readAsDataURL(file);
            }
        });

        // Fetch business name and update dashboard
        document.addEventListener('DOMContentLoaded', function () {
            fetch('getBusinessName.php')
                .then(response => response.text())
                .then(bname => {
                    if (bname) {
                        document.getElementById('businessName').textContent = bname;
                    } else {
                        document.getElementById('businessName').textContent = 'SkyLink Access';
                    }
                })
                .catch(error => {
                    console.error('Error fetching business name:', error);
                    document.getElementById('businessName').textContent = 'SkyLink Access';
                });

            // Load dashboard stats (mock data - replace with actual API calls)
            loadDashboardStats();
        });

        function loadDashboardStats() {
            // Mock data - replace with actual API calls
            const mockData = {
                totalUsers: 142,
                activeClients: 98,
                pendingClients: 24,
                recentClients: [
                    { name: "John Doe", date: "Today, 10:30 AM", status: "Active" },
                    { name: "Jane Smith", date: "Yesterday", status: "Pending" },
                    { name: "Robert Johnson", date: "2 days ago", status: "Active" }
                ],
                recentActivities: [
                    "New client registered - John Doe",
                    "Visit scheduled - ABC Corp",
                    "Document approved - XYZ Ltd"
                ]
            };

            // Update stats
            document.getElementById('totalUsers').textContent = mockData.totalUsers;
            document.getElementById('activeClients').textContent = mockData.activeClients;
            document.getElementById('pendingClients').textContent = mockData.pendingClients;

            // Update recent clients table
            const clientsTable = document.getElementById('recentClientsTable');
            clientsTable.innerHTML = mockData.recentClients.map(client => `
                <tr>
                    <td>
                        <p>${client.name}</p>
                    </td>
                    <td>${client.date}</td>
                    <td><span class="status ${client.status.toLowerCase()}">${client.status}</span></td>
                </tr>
            `).join('');

            // Update recent activities
            const activitiesList = document.getElementById('recentActivities');
            activitiesList.innerHTML = mockData.recentActivities.map(activity => `
                <li class="completed">
                    <p>${activity}</p>
                    <i class='bx bx-dots-vertical-rounded'></i>
                </li>
            `).join('');
        }

        // Fullscreen functionality
        const fullscreenBtn = document.querySelector('.fullscreen');
        fullscreenBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (!document.fullscreenElement) {
                // Enter fullscreen
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
                fullscreenBtn.innerHTML = '<i class="bx bx-exit-fullscreen"></i>';
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="bx bx-fullscreen"></i>';
                }
            }
        });

        // Listen for fullscreen change events to update icon
        document.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) {
                fullscreenBtn.innerHTML = '<i class="bx bx-fullscreen"></i>';
            }
        });

        // Add these new event listeners
        document.getElementById('exportForm').addEventListener('submit', function (e) {
            e.preventDefault();
            this.submit();
        });

        document.getElementById('importForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const fileInput = document.getElementById('import_file');

            if (fileInput.files.length === 0) {
                alert('Please select a file to import');
                return;
            }

            const fileType = fileInput.files[0].name.split('.').pop().toLowerCase();
            const isZip = fileType === 'zip';
            const message = isZip
                ? 'Are you sure you want to import this data and files? This may overwrite existing records and files.'
                : 'Are you sure you want to import this data? This may overwrite existing records.';

            if (confirm(message)) {
                this.submit();
            }
        });

        // Show success/error messages from URL parameters
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');

            if (error) {
                const errorMessages = {
                    'upload_failed': 'File upload failed',
                    'invalid_parameters': 'Invalid parameters',
                    'invalid_format': 'Invalid file format',
                    'import_failed': 'Import failed'
                };

                const message = urlParams.get('message') || '';
                alert('Error: ' + (errorMessages[error] || error) + (message ? '\n' + message : ''));
            }

            if (success) {
                const successMessages = {
                    'import_completed': 'Data imported successfully'
                };

                alert('Success: ' + (successMessages[success] || success));
            }
        });
    </script>
</body>

</html>