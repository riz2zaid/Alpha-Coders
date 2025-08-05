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
        #content main {
            width: 100%;
            padding: 36px 24px;
            font-family: var(--poppins);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }

        #content main .head-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            grid-gap: 16px;
            flex-wrap: wrap;
        }

        #content main .head-title .left h1 {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        #content main .head-title .left .breadcrumb {
            display: flex;
            align-items: center;
            grid-gap: 16px;
        }

        #content main .head-title .left .breadcrumb li {
            color: var(--dark);
        }

        #content main .head-title .left .breadcrumb li a {
            color: var(--dark-grey);
            pointer-events: none;
        }

        #content main .head-title .left .breadcrumb li a.active {
            color: var(--blue);
            pointer-events: unset;
        }

        #content main .head-title .btn-download {
            height: 36px;
            padding: 0 16px;
            border-radius: 36px;
            background: var(--blue);
            color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            grid-gap: 10px;
            font-weight: 500;
        }

        #content main .box-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            grid-gap: 24px;
            margin-top: 36px;
        }

        #content main .box-info li {
            padding: 24px;
            background: var(--light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        #content main .box-info li:hover {
            transform: translateY(-5px);
        }

        #content main .box-info li .bx {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            font-size: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #content main .box-info li:nth-child(1) .bx {
            background: var(--light-blue);
            color: var(--blue);
        }

        #content main .box-info li:nth-child(2) .bx {
            background: var(--light-yellow);
            color: var(--yellow);
        }

        #content main .box-info li:nth-child(3) .bx {
            background: var(--light-orange);
            color: var(--orange);
        }

        #content main .box-info li .text h3 {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        #content main .box-info li .text p {
            color: var(--dark);
        }

        #content main .table-data {
            display: flex;
            flex-wrap: wrap;
            grid-gap: 24px;
            margin-top: 24px;
            width: 100%;
            color: var(--dark);
        }

        #content main .table-data>div {
            border-radius: 20px;
            background: var(--light);
            padding: 24px;
            overflow-x: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        #content main .table-data .head {
            display: flex;
            align-items: center;
            grid-gap: 16px;
            margin-bottom: 24px;
        }

        #content main .table-data .head h3 {
            margin-right: auto;
            font-size: 24px;
            font-weight: 600;
        }

        #content main .table-data .head .bx {
            cursor: pointer;
        }

        #content main .table-data .order {
            flex-grow: 1;
            flex-basis: 500px;
        }

        #content main .table-data .order table {
            width: 100%;
            border-collapse: collapse;
        }

        #content main .table-data .order table th {
            padding-bottom: 12px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid var(--grey);
        }

        #content main .table-data .order table td {
            padding: 16px 0;
        }

        #content main .table-data .order table tr td:first-child {
            display: flex;
            align-items: center;
            grid-gap: 12px;
            padding-left: 6px;
        }

        #content main .table-data .order table td img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        #content main .table-data .order table tbody tr:hover {
            background: var(--grey);
        }

        #content main .table-data .todo {
            flex-grow: 1;
            flex-basis: 300px;
        }

        #content main .table-data .todo .todo-list {
            width: 100%;
        }

        #content main .table-data .todo .todo-list li {
            width: 100%;
            margin-bottom: 16px;
            background: var(--grey);
            border-radius: 10px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #content main .table-data .todo .todo-list li .bx {
            cursor: pointer;
        }

        #content main .table-data .todo .todo-list li.completed {
            border-left: 10px solid var(--blue);
        }

        #content main .table-data .todo .todo-list li.not-completed {
            border-left: 10px solid var(--orange);
        }

        #content main .table-data .todo .todo-list li:last-child {
            margin-bottom: 0;
        }

        /* Animations */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Responsive Design */
        @media screen and (max-width: 1200px) {
            #content main .table-data { flex-direction: column; }
        }

        @media screen and (max-width: 768px) {
            #sidebar { width: 200px; }
            #content { width: calc(100% - 60px); left: 200px; }
            #content nav .nav-link { display: none; }
            #content main .head-title .left h1 { font-size: 28px; }
        }

        @media screen and (max-width: 576px) {
            #content nav form .form-input input { display: none; }
            #content nav form .form-input button { width: auto; height: auto; background: transparent; border-radius: none; color: var(--dark); }
            #content nav form.show .form-input input { display: block; width: 100%; }
            #content nav form.show .form-input button { width: 36px; height: 100%; border-radius: 0 36px 36px 0; color: var(--light); background: var(--blue); }
            #content nav form.show~.notification, #content nav form.show~.profile, #content nav form.show~.logout { display: none; }
            #content main .box-info { grid-template-columns: 1fr; }
            #content main .table-data .head { min-width: 420px; }
            #content main .table-data .order table { min-width: 420px; }
            #content main .table-data .todo .todo-list { min-width: 420px; }
            #content main { padding: 24px 16px; }
        }

        /* CV Editor Container */
        .cv-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .cv-controls {
            flex: 1;
            max-width: 300px;
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .cv-preview {
            flex: 3;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: 800px;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--grey);
            border-radius: 5px;
            background: var(--light);
            color: var(--dark);
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background: var(--grey);
            color: var(--dark);
        }

        /* CV Template Options */
        .template-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .template-option {
            border: 2px solid var(--grey);
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .template-option:hover {
            border-color: var(--primary-color);
        }

        .template-option.selected {
            border-color: var(--primary-color);
            background: rgba(67, 97, 238, 0.1);
        }

        .template-preview {
            width: 100%;
            height: 100px;
            background: #f5f5f5;
            margin-bottom: 5px;
        }

        /* Language selector */
        .language-selector {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 15px 0;
        }

        .language-option {
            padding: 5px 10px;
            border: 1px solid var(--grey);
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
        }

        .language-option.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Dark mode adjustments */
        body.dark .cv-controls {
            background: #1a1a3d;
        }

        body.dark .form-group label {
            color: white;
        }

        body.dark .template-option {
            border-color: #2a2a4a;
        }

        body.dark .template-option:hover {
            border-color: var(--primary-color);
        }

        /* CV Template Specific Styles */
        .cv-classic { background: white; color: #333; }
        .cv-modern { background: #f9f9f9; color: #222; border: 1px solid #ddd; }
        .cv-header img { width: 120px; height: 120px; border-radius: 50%; margin-bottom: 15px; }
        .cv-section { margin-bottom: 25px; }
        .cv-section h2 { font-size: 24px; margin-bottom: 10px; }
        .cv-contact { font-size: 14px; margin-bottom: 15px; }
        .cv-education, .cv-skills { font-size: 14px; }
        .cv-education p, .cv-skills p { margin: 5px 0; }

        @media (max-width: 992px) {
            .cv-container { flex-direction: column; }
            .cv-controls { max-width: 100%; }
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
            <li class="active">
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
                <a href="cvgen.php">
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
            <li>
                <a href="adminuserManagement.php">
                    <i class='bx bx-transfer'></i>
                    <span class="text">Export & import</span>
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

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>CV Generator</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">CV Generator</a></li>
                    </ul>
                </div>
            </div>

            <div class="cv-container">
                <div class="cv-controls">
                    <form id="cvForm">
                        <div class="form-group">
                            <label for="candidate_id">Select Candidate</label>
                            <select id="candidate_id" name="candidate_id" required>
                                <option value="">-- Select Candidate --</option>
                                <option value="1">Nimasha Hedigama</option>
                            </select>
                        </div>

                        <h3>CV Template</h3>
                        <div class="template-options">
                            <div class="template-option selected" data-template="classic">
                                <div class="template-preview" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);"></div>
                                <div>Classic</div>
                            </div>
                            <div class="template-option" data-template="modern">
                                <div class="template-preview" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);"></div>
                                <div>Modern</div>
                            </div>
                        </div>

                        <h3>Color Scheme</h3>
                        <div class="form-group">
                            <label for="primary_color">Primary Color</label>
                            <input type="color" id="primary_color" name="primary_color" value="#4361ee">
                        </div>
                        <div class="form-group">
                            <label for="secondary_color">Secondary Color</label>
                            <input type="color" id="secondary_color" name="secondary_color" value="#3a0ca3">
                        </div>

                        <h3>Language</h3>
                        <div class="language-selector">
                            <div class="language-option selected" data-lang="en">English</div>
                            <div class="language-option" data-lang="ar">Arabic</div>
                        </div>

                        <div class="form-group">
                            <button type="button" id="generateCv" class="btn btn-primary">
                                <i class="fas fa-file-alt"></i> Generate CV
                            </button>
                            <button type="button" id="downloadPdf" class="btn btn-secondary" disabled>
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </form>
                </div>

                <div class="cv-preview" id="cvPreview">
                    <div class="text-center" style="padding: 100px 0; color: #666;">
                        <i class="fas fa-file-alt fa-5x" style="margin-bottom: 20px;"></i>
                        <h3>CV Preview</h3>
                        <p>Select a candidate and click "Generate CV" to see the preview</p>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <!-- Include required libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        // Initialize Quill editor
        const quill = new Quill('#cvPreview', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered'}, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: 'CV content will appear here...',
        });

        // Global variables
        let selectedCandidate = null;
        let currentTemplate = 'classic';
        let currentLanguage = 'en';
        const { jsPDF } = window.jspdf;

        // DOM ready
        $(document).ready(function() {
            // Template selection
            $('.template-option').click(function() {
                $('.template-option').removeClass('selected');
                $(this).addClass('selected');
                currentTemplate = $(this).data('template');
                if (selectedCandidate) generateCvPreview();
            });

            // Language selection
            $('.language-option').click(function() {
                $('.language-option').removeClass('selected');
                $(this).addClass('selected');
                currentLanguage = $(this).data('lang');
                if (selectedCandidate) generateCvPreview();
            });

            // Color change handlers
            $('#primary_color, #secondary_color').change(function() {
                if (selectedCandidate) generateCvPreview();
            });

            // Generate CV button
            $('#generateCv').click(function() {
                const candidateId = $('#candidate_id').val();
                if (!candidateId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please select a candidate first',
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                    return;
                }
                fetchCandidateData(candidateId);
            });

            // Download PDF button
            $('#downloadPdf').click(function() {
                if (!selectedCandidate) return;
                generatePdf();
            });
        });

        // Fetch candidate data
        function fetchCandidateData(candidateId) {
            $.ajax({
                url: 'getCandidateData.php',
                type: 'GET',
                data: { id: candidateId },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Loading...',
                        html: 'Fetching candidate data',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        selectedCandidate = response.data;
                        generateCvPreview();
                        $('#downloadPdf').prop('disabled', false);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to fetch candidate data',
                            icon: 'error',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to fetch candidate data: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
        }

        // Generate CV preview
        function generateCvPreview() {
            // Show loading state
            $('#cvPreview').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Generating CV...</p></div>');
            
            // Simulate API call for translation (in a real app, you'd call a translation API)
            setTimeout(() => {
                const primaryColor = $('#primary_color').val();
                const secondaryColor = $('#secondary_color').val();
                
                // Generate CV HTML based on template
                const cvHtml = generateCvHtml(selectedCandidate, currentTemplate, primaryColor, secondaryColor);
                
                // Display the generated CV
                $('#cvPreview').html(cvHtml);
                
                // Make CV editable
                makeCvEditable();
                
            }, 1000);
        }

        // Generate CV HTML
        function generateCvHtml(candidate, template, primaryColor, secondaryColor) {
            // Default photo URL if profile_photo is not available
            const photoUrl = candidate.profile_photo ? `../uploads/candidates/${candidate.profile_photo}` : '../image/logo/default.png';

            let html = '';
            if (template === 'classic') {
                html = `
                    <div class="cv-template cv-${template}" style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
                        <div class="cv-header" style="text-align: center; padding: 20px; background: ${primaryColor}; color: white;">
                            <img src="${photoUrl}" alt="Profile Photo">
                            <h1 style="margin: 10px 0; font-size: 28px;">${candidate.fullname || 'N/A'}</h1>
                        </div>
                        <div class="cv-body" style="padding: 20px;">
                            <div class="cv-section cv-contact">
                                <h2 style="color: ${secondaryColor}; border-bottom: 2px solid ${secondaryColor}; padding-bottom: 5px;">CONTACT</h2>
                                <p><strong>Phone:</strong> ${candidate.contact_number || '+947660955111'}</p>
                                <p><strong>Email:</strong> ${candidate.email || 'nimashahed12@gmail.com'}</p>
                                <p><strong>Address:</strong> ${candidate.address || 'Welimada, Badulla District'}</p>
                            </div>
                            <div class="cv-section cv-education">
                                <h2 style="color: ${secondaryColor}; border-bottom: 2px solid ${secondaryColor}; padding-bottom: 5px;">EDUCATION</h2>
                                ${candidate.dob ? `<p><strong>2019 - B.Welimada Muslim Maha Vidyalaya</strong> - Complete Ordinary Level</p>` : ''}
                                ${candidate.institute_name ? `<p><strong>2021 - 2022 - ${candidate.institute_name}</strong> - Diploma in Hardware Engineering, Graphic Design, Microsoft Kit</p>` : ''}
                                ${candidate.highest_qualification ? `<p><strong>2023 - Now - Java Institute Advanced Technology</strong> - ${candidate.highest_qualification} (Ongoing)</p>` : ''}
                            </div>
                            <div class="cv-section cv-skills">
                                <h2 style="color: ${secondaryColor}; border-bottom: 2px solid ${secondaryColor}; padding-bottom: 5px;">SKILLS</h2>
                                <p>HTML, Bootstrap, JS, PHP, Java, C++, MySQL, English, Sinhala, Tamil</p>
                            </div>
                            <div class="cv-section cv-profile">
                                <h2 style="color: ${secondaryColor}; border-bottom: 2px solid ${secondaryColor}; padding-bottom: 5px;">PROFILE</h2>
                                <p>I am deeply passionate about software engineering and am committed to advancing my skills through dedicated study and practical application. I focus on creating user-friendly and responsive web designs, with a deep understanding of the latest technology and development practices.</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (template === 'modern') {
                html = `
                    <div class="cv-template cv-${template}" style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: 'Helvetica Neue', sans-serif; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                        <div class="cv-header" style="display: flex; align-items: center; padding: 20px; background: ${primaryColor}; color: white;">
                            <img src="${photoUrl}" alt="Profile Photo" style="margin-right: 20px;">
                            <div>
                                <h1 style="margin: 0; font-size: 28px;">${candidate.fullname || 'N/A'}</h1>
                                <p style="margin: 5px 0; font-size: 16px;">Software Engineer</p>
                            </div>
                        </div>
                        <div class="cv-body" style="padding: 20px; background: white;">
                            <div class="cv-section cv-contact" style="margin-bottom: 20px;">
                                <h2 style="color: ${secondaryColor}; font-size: 20px; margin-bottom: 10px;">Contact</h2>
                                <p><i class='bx bx-phone'></i> ${candidate.contact_number || '+947660955111'}</p>
                                <p><i class='bx bx-envelope'></i> ${candidate.email || 'nimashahed12@gmail.com'}</p>
                                <p><i class='bx bx-map'></i> ${candidate.address || 'Welimada, Badulla District'}</p>
                            </div>
                            <div class="cv-section cv-education" style="margin-bottom: 20px;">
                                <h2 style="color: ${secondaryColor}; font-size: 20px; margin-bottom: 10px;">Education</h2>
                                ${candidate.dob ? `<p><strong>2019</strong> - B.Welimada Muslim Maha Vidyalaya - Complete Ordinary Level</p>` : ''}
                                ${candidate.institute_name ? `<p><strong>2021-2022</strong> - ${candidate.institute_name} - Diploma in Hardware Engineering, Graphic Design, Microsoft Kit</p>` : ''}
                                ${candidate.highest_qualification ? `<p><strong>2023-Now</strong> - Java Institute Advanced Technology - ${candidate.highest_qualification} (Ongoing)</p>` : ''}
                            </div>
                            <div class="cv-section cv-skills" style="margin-bottom: 20px;">
                                <h2 style="color: ${secondaryColor}; font-size: 20px; margin-bottom: 10px;">Skills</h2>
                                <p>HTML, Bootstrap, JS, PHP, Java, C++, MySQL, English, Sinhala, Tamil</p>
                            </div>
                            <div class="cv-section cv-profile">
                                <h2 style="color: ${secondaryColor}; font-size: 20px; margin-bottom: 10px;">Profile</h2>
                                <p>I am passionate about software engineering, focusing on user-friendly web designs and leveraging the latest technologies to deliver innovative solutions.</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            return html;
        }

        // Make CV editable
        function makeCvEditable() {
            // In a real implementation, you would use Quill.js or similar
            // to make sections of the CV editable
            console.log("CV is now editable");
        }

        // Generate PDF
        function generatePdf() {
            Swal.fire({
                title: 'Generating PDF',
                html: 'Please wait while we generate your PDF...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Use html2canvas to capture the CV
                    html2canvas(document.getElementById('cvPreview')).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new jsPDF({
                            orientation: 'portrait',
                            unit: 'mm'
                        });
                        
                        const imgProps = pdf.getImageProperties(imgData);
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                        
                        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                        pdf.save(`${selectedCandidate.fullname}_CV.pdf`);
                        
                        Swal.close();
                    });
                }
            });
        }

        // Dark mode toggle
        const switchMode = document.getElementById('switch-mode');
        switchMode.addEventListener('change', function() {
            document.body.classList.toggle('dark', this.checked);
            localStorage.setItem('theme', this.checked ? 'dark' : 'light');
        });

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            switchMode.checked = true;
        }

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

        document.addEventListener('click', function () {
            profileDropdown.classList.remove('active');
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
    </script>
</body>
</html>