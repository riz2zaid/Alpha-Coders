<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check session and redirect if invalid
if (!isset($_SESSION['client'])) {
    error_log("Session 'client' not set. Redirecting to index.php");
    header("Location: index.php");
    exit();
}

// Get client ID from session
$clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);
if (!$clientId || !is_numeric($clientId)) {
    error_log("Invalid client ID: " . var_export($clientId, true));
    header("Location: index.php");
    exit();
}

include "../DB/connection.php";

// Fetch candidates for the logged-in client
$candidates = [];
try {
    Database::setupConnection();
    if (!Database::$connection) {
        throw new Exception("Database connection is null");
    }

    $query = "SELECT c.*, 
              g.name AS gender_name,
              cs.status AS civil_status,
              ps.status AS passport_status,
              co.option AS candidate_option,
              comp.skill AS computer_skill
              FROM candidates c
              LEFT JOIN gender g ON c.gender_id = g.id
              LEFT JOIN clivil_status cs ON c.clivil_status_id = cs.id
              LEFT JOIN passport_status ps ON c.passport_status_id = ps.id
              LEFT JOIN candidate_option co ON c.candidate_option_id = co.id
              LEFT JOIN computer_skill comp ON c.computer_skill_id = comp.id
              WHERE c.request_id = ?
              ORDER BY c.register_date DESC";

    $stmt = Database::$connection->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . Database::$connection->error);
    }
    $stmt->bind_param('i', $clientId);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    $stmt->close();

    // Debugging: Log the number of candidates fetched
    error_log("Fetched " . count($candidates) . " candidates for client ID: $clientId");
} catch (Exception $e) {
    error_log("Error fetching candidates: " . $e->getMessage());
    $candidates = []; // Ensure candidates is empty on error
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Database - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        a { text-decoration: none; }
        li { list-style: none; }
        html { overflow-x: hidden; }

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

        /* Sidebar styles */
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

        #sidebar::-webkit-scrollbar { display: none; }
        #sidebar.hide { width: 60px; }
        #sidebar.hide .business-name { display: none; }

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

        #sidebar .side-menu.top li.active a { color: var(--blue); }
        #sidebar.hide .side-menu li a { width: calc(48px - (4px * 2)); transition: width .3s ease; }
        #sidebar .side-menu li a.logout { color: var(--red); }
        #sidebar .side-menu.top li a:hover { color: var(--blue); }
        #sidebar .side-menu li a .bx {
            min-width: calc(60px - ((4px + 6px) * 2));
            display: flex;
            justify-content: center;
        }

        /* Main content styles */
        #content {
            position: relative;
            width: calc(100% - 280px);
            left: 280px;
            transition: .3s ease;
        }

        #sidebar.hide~#content { width: calc(100% - 60px); left: 60px; }

        /* Navigation styles */
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

        #content nav a { color: var(--dark); }
        #content nav .bx.bx-menu { cursor: pointer; color: var(--dark); font-size: 24px; }
        #content nav .nav-link { font-size: 16px; transition: .3s ease; }
        #content nav .nav-link:hover { color: var(--blue); }
        #content nav form { max-width: 400px; width: 100%; margin-right: auto; }
        #content nav form .form-input { display: flex; align-items: center; height: 36px; }
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

        #content nav .notification { font-size: 20px; position: relative; }
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

        .profile-dropdown { position: relative; }
        .profile-btn { display: flex; align-items: center; cursor: pointer; }
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

        .dropdown-item:hover { background: var(--grey); }
        .dropdown-item i { font-size: 18px; }
        .dropdown-divider { height: 1px; background: var(--grey); margin: 5px 0; }
        .upload-profile { display: none; }

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

        /* Main content */
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
            gap: 16px;
        }

        #content main .head-title .left .breadcrumb li { color: var(--dark); }
        #content main .head-title .left .breadcrumb li a { color: var(--dark-grey); pointer-events: none; }
        #content main .head-title .left .breadcrumb li a.active { color: var(--blue); pointer-events: unset; }

        .btn-download {
            background: var(--primary-color);
            color: var(--light);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-download:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            color: var(--light);
        }

        /* Table styles */
        .data-table-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 15px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid var(--grey);
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--grey);
            vertical-align: middle;
        }

        .table tr { background-color: transparent; }
        .table tr:nth-child(even) { background-color: rgba(67, 97, 238, 0.05); }
        .table tr:hover { background-color: rgba(67, 97, 238, 0.2); }
        .table th:first-child, .table td:first-child { border-top-left-radius: 15px; }
        .table th:last-child, .table td:last-child { border-top-right-radius: 15px; }

        .action-btns { display: flex; gap: 8px; }
        .btn-action {
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: var(--light);
        }

        .btn-edit { background-color: var(--warning-color); color: var(--dark); }
        .btn-delete { background-color: var(--danger-color); }
        .btn-view { background-color: var(--primary-color); }
        .btn-action i { margin-right: 5px; }

        /* Image styles */
        .document-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            border: 1px solid var(--grey);
            border-radius: 5px;
        }

        .download-btn {
            padding: 4px 8px;
            background-color: var(--success-color);
            color: var(--light);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .download-btn:hover { background-color: #218838; }

        /* Zoom Modal styles */
        .zoom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .zoom-modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }

        .zoom-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: var(--light);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 18px;
            cursor: pointer;
        }

        .close-btn:hover { background: #c82333; }

        /* Status badges */
        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active { background-color: rgba(40, 167, 69, 0.2); color: var(--success-color); }
        .badge-inactive { background-color: rgba(220, 53, 69, 0.2); color: var(--danger-color); }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 10px;
            background: var(--light);
            border-radius: 10px;
        }

        .pagination button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background: var(--secondary-color);
            color: var(--blue);
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination button:disabled { background: var(--grey); cursor: not-allowed; }
        .pagination button:hover:not(:disabled) { background: var(--secondary-color); }
        .pagination span { font-size: 14px; color: var(--dark); }

        /* Dark mode styles */
        body.dark .data-table-container { background: #070722ff; }
        body.dark .table th { background-color: #06063bff; }
        body.dark .table tr:nth-child(even) { background-color: rgba(246, 242, 255, 0.1); }
        body.dark .table tr:hover { background-color: #08083dff; color: white; }
        body.dark .table td { border-bottom: 1px solid #2a2a4a; color: white; }
        body.dark .pagination { background: #1a1a3d; }
        body.dark .pagination span { color: var(--light); }
        body.dark .zoom-modal { background: rgba(0, 0, 0, 0.9); }

        /* Responsive styles */
        @media (max-width: 1200px) {
            #content main { padding: 24px 16px; }
            .data-table-container { padding: 1.5rem; }
        }

        @media (max-width: 992px) {
            #sidebar { width: 200px; }
            #content { width: calc(100% - 200px); left: 200px; }
            .table th, .table td { padding: 10px 12px; }
        }

        @media (max-width: 768px) {
            #sidebar { width: 60px; }
            #content { width: calc(100% - 60px); left: 60px; }
            .head-title .left h1 { font-size: 28px; }
            .btn-download span { display: none; }
            .btn-download i { margin-right: 0; }
            .action-btns { flex-direction: column; gap: 5px; }
            .btn-action { width: 100%; }
            .document-preview { width: 80px; height: 80px; }
        }

        @media (max-width: 576px) {
            #content nav form .form-input input { display: none; }
            #content nav form .form-input button {
                width: auto;
                height: auto;
                background: transparent;
                border-radius: none;
                color: var(--dark);
            }
            #content nav form.show .form-input input { display: block; width: 100%; }
            #content nav form.show .form-input button {
                width: 36px;
                height: 100%;
                border-radius: 0 36px 36px 0;
                color: var(--light);
                background: var(--blue);
            }
            #content nav form.show~.notification,
            #content nav form.show~.profile,
            #content nav form.show~.logout { display: none; }
            .data-table-container { padding: 1rem; }
            .table th, .table td { padding: 8px 10px; font-size: 14px; }
            .btn-action span { display: none; }
            .btn-action i { margin-right: 0; }
            .document-preview { width: 60px; height: 60px; }
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Form styles */
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(67, 97, 238, 0.05);
            border-radius: 8px;
        }

        .form-section h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--grey);
        }

        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark);
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--grey);
            border-radius: 4px;
            background: var(--light);
            color: var(--dark);
        }
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--grey);
            border-radius: 4px;
            background: var(--light);
            color: var(--dark);
            min-height: 80px;
        }
        .current-file { font-size: 12px; color: var(--dark-grey); margin-top: 5px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }

        /* Dark mode form styles */
        body.dark .form-section { background: rgba(58, 12, 163, 0.1); }
        body.dark .form-group input[type="text"],
        body.dark .form-group input[type="date"],
        body.dark .form-group input[type="email"],
        body.dark .form-group input[type="tel"],
        body.dark .form-group select,
        body.dark .form-group textarea {
            background: #1a1a3d;
            color: white;
            border-color: #2a2a4a;
        }
        body.dark .form-group label { color: white; }

        /* Responsive form styles */
        @media (max-width: 768px) {
            .form-row { flex-direction: column; gap: 10px; }
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: var(--light);
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            padding: 20px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--grey);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-title { font-size: 24px; color: var(--dark); }
        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--dark);
        }

        .modal-body { padding: 10px 0; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; }
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-secondary { background: var(--grey); color: var(--dark); }
        .btn-primary { background: var(--primary-color); color: var(--light); }

        body.dark .modal-content { background: #1a1a3d; color: white; }
        body.dark .modal-header { border-bottom: 1px solid #2a2a4a; }
        body.dark .modal-title { color: white; }
        body.dark .btn-close { color: white; }
        body.dark .btn-secondary { background: #2a2a4a; color: white; }
    </style>
</head>

<body>
    <section id="sidebar">
        <div class="brand">
            <img src="../image/logo/icon2.png" height="20" alt="">
            <div class="business-name" id="businessName">SkyLink</div>
            <div class="made-by">Made by SkyLink</div>
        </div>
        <ul class="side-menu top">
            <li><a href="clientDashboard.php"><i class='bx bx-home-alt'></i><span class="text">Dashboard</span></a></li>
            <li><a href="addVacancy.php"><i class='bx bx-briefcase'></i><span class="text">Add Job & Country</span></a></li>
            <li><a href="viewVacancy.php"><i class='bx bx-file'></i><span class="text">View Job & Country</span></a></li>
            <li><a href="addCandidate.php"><i class='bx bx-user-plus'></i><span class="text">Add Candidate</span></a></li>
            <li class="active"><a href="candidateDatabase.php"><i class='bx bx-data'></i><span class="text">Candidate Database</span></a></li>
            <li><a href="exportImport.php"><i class='bx bx-transfer'></i><span class="text">Export & Import</span></a></li>
            <li><a href="addTask.php"><i class='bx bx-money'></i><span class="text">Billing Payment</span></a></li>
           
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#" id="searchForm">
                <div class="form-input">
                    <input type="search" id="searchInput" placeholder="Search candidates...">
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
                    <h1>Candidate Database</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Candidate Database</a></li>
                    </ul>
                </div>
                <a href="addCandidate.php" class="btn-download">
                    <i class='bx bx-plus'></i>
                    <span class="text">Add New Candidate</span>
                </a>
            </div>

            <div class="data-table-container">
                <?php if (empty($candidates)): ?>
                    <p style="text-align: center; color: var(--dark);">No candidates found for this client.</p>
                <?php else: ?>
                    <div class="table-wrapper" id="tableWrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Personal Information</th>
                                    <th>Profile</th>
                                    <th>Address & Contact</th>
                                    <th>NIC</th>
                                    <th>NIC Front</th>
                                    <th>NIC Back</th>
                                    <th>Passport Details</th>
                                    <th>Passport Scan</th>
                                    <th>Guardian</th>
                                    <th>Education</th>
                                    <th>Certificate</th>
                                    <th>Skill</th>
                                    <th>Technical Training</th>
                                    <th>Work Experience</th>
                                    <th>Experience Letter</th>
                                    <th>Medical Details</th>
                                    <th>Medical Clearance</th>
                                    <th>COVID Card</th>
                                    <th>Vision Test Report</th>
                                    <th>Police Certificate</th>
                                    <th>Police Issue/Expiry</th>
                                    <th>Other Documents</th>
                                    <th>CV</th>
                                    <th>Agreement</th>
                                    <th>Contract</th>
                                    <th>Undertaking</th>
                                    <th>GS Letter</th>
                                    <th>Visa</th>
                                    <th>Candidate Option</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                    <div class="pagination" id="pagination"></div>
                <?php endif; ?>
            </div>
        </main>
    </section>

    <!-- View Candidate Modal -->
    <div class="modal" id="viewCandidateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Candidate Details</h5>
                <button type="button" class="btn-close" onclick="closeModal('viewCandidateModal')">×</button>
            </div>
            <div class="modal-body" id="candidateDetails"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('viewCandidateModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Candidate Modal -->
    <div class="modal" id="editCandidateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Candidate</h5>
                <button type="button" class="btn-close" onclick="closeModal('editCandidateModal')">×</button>
            </div>
            <div class="modal-body" id="editCandidateForm"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editCandidateModal')">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCandidateChanges">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Zoom Modal -->
    <div class="zoom-modal" id="zoomModal">
        <div class="zoom-modal-content">
            <button class="close-btn" onclick="closeZoomModal()">×</button>
            <img src="" alt="Zoomed Image" class="zoom-image" id="zoomImage">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Pagination and table initialization
        let candidates = <?php echo json_encode($candidates); ?>;
        const rowsPerPage = 5;
        let currentPage = 1;

        function displayCandidates(page) {
            const tableBody = document.getElementById('tableBody');
            if (!tableBody) return;

            tableBody.innerHTML = '';
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedCandidates = candidates.slice(start, end);

            if (paginatedCandidates.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="30" style="text-align: center;">No candidates found.</td></tr>';
                updatePagination();
                return;
            }

            paginatedCandidates.forEach(candidate => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${candidate.id || ''}</td>
                    <td>
                        <strong>${htmlspecialchars(candidate.fullname || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.dob || '')}</small><br>
                        <small>${htmlspecialchars(candidate.gender_name || '')}</small><br>
                        <small>${htmlspecialchars(candidate.civil_status || '')}</small>
                    </td>
                    <td>
                        ${candidate.profile_photo ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.profile_photo)}" alt="Profile" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.profile_photo)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.profile_photo)}', 'profile_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Profile Image</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.address || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.district_province || '')}</small><br>
                        ${htmlspecialchars(candidate.contact_number || '')}<br>
                        ${candidate.email ? `<small>${htmlspecialchars(candidate.email)}</small>` : ''}
                    </td>
                    <td>
                        ${candidate.nic_no ? `NIC: ${htmlspecialchars(candidate.nic_no)}<br>` : ''}
                        ${candidate.issue_date ? `Issue: ${htmlspecialchars(candidate.issue_date)}` : ''}
                    </td>
                    <td>
                        ${candidate.nic_front ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.nic_front)}" alt="NIC Front" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.nic_front)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.nic_front)}', 'nic_front_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No NIC Front Image</span>'}
                    </td>
                    <td>
                        ${candidate.nic_back ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.nic_back)}" alt="NIC Back" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.nic_back)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.nic_back)}', 'nic_back_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No NIC Back Image</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.passport_no || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.country_issue || '')}</small><br>
                        <small>${htmlspecialchars(candidate.passport_status || '')}</small><br>
                        <small>${htmlspecialchars(candidate.passport_issue || '')}</small><br>
                        <small>${htmlspecialchars(candidate.passport_expiry || '')}</small>
                    </td>
                    <td>
                        ${candidate.passport_scan ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.passport_scan)}" alt="Passport Scan" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.passport_scan)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.passport_scan)}', 'passport_scan_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Passport Scan Image</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.name || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.relationship || '')}</small><br>
                        <small>${htmlspecialchars(candidate.mobile_no || '')}</small><br>
                        <small>${htmlspecialchars(candidate.alternative_no || '')}</small><br>
                        <small>${htmlspecialchars(candidate.o_address || '')}</small>
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.highest_qualification || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.fieldofstudy || '')}</small><br>
                        <small>${htmlspecialchars(candidate.institute_name || '')}</small><br>
                        <small>${htmlspecialchars(candidate.complete_year || '')}</small>
                    </td>
                    <td>
                        ${candidate.certificate_image ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.certificate_image)}" alt="Certificate" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.certificate_image)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.certificate_image)}', 'certificate_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Certificate</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.language || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.work_skill || '')}</small><br>
                        <small>${htmlspecialchars(candidate.computer_skill || '')}</small>
                    </td>
                    <td>
                        ${candidate.technical_image ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.technical_image)}" alt="Technical Training" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.technical_image)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.technical_image)}', 'technical_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Technical Training</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.company_name || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.position || '')}</small><br>
                        <small>${htmlspecialchars(candidate.duration || '')}</small><br>
                        <small>${htmlspecialchars(candidate.country_work || '')}</small><br>
                        <small>${htmlspecialchars(candidate.reasonof_leave || '')}</small>
                    </td>
                    <td>
                        ${candidate.experience_letter ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.experience_letter)}" alt="Experience Letter" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.experience_letter)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.experience_letter)}', 'experience_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Experience Letter</span>'}
                    </td>
                    <td>
                        <strong>${htmlspecialchars(candidate.blood || '')}</strong><br>
                        <small>${htmlspecialchars(candidate.allergies || '')}</small><br>
                        <small>${htmlspecialchars(candidate.chronic || '')}</small><br>
                        <small>${htmlspecialchars(candidate.fitness_status || '')}</small>
                    </td>
                    <td>
                        ${candidate.medical_report ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.medical_report)}" alt="Medical Report" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.medical_report)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.medical_report)}', 'medical_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Medical Report</span>'}
                    </td>
                    <td>
                        ${candidate.covid_card ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.covid_card)}" alt="COVID Card" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.covid_card)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.covid_card)}', 'covid_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No COVID Card</span>'}
                    </td>
                    <td>
                        ${candidate.vision_test ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.vision_test)}" alt="Vision Test Report" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.vision_test)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.vision_test)}', 'vision_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Vision Test Report</span>'}
                    </td>
                    <td>
                        ${candidate.police_certificate ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.police_certificate)}" alt="Police Certificate" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.police_certificate)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.police_certificate)}', 'police_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Police Certificate</span>'}
                    </td>
                    <td>
                        ${htmlspecialchars(candidate.police_issuedate || '')} / ${htmlspecialchars(candidate.police_expiredate || '')}
                    </td>
                    <td>
                        ${candidate.other_document ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.other_document)}" alt="Other Documents" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.other_document)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.other_document)}', 'other_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No Other Documents</span>'}
                    </td>
                    <td>
                        ${candidate.cv ? `<a href="../Uploads/candidates/${htmlspecialchars(candidate.cv)}" target="_blank">View CV</a>` : '<span>No CV</span>'}
                    </td>
                    <td>
                        ${candidate.agreement ? `<a href="../Uploads/candidates/${htmlspecialchars(candidate.agreement)}" target="_blank">View Agreement</a>` : '<span>No Agreement</span>'}
                    </td>
                    <td>
                        ${candidate.contract ? `<a href="../Uploads/candidates/${htmlspecialchars(candidate.contract)}" target="_blank">View Contract</a>` : '<span>No Contract</span>'}
                    </td>
                    <td>
                        ${candidate.undertaking_letter ? `<a href="../Uploads/candidates/${htmlspecialchars(candidate.undertaking_letter)}" target="_blank">View Undertaking</a>` : '<span>No Undertaking</span>'}
                    </td>
                    <td>
                        ${candidate.gs_letter ? `<img src="../Uploads/candidates/${htmlspecialchars(candidate.gs_letter)}" alt="GS Letter" class="document-preview" onclick="showZoomImage('../Uploads/candidates/${htmlspecialchars(candidate.gs_letter)}')"><button class="download-btn" onclick="downloadImage('../Uploads/candidates/${htmlspecialchars(candidate.gs_letter)}', 'medical_${candidate.id}.jpg')"><i class="fas fa-download"></i></button>` : '<span>No GS Letter</span>'}
                    </td>
                    <td>
                        ${candidate.visa ? `<a href="../Uploads/candidates/${htmlspecialchars(candidate.visa)}" target="_blank">View Visa</a>` : '<span>No Visa</span>'}
                    </td>
                    <td>
                        <span class="badge ${candidate.candidate_option_id == 1 ? 'badge-active' : 'badge-inactive'}">
                            ${htmlspecialchars(candidate.candidate_option || '')}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="#" class="btn-action btn-edit" onclick="editCandidate(${candidate.id}); return false;">
                                <i class="fas fa-edit"></i><span>Edit</span>
                            </a>
                            <a href="#" class="btn-action btn-view" onclick="viewCandidate(${candidate.id}); return false;">
                                <i class="fas fa-eye"></i><span>View</span>
                            </a>
                            <a href="#" class="btn-action btn-delete" onclick="confirmDelete(${candidate.id}); return false;">
                                <i class="fas fa-trash"></i><span>Delete</span>
                            </a>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            updatePagination();
        }

        function updatePagination() {
            const pagination = document.getElementById('pagination');
            if (!pagination) return;

            const totalPages = Math.ceil(candidates.length / rowsPerPage);
            pagination.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayCandidates(currentPage);
                }
            };
            pagination.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = i === currentPage ? 'active' : '';
                pageBtn.onclick = () => {
                    currentPage = i;
                    displayCandidates(currentPage);
                };
                pagination.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayCandidates(currentPage);
                }
            };
            pagination.appendChild(nextBtn);
        }

        // Search functionality
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            candidates = <?php echo json_encode($candidates); ?>.filter(candidate =>
                (candidate.fullname || '').toLowerCase().includes(searchTerm) ||
                (candidate.contact_number || '').includes(searchTerm) ||
                (candidate.nic_no || '').toLowerCase().includes(searchTerm) ||
                (candidate.passport_no || '').toLowerCase().includes(searchTerm)
            );
            currentPage = 1;
            displayCandidates(currentPage);
        });

        // View candidate details
        function viewCandidate(id) {
            $.ajax({
                url: 'getCandidateDetails.php',
                type: 'GET',
                data: { id: id },
                beforeSend: function () {
                    $('#candidateDetails').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading details...</p></div>');
                },
                success: function (response) {
                    $('#candidateDetails').html(response);
                    openModal('viewCandidateModal');
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load candidate details: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
        }

        // Edit candidate
        function editCandidate(id) {
            if (!id) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid candidate ID',
                    icon: 'error',
                    confirmButtonColor: '#4361ee'
                });
                return;
            }

            $.ajax({
                url: 'getCandidateEditForm.php?id=' + id,
                type: 'GET',
                beforeSend: function () {
                    $('#editCandidateForm').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading form...</p></div>');
                },
                success: function (response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success === false) {
                            throw new Error(data.message);
                        }
                    } catch (e) {
                        $('#editCandidateForm').html(response);
                        openModal('editCandidateModal');
                        $('#editCandidateForm form').on('submit', function (e) {
                            e.preventDefault();
                            saveCandidateChanges();
                        });
                        return;
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load edit form: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
        }

        // Save edited candidate
        function saveCandidateChanges() {
            const form = $('#editCandidateForm form')[0];
            if (!form) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Form not found',
                    icon: 'error',
                    confirmButtonColor: '#4361ee'
                });
                return;
            }

            const formData = new FormData(form);
            const saveBtn = $('#saveCandidateChanges');

            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: 'updateCandidate.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#4361ee'
                            }).then(() => {
                                closeModal('editCandidateModal');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to update candidate',
                                icon: 'error',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Invalid server response',
                            icon: 'error',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update candidate: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                },
                complete: function () {
                    saveBtn.prop('disabled', false).html('Save Changes');
                }
            });
        }

        // Confirm delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteCandidate(id);
                }
            });
        }

        // Delete candidate
        function deleteCandidate(id) {
            $.ajax({
                url: 'deleteCandidate.php',
                type: 'POST',
                data: { id: id },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing',
                        html: 'Deleting candidate...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#4361ee'
                            }).then(() => {
                                candidates = candidates.filter(candidate => candidate.id !== id);
                                displayCandidates(currentPage);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to delete candidate',
                                icon: 'error',
                                confirmButtonColor: '#4361ee'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Invalid server response',
                            icon: 'error',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete candidate: ' + error,
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                }
            });
        }

        // Modal functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        }

        // Zoom modal functions
        function showZoomImage(src) {
            const zoomImage = document.getElementById('zoomImage');
            if (zoomImage) {
                zoomImage.src = src;
                document.getElementById('zoomModal').style.display = 'flex';
            }
        }

        function closeZoomModal() {
            document.getElementById('zoomModal').style.display = 'none';
        }

        // Download image function
        function downloadImage(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Sidebar toggle
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');
        if (menuBar && sidebar) {
            menuBar.addEventListener('click', function () {
                sidebar.classList.toggle('hide');
            });
        }

        // Profile dropdown toggle
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown) {
            profileDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
                this.classList.toggle('active');
            });
        }

        document.addEventListener('click', function () {
            if (profileDropdown) profileDropdown.classList.remove('active');
        });

        // Dark mode toggle
        const switchMode = document.getElementById('switch-mode');
        if (switchMode) {
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
        }

        // Mobile search toggle
        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        if (searchButton && searchButtonIcon && searchForm) {
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
        }

        if (window.innerWidth < 768) {
            if (sidebar) sidebar.classList.add('hide');
        }

        window.addEventListener('resize', function () {
            if (this.innerWidth > 576 && searchButtonIcon && searchForm) {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
            }
        });

        // Profile image upload
        const uploadProfile = document.getElementById('uploadProfile');
        const profileImage = document.getElementById('profileImage');
        if (uploadProfile && profileImage) {
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
        }

        // Load business name
        document.addEventListener('DOMContentLoaded', function () {
            const businessName = document.getElementById('businessName');
            if (businessName) {
                businessName.textContent = 'SkyLink Access';
            }
            displayCandidates(currentPage);
        });

        // Business name fetch
        document.addEventListener('DOMContentLoaded', function () {
            fetch('getBusinessName.php')
                .then(response => response.text())
                .then(bname => {
                    const businessName = document.getElementById('businessName');
                    if (businessName) {
                        businessName.textContent = bname || 'SkyLink Access';
                    }
                })
                .catch(error => {
                    console.error('Error fetching business name:', error);
                    const businessName = document.getElementById('businessName');
                    if (businessName) {
                        businessName.textContent = 'SkyLink Access';
                    }
                });

            // File upload name display
            const fileInputs = document.querySelectorAll('.file-upload-input');
            fileInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const fileName = this.files[0] ? this.files[0].name : '';
                    const textField = document.getElementById(this.id + 'Name');
                    if (textField) textField.value = fileName;
                });
            });
        });

        // HTML escaping function
        function htmlspecialchars(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>