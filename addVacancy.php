<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

// Fetch subscription details from the request table
include "../DB/connection.php";
Database::setupConnection();

$clientId = $_SESSION['client']['id'] ?? $_SESSION['client'];
$query = "SELECT subscription_end_date, year_end_date FROM request WHERE id = " . (int)$clientId;
$result = Database::$connection->query($query);

$subscriptionEndDate = '';
$yearEndDate = '';
if ($result && $result->num_rows > 0) {
    $request = $result->fetch_assoc();
    $subscriptionEndDate = $request['subscription_end_date'] ?? '';
    $yearEndDate = $request['year_end_date'] ?? '';
}

$currentDate = date('Y-m-d');
$showDashboard = true;
$paymentMessage = '';

if ($subscriptionEndDate && $currentDate === $subscriptionEndDate) {
    $showDashboard = false;
    $paymentMessage = "Your monthly payment is finished. Please make payment and re-access the system.";
} elseif ($yearEndDate && $currentDate === $yearEndDate) {
    $showDashboard = false;
    $paymentMessage = "Make payment for yearly payment.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        #content nav {
            height: 70px;
            background: var(--light);
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 24px;
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

        #content nav .fullscreen {
            font-size: 20px;
            cursor: pointer;
            color: var(--dark);
        }

        #content nav .fullscreen:hover {
            color: var(--blue);
        }

        #content nav .bx.bx-menu {
            cursor: pointer;
            color: var(--dark);
            font-size: 24px;
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
            gap: 16px;
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
            gap: 16px;
        }

        .breadcrumb {
            margin-bottom: 20px;
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

        #content main .head-title .right a {
            height: 36px;
            padding: 0 16px;
            border-radius: 36px;
            background: var(--blue);
            color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            text-decoration: none;
        }

        #content main .head-title .right a:hover {
            background: var(--primary-color);
        }

        #content main .head-title .right button {
            height: 36px;
            padding: 0 16px;
            border-radius: 36px;
            background: var(--light-blue);
            color: var(--primary-color);
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            cursor: pointer;
        }

        #content main .head-title .right button:hover {
            background: var(--primary-color);
            color: var(--light);
        }

        .form-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h2 {
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--light);
            color: var(--dark);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group button[type="submit"] {
            background: var(--primary-color);
            color: var(--light);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .form-group button[type="submit"]:hover {
            background: var(--blue);
            transform: translateY(-2px);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            overflow-y: auto;
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: none;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
                margin: 1.75rem auto;
            }
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: var(--light);
            background-clip: padding-box;
            border: none;
            border-radius: 10px;
            outline: 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--grey);
        }

        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-close {
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
            background-color: transparent;
            border: 0;
            font-size: 1.5rem;
            line-height: 1;
            color: var(--dark);
            opacity: 0.5;
            cursor: pointer;
        }

        .btn-close:hover {
            opacity: 0.75;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--grey);
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .btn-primary {
            color: var(--light);
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--secondary-color);
        }

        .btn-secondary {
            color: var(--dark);
            background-color: var(--grey);
            border-color: var(--grey);
        }

        .btn-secondary:hover {
            background-color: var(--dark-grey);
            border-color: var(--dark-grey);
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

        @media screen and (max-width: 1200px) {
            #content main .head-title {
                flex-direction: column;
                gap: 1rem;
            }

            #content main .head-title .right {
                width: 100%;
                display: flex;
                justify-content: flex-end;
                gap: 1rem;
            }
        }

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

            #content main .head-title .left h1 {
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
            #content nav form.show~.profile-dropdown {
                display: none;
            }

            #content main {
                padding: 24px 16px;
            }

            #content main .head-title .right {
                justify-content: flex-start;
            }
        }

        .manage-vacancies {
            margin-bottom: 5px;
        }

        .btn-secondary {
            margin-left: 2px;
        }
    </style>
</head>

