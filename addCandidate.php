<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

include "../DB/connection.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        html {
            overflow-x: hidden;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
            --dark-grey: #BBBBBB;
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
            font-family: var(--lato);
        }

        body.dark .form-container,
        body.dark .form-section,
        body.dark .form-group input,
        body.dark .form-group select,
        body.dark .form-group textarea,
        body.dark .form-group .file-upload-text {
            background: #020216ff;
            color: #ffffff;
        }

        body.dark .form-group input:focus,
        body.dark .form-group select:focus,
        body.dark .form-group textarea:focus,
        body.dark .form-group .file-upload-text:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        body.dark .form-group button[type="submit"] {
            background: var(--primary-color);
            color: var(--light);
        }

        body.dark .form-group button[type="submit"]:hover {
            background: var(--secondary-color);
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
            grid-gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
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

        /* Updated Form Styles */
        .form-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
            background: var(--light);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(67, 97, 238, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-section-header {
            padding: 1.25rem 1.5rem;
            background: rgba(67, 97, 238, 0.1);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
        }

        .form-section-header h2 {
            color: var(--primary-color);
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            background: var(--primary-color);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: glow 2s ease-in-out infinite alternate;
            position: relative;
            z-index: 1;
        }

        .form-section-header h2 i {
            margin-right: 10px;
            font-size: 1.2em;
            color: var(--primary-color);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .form-section-header .toggle-icon {
            transition: transform 0.3s ease;
            font-size: 1.1em;
            color: var(--primary-color);
        }

        .form-section-header.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        .form-section-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shine 3s ease-in-out infinite;
            z-index: 0;
        }

        .form-section-content {
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .form-section-content.collapsed {
            display: none;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .form-group .optional-badge {
            background: var(--light-blue);
            color: var(--blue);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            margin-left: 5px;
            font-weight: normal;
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
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .form-group button[type="submit"]:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: block;
            padding: 0.75rem;
            border: 1px dashed #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--light);
        }

        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
        }

        .file-upload-label i {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-text {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            background: var(--light);
            color: var(--dark-grey);
            margin-top: 0.5rem;
            display: block;
            resize: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem;
        }

        .col-md-6,
        .col-md-4,
        .col-md-12 {
            padding: 0 0.75rem;
            flex: 0 0 100%;
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
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
            #content nav form.show~.profile,
            #content nav form.show~.logout {
                display: none;
            }

            #content main {
                padding: 24px 16px;
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
            <li class="active">
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
    <!-- SIDEBAR -->

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search . . . .">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
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
                        <i class='bx bxs-cloud-upload'></i><span>Upload Profile</span>
                    </a>
                    <input type="file" id="uploadProfile" class="upload-profile" accept="image/*">
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item"><i class='bx bxs-user'></i><span>Profile</span></a>
                    <a href="#" class="dropdown-item"><i class='bx bxs-cog'></i><span>Settings</span></a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item"><i class='bx bxs-log-out-circle'></i><span>Logout</span></a>
                </div>
            </div>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Add Candidate</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Add Candidate</a></li>
                    </ul>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="form-container" id="candidateForm">
                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-user me-2"></i>1. Personal Information</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" id="full_name"
                                        placeholder="As per NIC or Passport">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" id="date_of_birth" name="date_of_birth">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <select name="gender">
                                        <?php
                                        $rs = Database::search("SELECT * FROM `gender`");
                                        $num = $rs->num_rows;
                                        for ($x = 0; $x < $num; $x++) {
                                            $data = $rs->fetch_assoc();
                                            ?>
                                            <option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Civil Status <span class="text-danger">*</span></label>
                                    <select name="civil_status">
                                        <?php
                                        $rs = Database::search("SELECT * FROM `clivil_status`");
                                        $num = $rs->num_rows;
                                        for ($x = 0; $x < $num; $x++) {
                                            $data = $rs->fetch_assoc();
                                            ?>
                                            <option value="<?php echo $data['id']; ?>"><?php echo $data['status']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Profile Photo <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="profile_photo" class="file-upload-label">
                                            <i class="fas fa-camera"></i>
                                            <span>Click to upload photo</span>
                                        </label>
                                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="profile_photoName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" placeholder="Full residential address"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>District & Province <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="district" name="district_province"
                                        placeholder="Useful for regional filtering">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_number" name="contact_number"
                                        placeholder="Mobile number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address <span class="optional-badge">Optional</span></label>
                            <input type="email" id="email_address" name="email_address" placeholder="For notifications">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-id-card me-2"></i>2. NIC (National Identity Card – Sri Lanka)</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIC Number <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="nic_number" name="nic_number"
                                        placeholder="12-digit or 9-digit with letter">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIC Issue Date <span class="optional-badge">Optional</span></label>
                                    <input type="date" id="nic_issue_date" name="nic_issue_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIC Front Image <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="nic_front_image" class="file-upload-label">
                                            <i class="fas fa-file-image"></i>
                                            <span>Upload NIC Front (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="nic_front_image" name="nic_front_image"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="nic_front_imageName" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIC Back Image <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="nic_back_image" class="file-upload-label">
                                            <i class="fas fa-file-image"></i>
                                            <span>Upload NIC Back (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="nic_back_image" name="nic_back_image"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="nic_back_imageName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-passport me-2"></i>3. Passport Details (For Foreign Work)</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Passport Number <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="passport_number" name="passport_number"
                                        placeholder="e.g., N1234567">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Country of Issue <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="country_of_issue" name="country_of_issue" value="Sri Lanka">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Passport Status <span class="optional-badge">Optional</span></label>
                                    <select name="passport_status">
                                        <?php
                                        $rs = Database::search("SELECT * FROM `passport_status`");
                                        $num = $rs->num_rows;
                                        for ($x = 0; $x < $num; $x++) {
                                            $data = $rs->fetch_assoc();
                                            ?>
                                            <option value="<?php echo $data['id']; ?>"><?php echo $data['status']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Passport Issue Date <span class="optional-badge">Optional</span></label>
                                    <input type="date" id="passport_issue_date" name="passport_issue_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Passport Expiry Date <span class="optional-badge">Optional</span></label>
                                    <input type="date" id="passport_expiry_date" name="passport_expiry_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Passport Scan Upload <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="passport_scan_upload" class="file-upload-label">
                                            <i class="fas fa-file-upload"></i>
                                            <span>Upload Passport (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="passport_scan_upload" name="passport_scan_upload"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="passport_scan_uploadName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-phone-alt me-2"></i>4. Emergency Contact Details</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                        placeholder="Full name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Relationship <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="emergency_contact_relationship"
                                        name="emergency_contact_relationship" placeholder="e.g., Mother, Brother">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile Number <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="emergency_contact_mobile" name="emergency_contact_mobile"
                                        placeholder="Active number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Alternate Number <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="emergency_contact_alternate"
                                        name="emergency_contact_alternate" placeholder="Secondary number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address <span class="optional-badge">Optional</span></label>
                            <textarea name="emergency_contact_address" id="emergency_contact_address"
                                placeholder="Full address"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-graduation-cap me-2"></i>5. Education & Qualifications</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Highest Qualification <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="highest_qualification" name="highest_qualification"
                                        placeholder="O/L, A/L, Diploma, etc.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Field of Study <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="field_of_study" name="field_of_study"
                                        placeholder="e.g., Science, Arts">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Institution Name <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="institution_name" name="institution_name"
                                        placeholder="School / University">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Year Completed <span class="optional-badge">Optional</span></label>
                                    <input type="number" id="year_completed" name="year_completed"
                                        placeholder="e.g., 2020" min="1900" max="2025">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Upload Certificate(s) <span class="optional-badge">Optional</span></label>
                            <div class="file-upload-wrapper">
                                <label for="upload_certificates" class="file-upload-label">
                                    <i class="fas fa-file-certificate"></i>
                                    <span>Upload Certificate (JPEG/PDF)</span>
                                </label>
                                <input type="file" id="upload_certificates" name="upload_certificates"
                                    accept="image/jpeg,application/pdf" class="file-upload-input">
                                <input type="text" class="file-upload-text" id="upload_certificatesName" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-language me-2"></i>6. Language & Skills</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Languages Spoken <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="languages_spoken" name="languages_spoken"
                                        placeholder="Sinhala, English, Tamil, etc.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Work Skills <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="work_skills" name="work_skills"
                                        placeholder="Typing, Driving, Cooking, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Computer Skills <span class="optional-badge">Optional</span></label>
                                    <select name="computer_skills">
                                        <?php
                                        $rs = Database::search("SELECT * FROM `computer_skill`");
                                        $num = $rs->num_rows;
                                        for ($x = 0; $x < $num; $x++) {
                                            $data = $rs->fetch_assoc();
                                            ?>
                                            <option value="<?php echo $data['id']; ?>"><?php echo $data['skill']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Technical Training <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="technical_training" class="file-upload-label">
                                            <i class="fas fa-file-alt"></i>
                                            <span>Upload Training Documents (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="technical_training" name="technical_training"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="technical_trainingName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-briefcase me-2"></i>7. Work Experience</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company Name <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="company_name" name="company_name"
                                        placeholder="Past employer">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position Held <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="position_held" name="position_held" placeholder="Job title">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Duration <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="duration" name="duration" placeholder="Start and end dates">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Country (if abroad) <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="country_abroad" name="country_abroad">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reason for Leaving <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="reason_for_leaving" name="reason_for_leaving"
                                        placeholder="Optional">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Experience Letter <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="experience_letter" class="file-upload-label">
                                            <i class="fas fa-file-signature"></i>
                                            <span>Upload Experience Letter (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="experience_letter" name="experience_letter"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="experience_letterName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-heartbeat me-2"></i>8. Medical & Health Information</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Blood Group <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="blood_group" name="blood_group"
                                        placeholder="A, B, AB, O (+/-)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Allergies <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="allergies" name="allergies"
                                        placeholder="Any known allergies">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Chronic Illness <span class="optional-badge">Optional</span></label>
                                    <input type="text" id="chronic_illness" name="chronic_illness"
                                        placeholder="Diabetes, Heart Disease, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Medical Clearance Report <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="medical_clearance_report" class="file-upload-label">
                                            <i class="fas fa-file-medical"></i>
                                            <span>Upload Medical Report (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="medical_clearance_report" name="medical_clearance_report"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="medical_clearance_reportName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>COVID Vaccination Card <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="covid_vaccination_card" class="file-upload-label">
                                            <i class="fas fa-syringe"></i>
                                            <span>Upload COVID Card (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="covid_vaccination_card" name="covid_vaccination_card"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="covid_vaccination_cardName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vision Test Report <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="vision_test_report" class="file-upload-label">
                                            <i class="fas fa-eye"></i>
                                            <span>Upload Vision Test (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="vision_test_report" name="vision_test_report"
                                            accept="image/jpeg,application/pdf" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="vision_test_reportName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Physical Fitness Status <span class="optional-badge">Optional</span></label>
                            <textarea name="physical_fitness_status" id="physical_fitness_status"
                                placeholder="Dropdown or doctor's notes"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-shield-alt me-2"></i>9. Police & Legal Clearance</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Police Clearance Certificate <span
                                            class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="police_clearance_certificate" class="file-upload-label">
                                            <i class="fas fa-file-shield"></i>
                                            <span>Upload Police Certificate (JPEG/PDF)</span>
                                        </label>
                                        <input type="file" id="police_clearance_certificate"
                                            name="police_clearance_certificate" accept="image/jpeg,application/pdf"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text"
                                            id="police_clearance_certificateName" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Certificate Issue Date <span class="optional-badge">Optional</span></label>
                                    <input type="date" id="certificate_issue_date" name="certificate_issue_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Expiry Date <span class="optional-badge">Optional</span></label>
                                    <input type="date" id="expiry_date" name="expiry_date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-file-upload me-2"></i>10. Document Uploads Summary</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Other Documents <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="other_documents" class="file-upload-label">
                                            <i class="fas fa-file-archive"></i>
                                            <span>Upload Other Documents (JPEG/PNG)</span>
                                        </label>
                                        <input type="file" id="other_documents" name="other_documents"
                                            accept="image/png,image/jpeg" class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="other_documentsName" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CV/Resume <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="cv_resume" class="file-upload-label">
                                            <i class="fas fa-file-user"></i>
                                            <span>Upload CV/Resume (PDF/DOC/DOCX)</span>
                                        </label>
                                        <input type="file" id="cv_resume" name="cv_resume"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="cv_resumeName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-file-upload me-2"></i>11. Offer Letter</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Agreement <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="agreement" class="file-upload-label">
                                            <i class="fas fa-file-contract"></i>
                                            <span>Upload Agreement (JPEG/PNG)</span>
                                        </label>
                                        <input type="file" id="agreement" name="agreement" accept="image/png,image/jpeg"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="agreementName" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contract <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="contract" class="file-upload-label">
                                            <i class="fas fa-file-signature"></i>
                                            <span>Upload Contract (PDF/DOC/DOCX)</span>
                                        </label>
                                        <input type="file" id="contract" name="contract"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="contractName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-file-upload me-2"></i>12. Undertaking Letter</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Undertaking Letter <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="undertaking_letter" class="file-upload-label">
                                            <i class="fas fa-handshake"></i>
                                            <span>Upload Undertaking Letter (PDF/DOC/DOCX)</span>
                                        </label>
                                        <input type="file" id="undertaking_letter" name="undertaking_letter"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="undertaking_letterName"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-file-upload me-2"></i>13. Visa Upload</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Visa Document <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="visa_document" class="file-upload-label">
                                            <i class="fas fa-handshake"></i>
                                            <span>Upload Visa Document (PDF/DOC/DOCX)</span>
                                        </label>
                                        <input type="file" id="visa_document" name="visa_document"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="visa_documentName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-handshake me-2"></i>13. GS Letter</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="form-section-content collapsed">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>GS Letter <span class="optional-badge">Optional</span></label>
                                    <div class="file-upload-wrapper">
                                        <label for="gs_letter" class="file-upload-label">
                                            <i class="fas fa-handshake"></i>
                                            <span>Upload GS Letter (PDF/DOC/DOCX)</span>
                                        </label>
                                        <input type="file" id="gs_letter" name="gs_letter"
                                            accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="file-upload-input">
                                        <input type="text" class="file-upload-text" id="gs_letterName" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <h2><i class="fas fa-check-circle me-2"></i>Candidate Options</h2>
                    </div>
                    <div class="form-section-content">
                        <div class="form-group">
                            <label>Candidate Option <span class="text-danger">*</span></label>
                            <select name="candidate_option" required>
                                <?php
                                $rs = Database::search("SELECT * FROM `candidate_option`");
                                $num = $rs->num_rows;
                                for ($x = 0; $x < $num; $x++) {
                                    $data = $rs->fetch_assoc();
                                    ?>
                                    <option value="<?php echo $data['id']; ?>"><?php echo $data['option']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit"><i class="fas fa-save me-2"></i>Save Candidate</button>
                </div>
            </form>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Sidebar toggle
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

// Dark mode toggle
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

if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark');
    switchMode.checked = true;
}

// Search toggle for mobile
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
        };
        reader.readAsDataURL(file);
    }
});

// Business name fetch
document.addEventListener('DOMContentLoaded', function () {
    fetch('getBusinessName.php')
        .then(response => response.text())
        .then(bname => {
            document.getElementById('businessName').textContent = bname || 'SkyLink Access';
        })
        .catch(error => {
            console.error('Error fetching business name:', error);
            document.getElementById('businessName').textContent = 'SkyLink Access';
        });

    // Initialize collapsible sections
    initCollapsibleSections();

    // Initialize file upload previews
    initFileUploadPreviews();
});

// Custom collapsible sections implementation
function initCollapsibleSections() {
    const sectionHeaders = document.querySelectorAll('.form-section-header');

    sectionHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const section = this.parentElement;
            const content = this.nextElementSibling;

            this.classList.toggle('collapsed');
            content.classList.toggle('collapsed');

            const sectionId = section.id || section.querySelector('h2').textContent.trim();
            const isCollapsed = content.classList.contains('collapsed');
            localStorage.setItem(`section_${sectionId}`, isCollapsed);
        });

        const section = header.parentElement;
        const content = header.nextElementSibling;
        const sectionId = section.id || section.querySelector('h2').textContent.trim();
        const isCollapsed = localStorage.getItem(`section_${sectionId}`) === 'true';

        if (isCollapsed) {
            header.classList.add('collapsed');
            content.classList.add('collapsed');
        }
    });
}

// File upload handling with previews
function initFileUploadPreviews() {
    const fileInputs = document.querySelectorAll('.file-upload-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const textField = document.querySelector(`#${this.id}Name`);
            if (this.files && this.files[0] && textField) {
                textField.value = this.files[0].name;
            } else if (textField) {
                textField.value = '';
            }

            if (this.files && this.files[0] && this.files[0].type.match('image.*')) {
                const previewContainer = document.getElementById(this.id + 'Preview');
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '100px';
                        img.style.marginTop = '10px';
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'remove-preview';
                        removeBtn.innerHTML = '×';
                        removeBtn.style.marginLeft = '10px';
                        removeBtn.onclick = function () {
                            input.value = '';
                            textField.value = '';
                            previewContainer.innerHTML = '';
                        };
                        previewDiv.appendChild(img);
                        previewDiv.appendChild(removeBtn);
                        previewContainer.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
}

// Check candidate limit
async function checkCandidateLimit() {
    try {
        const response = await fetch('check_candidate_limit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error checking candidate limit:', error);
        return { canAdd: false, message: 'Failed to check candidate limit' };
    }
}

// Duplicate check and form submission
async function checkDuplicateNicOrPassport(nicNumber, passportNumber) {
    try {
        const response = await fetch('check_duplicate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `nic_number=${encodeURIComponent(nicNumber)}&passport_number=${encodeURIComponent(passportNumber)}`
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error checking duplicates:', error);
        return { exists: false };
    }
}

async function saveCandidate(e) {
    e.preventDefault();

    const form = document.getElementById('candidateForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    submitBtn.disabled = true;

    // Check candidate limit
    const limitCheck = await checkCandidateLimit();
    if (!limitCheck.canAdd) {
        Swal.fire({
            title: 'Candidate Limit Reached!',
            text: limitCheck.message || 'You have reached the maximum number of candidates allowed.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4361ee',
            width: 400
        });
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        return;
    }

    // Check for duplicates
    const nicNumber = formData.get('nic_number') || '';
    const passportNumber = formData.get('passport_number') || '';
    const duplicateCheck = await checkDuplicateNicOrPassport(nicNumber, passportNumber);

    if (duplicateCheck.exists) {
        let errorMessage = '';
        if (duplicateCheck.nic_exists && duplicateCheck.passport_exists) {
            errorMessage = 'Both NIC and Passport numbers already exist in our system!';
        } else if (duplicateCheck.nic_exists) {
            errorMessage = 'This NIC number already exists in our system!';
        } else if (duplicateCheck.passport_exists) {
            errorMessage = 'This Passport number already exists in our system!';
        }

        Swal.fire({
            title: 'Duplicate Found!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4361ee',
            width: 400
        });

        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        return;
    }

    // Continue with saving
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch('save_candidate.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4361ee',
                allowOutsideClick: false,
                width: 400
            }).then((result) => {
                if (result.isConfirmed) {
                    form.reset();
                    document.querySelectorAll('.file-upload-text').forEach(el => {
                        el.value = '';
                    });
                    window.scrollTo(0, 0);
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                html: `<div style="text-align:left">
                    <p>${data.message}</p>
                    <small>Please check your inputs and try again</small>
                </div>`,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4361ee',
                width: 400
            });
        }
    } catch (error) {
        Swal.fire({
            title: 'Network Error!',
            text: 'Failed to connect to server. Please check your internet connection.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4361ee',
            width: 400
        });
        console.error('Error:', error);
    } finally {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
}

// Attach event listener
document.getElementById('candidateForm').addEventListener('submit', saveCandidate);
</script>
</body>

</html>