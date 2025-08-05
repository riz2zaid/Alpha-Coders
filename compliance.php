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
    <title>Admin Dashboard - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
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
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
            font-family: var(--lato);
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

        #sidebar.hide .business-name {
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

        #content nav .fullscreen {
            font-size: 20px;
            cursor: pointer;
            color: var(--dark);
        }

        #content nav .fullscreen:hover {
            color: var(--blue);
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
            color: var(--primary-color);
            pointer-events: unset;
        }

        /* Compliance Sections */
        .compliance-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .compliance-card {
            background: var(--light);
            border-radius: 15px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .compliance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .compliance-card h3 {
            color: var(--primary-color);
            margin-bottom: 16px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .compliance-card p {
            color: var(--dark-grey);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .compliance-card .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--light);
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .compliance-card .btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .compliance-card .btn i {
            margin-right: 8px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--grey);
            margin-bottom: 24px;
        }

        .tab {
            padding: 12px 24px;
            cursor: pointer;
            position: relative;
            color: var(--dark-grey);
            font-weight: 500;
        }

        .tab.active {
            color: var(--primary-color);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
                background: var(--primary-color);
            }

            #content main {
                padding: 24px 16px;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab {
                flex: 1 0 auto;
                text-align: center;
                padding: 12px 10px;
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
            <div class="business-name" id="businessName">Loading...</div>
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
                <a href="addAdmin.php">
                    <i class='bx bx-data'></i>
                    <span class="text">Add Admin</span>
                </a>
            </li>

            <li>
                <a href="adminuserManagement.php">
                    <i class='bx bx-transfer'></i>
                    <span class="text">Export & import</span>
                </a>
            </li>

            <li class="active">
                <a href="adminuserManagement.php">
                    <i class='bx bx-check-shield'></i>
                    <span class="text">Compliance</span>
                </a>
            </li>

            <li>
                <a href="addTask.php">
                    <i class='bx bx-money'></i>
                    <span class="text">Billing Payment</span>
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

            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-btn">
                    <img src="../image/logo/Management.png" id="profileImage">
                </div>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item" onclick="document.getElementById('uploadProfile').click()">
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
                    <h1>Compliance Management</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Compliance</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tabs">
                <div class="tab active" data-tab="system">System Compliance</div>
                <div class="tab" data-tab="user">User Compliance</div>
            </div>

            <div class="tab-content active" id="system-compliance">
                <div class="compliance-container">
                    <div class="compliance-card">
                        <h3><i class='bx bx-shield-quarter'></i> Data Protection</h3>
                        <p>Manage data protection policies and ensure GDPR compliance for all candidate and client data.</p>
                        <a href="#" class="btn"><i class='bx bx-edit'></i> Configure</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-lock-alt'></i> Security Settings</h3>
                        <p>Configure system security settings including password policies and access controls.</p>
                        <a href="#" class="btn"><i class='bx bx-cog'></i> Manage</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-history'></i> Audit Logs</h3>
                        <p>View and export system activity logs for compliance reporting and monitoring.</p>
                        <a href="#" class="btn"><i class='bx bx-file'></i> View Logs</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-data'></i> Data Retention</h3>
                        <p>Set up data retention policies and automatic data purging schedules.</p>
                        <a href="#" class="btn"><i class='bx bx-calendar'></i> Set Policies</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-cloud'></i> Backup & Recovery</h3>
                        <p>Configure automated backups and test disaster recovery procedures.</p>
                        <a href="#" class="btn"><i class='bx bx-cloud-upload'></i> Setup</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-certification'></i> Certifications</h3>
                        <p>Manage system certifications and compliance documentation.</p>
                        <a href="#" class="btn"><i class='bx bx-book'></i> Documentation</a>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="user-compliance">
                <div class="compliance-container">
                    <div class="compliance-card">
                        <h3><i class='bx bx-user-check'></i> User Permissions</h3>
                        <p>Manage user roles and permissions to ensure proper access controls.</p>
                        <a href="#" class="btn"><i class='bx bx-edit'></i> Configure</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-key'></i> Password Policies</h3>
                        <p>Set requirements for user passwords including complexity and expiration.</p>
                        <a href="#" class="btn"><i class='bx bx-lock'></i> Set Policies</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-list-check'></i> Training Records</h3>
                        <p>Track and manage compliance training completion for all users.</p>
                        <a href="#" class="btn"><i class='bx bx-notepad'></i> View Records</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-calendar-check'></i> Access Reviews</h3>
                        <p>Schedule and conduct periodic user access reviews.</p>
                        <a href="#" class="btn"><i class='bx bx-calendar'></i> Schedule</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-user-x'></i> Terminated Users</h3>
                        <p>Manage access revocation for terminated or inactive users.</p>
                        <a href="#" class="btn"><i class='bx bx-trash'></i> Review</a>
                    </div>

                    <div class="compliance-card">
                        <h3><i class='bx bx-file'></i> User Agreements</h3>
                        <p>Manage and track acceptance of user agreements and policies.</p>
                        <a href="#" class="btn"><i class='bx bx-download'></i> Reports</a>
                    </div>
                </div>
            </div>
        </main>
    </section>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle sidebar
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

        // Fetch business name
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
            
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and content
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(`${tabId}-compliance`).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>