<body>
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
            <li class="active">
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
                <a href="exportImport.php">
                    <i class='bx bx-transfer'></i>
                    <span class="text">Export & Import</span>
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

    <section id="content">
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

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Add Vacancies</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Add Vacancies</a></li>
                    </ul>
                </div>
                <div class="right">
                    <a href="viewVacancy.php" class="manage-vacancies">
                        <i class='bx bxs-cog'></i>
                        <span>Manage Vacancies</span>
                    </a>
                    <button type="button" id="addCountryBtn" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                        <i class='bx bx-plus'></i>
                        <span>Add Country</span>
                    </button>
                </div>
            </div>

            <div id="addCountryModal" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Country</h5>
                            <button type="button" class="btn-close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="countryForm">
                                <div class="form-group">
                                    <label for="countryName">Country Name</label>
                                    <input type="text" class="form-control" id="countryName" placeholder="Enter country name" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="addCountry()">Add Country</button>
                        </div>
                    </div>
                </div>
            </div>

            <form id="vacancyForm" class="form-container">
                <div class="form-section">
                    <h2><i class='bx bxs-info-circle'></i>Basic Information</h2>
                    <div class="form-group">
                        <label for="countrySelect">Country</label>
                        <select name="country" class="form-control" id="countrySelect" required>
                            <option value="">Select Country</option>
                            <?php
                            $query = "SELECT cname FROM country WHERE request_id = " . (int)$clientId;
                            $result = Database::$connection->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"" . htmlspecialchars($row['cname']) . "\">" . htmlspecialchars($row['cname']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jobPosition">Job Position</label>
                        <input type="text" name="job_position" class="form-control" id="jobPosition" required>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bxs-save'></i> Save Vacancy
                    </button>
                </div>
            </form>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const clientId = <?php echo json_encode($clientId); ?>;

        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');
        menuBar.addEventListener('click', () => sidebar.classList.toggle('hide'));

        const profileDropdown = document.getElementById('profileDropdown');
        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        document.addEventListener('click', () => profileDropdown.classList.remove('active'));

        const switchMode = document.getElementById('switch-mode');
        switchMode.addEventListener('change', () => {
            document.body.classList.toggle('dark', switchMode.checked);
            localStorage.setItem('theme', switchMode.checked ? 'dark' : 'light');
        });

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            switchMode.checked = true;
        }

        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', (e) => {
            if (window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                searchButtonIcon.classList.toggle('bx-x', searchForm.classList.contains('show'));
                searchButtonIcon.classList.toggle('bx-search', !searchForm.classList.contains('show'));
            }
        });

        if (window.innerWidth < 768) sidebar.classList.add('hide');

        window.addEventListener('resize', () => {
            if (window.innerWidth > 576) {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
            }
        });

        const modal = document.getElementById('addCountryModal');
        const addCountryBtn = document.getElementById('addCountryBtn');
        const closeBtn = document.querySelector('.btn-close');
        const closeModalBtn = document.querySelector('[data-dismiss="modal"]');

        addCountryBtn.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        closeBtn.addEventListener('click', closeModal);
        closeModalBtn.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        const uploadProfile = document.getElementById('uploadProfile');
        const profileImage = document.getElementById('profileImage');

        uploadProfile.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => profileImage.src = event.target.result;
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            fetch('getBusinessName.php')
                .then(response => response.text())
                .then(bname => document.getElementById('businessName').textContent = bname || 'SkyLink Access')
                .catch(() => document.getElementById('businessName').textContent = 'SkyLink Access');
        });

        function addCountry() {
            const countryNameInput = document.getElementById('countryName');
            const countryName = countryNameInput.value.trim();

            if (!countryName) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please enter a country name',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
                return;
            }

            const form = new FormData();
            form.append('cn', countryName);
            form.append('request_id', clientId);

            const req = new XMLHttpRequest();
            req.open('POST', 'addCountryProcess.php', true);

            req.onreadystatechange = () => {
                if (req.readyState === 4) {
                    if (req.status === 200) {
                        try {
                            const response = JSON.parse(req.responseText);
                            if (response.status === 'success') {
                                const countrySelect = document.getElementById('countrySelect');
                                const option = document.createElement('option');
                                option.value = countryName;
                                option.textContent = countryName;
                                countrySelect.appendChild(option);
                                countryNameInput.value = '';
                                closeModal();

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Country added successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#3e64ff',
                                    width: 300
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonColor: '#3e64ff',
                                    width: 300
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error parsing response: ' + e.message,
                                icon: 'error',
                                confirmButtonColor: '#3e64ff',
                                width: 300
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Request failed with status: ' + req.status,
                            icon: 'error',
                            confirmButtonColor: '#3e64ff',
                            width: 300
                        });
                    }
                }
            };

            req.onerror = () => {
                Swal.fire({
                    title: 'Network Error!',
                    text: 'Network error occurred while adding country',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
            };

            req.send(form);
        }

        document.getElementById('vacancyForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('request_id', clientId);

            const req = new XMLHttpRequest();
            req.open('POST', 'process_vacancy.php', true);

            req.onreadystatechange = () => {
                if (req.readyState === 4) {
                    if (req.status === 200) {
                        try {
                            const response = JSON.parse(req.responseText);
                            Swal.fire({
                                title: response.status === 'success' ? 'Success!' : 'Error!',
                                text: response.message,
                                icon: response.status === 'success' ? 'success' : 'error',
                                confirmButtonColor: '#3e64ff',
                                width: 300
                            });

                            if (response.status === 'success') form.reset();
                        } catch (e) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error parsing response: ' + e.message,
                                icon: 'error',
                                confirmButtonColor: '#3e64ff',
                                width: 300
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Request failed with status: ' + req.status,
                            icon: 'error',
                            confirmButtonColor: '#3e64ff',
                            width: 300
                        });
                    }
                }
            };

            req.onerror = () => {
                Swal.fire({
                    title: 'Network Error!',
                    text: 'Network error occurred while adding vacancy',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
            };

            req.send(formData);
        });

        document.getElementById('addCountryBtn').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('addCountryModal'));
            modal.show();
        });

        const fullscreenBtn = document.querySelector('.fullscreen');
        fullscreenBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
                fullscreenBtn.innerHTML = '<i class="bx bx-exit-fullscreen"></i>';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="bx bx-fullscreen"></i>';
                }
            }
        });

        document.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) {
                fullscreenBtn.innerHTML = '<i class="bx bx-fullscreen"></i>';
            }
        });
    </script>
</body>

</html>