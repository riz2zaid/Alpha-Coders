<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Location: index.php");
    exit();
}

// Fetch client ID from session
$clientId = is_array($_SESSION['client']) ? ($_SESSION['client']['id'] ?? null) : ($_SESSION['client'] ?? null);
if (!$clientId || !is_numeric($clientId)) {
    die("Error: Invalid client session.");
}

include "../DB/connection.php";

// Get filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination logic
$results_per_page = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Build query with prepared statements to prevent SQL injection
$conditions = ["v.request_id = ?"];
$params = [$clientId];
$types = 'i';

if (!empty($search)) {
    $conditions[] = "(v.job_position LIKE ? OR c.cname LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Get vacancy data
$query = "SELECT v.id, v.job_position, c.cname AS country, v.created_at, v.country_id
          FROM vacancy v
          INNER JOIN country c ON v.country_id = c.id
          $where_clause
          ORDER BY v.created_at DESC
          LIMIT ?, ?";

$vacancies = [];
try {
    Database::setupConnection();
    $stmt = Database::$connection->prepare($query);
    $stmt->bind_param($types . 'ii', ...array_merge($params, [$start_from, $results_per_page]));
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $vacancies[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching vacancies: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Error fetching vacancies: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Get all countries for edit modal (filtered by request_id)
$countries = [];
try {
    $country_query = "SELECT id, cname FROM country WHERE request_id = ? ORDER BY cname";
    $stmt = Database::$connection->prepare($country_query);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching countries: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Error fetching countries: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM vacancy v INNER JOIN country c ON v.country_id = c.id $where_clause";
try {
    $stmt = Database::$connection->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_records = $stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $results_per_page);
    $stmt->close();
} catch (Exception $e) {
    error_log("Error counting vacancies: " . $e->getMessage());
    $total_records = 0;
    $total_pages = 1;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
            --modal-label-color: rgba(255, 255, 255, 0.85);
            --modal-text-color: rgba(255, 255, 255, 0.9);
            --modal-bg-color: rgba(255, 255, 255, 0.1);
            --modal-border-color: rgba(255, 255, 255, 0.2);
            --modal-placeholder-color: rgba(255, 255, 255, 0.5);
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
            margin-bottom: 24px;
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

        #content main .head-title .btn-add {
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

        #content main .head-title .btn-add:hover {
            background: var(--primary-color);
        }

        #content main .table-container {
            background: var(--light);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        #content main .table-container .table-responsive {
            overflow-x: auto;
        }

        #content main .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        #content main .table-container table th {
            padding: 12px;
            font-size: 14px;
            text-align: left;
            background: var(--primary-color);
            color: var(--light);
            border-bottom: 2px solid var(--grey);
        }

        #content main .table-container table td {
            padding: 12px;
            border-bottom: 1px solid var(--grey);
            color: var(--dark);
        }

        #content main .table-container table tr:hover {
            background: var(--grey);
        }

        #content main .table-container table .text-center {
            text-align: center;
        }

        #content main .table-container .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            padding: 0;
        }

        #content main .table-container .pagination .page-item {
            margin: 0 5px;
        }

        #content main .table-container .pagination .page-link {
            padding: 8px 12px;
            border: 1px solid var(--grey);
            border-radius: 4px;
            color: var(--dark);
            text-decoration: none;
        }

        #content main .table-container .pagination .page-item.active .page-link {
            background: var(--blue);
            color: var(--light);
            border-color: var(--blue);
        }

        #content main .table-container .pagination .page-item.disabled .page-link {
            color: var(--dark-grey);
            background: var(--light);
            cursor: not-allowed;
        }

        #content main .table-container .pagination .page-link:hover {
            background: var(--light-blue);
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 6px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .action-btn.edit {
            color: var(--primary-color);
        }

        .action-btn.edit:hover {
            background: var(--light-blue);
            color: var(--blue);
        }

        .action-btn.delete {
            color: var(--red);
            margin-left: 8px;
        }

        .action-btn.delete:hover {
            background: var(--light-orange);
            color: var(--orange);
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
            background-color: var(--secondary-color);
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

        .editLabal {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: var(--modal-label-color, #4a5568);
            transition: color 0.2s ease;
        }

        .editText,
        .editSelect {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--modal-border-color, #e2e8f0);
            background-color: var(--modal-bg-color, #ffffff);
            color: var(--modal-text-color, #2d3748);
            font-size: 15px;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        .editSelect option {
            background-color: var(--modal-bg-color, #fff);
            color: var(--modal-text-color, #2d3748);
        }

        body.dark .editSelect option {
            background-color: #232946;
            color: #fff;
        }

        body:not(.dark) .editSelect option {
            background-color: #fff;
            color: #2d3748;
        }

        .editText:focus,
        .editSelect:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .editSelect {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 32px;
        }

        .editText::placeholder {
            color: var(--modal-placeholder-color, #a0aec0);
            opacity: 1;
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

            #content main .head-title .btn-add {
                width: 100%;
                text-align: center;
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

            .editText,
            .editSelect {
                padding: 8px 12px;
                font-size: 14px;
            }

            .editLabal {
                font-size: 13px;
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
            <li><a href="clientDashboard.php"><i class='bx bx-home-alt'></i><span class="text">Dashboard</span></a></li>
            <li><a href="addVacancy.php"><i class='bx bx-briefcase'></i><span class="text">Add Job & Country</span></a>
            </li>
            <li class="active"><a href="viewVacancy.php"><i class='bx bx-file'></i><span class="text">View Job &
                        Country</span></a></li>
            <li><a href="addCandidate.php"><i class='bx bx-user-plus'></i><span class="text">Add Candidate</span></a>
            </li>
            <li><a href="candidateDatabase.php"><i class='bx bx-data'></i><span class="text">Candidate
                        Database</span></a></li>
            <li><a href="exportImport.php"><i class='bx bx-transfer'></i><span class="text">Export & Import</span></a>
            </li>
            <li><a href="addTask.php"><i class='bx bx-money'></i><span class="text">Billing Payment</span></a></li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form action="viewVacancy.php" method="GET">
                <div class="form-input">
                    <input type="search" name="search" placeholder="Search for Country name or Job Position . . . ."
                        value="<?php echo htmlspecialchars($search); ?>">
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
                    <h1>View Vacancies</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">View Vacancies</a></li>
                    </ul>
                </div>
                <a href="addVacancy.php" class="btn-add">
                    <i class='bx bx-plus'></i><span>Add New Vacancy</span>
                </a>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job Position</th>
                                <th>Country</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vacancies as $vacancy): ?>
                                <tr data-id="<?php echo $vacancy['id']; ?>">
                                    <td><?php echo htmlspecialchars($vacancy['job_position']); ?></td>
                                    <td><?php echo htmlspecialchars($vacancy['country']); ?></td>
                                    <td><?php echo htmlspecialchars($vacancy['created_at']); ?></td>
                                    <td>
                                        <button class="action-btn edit" title="Edit" data-bs-toggle="modal"
                                            data-bs-target="#editVacancyModal" data-id="<?php echo $vacancy['id']; ?>"
                                            data-job-position="<?php echo htmlspecialchars($vacancy['job_position']); ?>"
                                            data-country-id="<?php echo $vacancy['country_id']; ?>">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="action-btn delete" title="Delete"
                                            onclick="confirmDelete(<?php echo $vacancy['id']; ?>)">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($vacancies)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No vacancies found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </main>

        <div class="modal fade" id="editVacancyModal" tabindex="-1" aria-labelledby="editVacancyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVacancyModalLabel">Edit Vacancy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editVacancyForm">
                            <input type="hidden" id="editVacancyId" name="id">
                            <input type="hidden" id="editRequestId" name="request_id"
                                value="<?php echo htmlspecialchars($clientId); ?>">
                            <div class="mb-3">
                                <label for="editJobPosition" class="editLabal">Job Position</label>
                                <input type="text" class="editText form-control" id="editJobPosition"
                                    name="job_position" placeholder="Enter job position" required>
                            </div>
                            <div class="mb-3">
                                <label for="editCountry" class="editLabal">Country</label>
                                <select class="editSelect form-control" id="editCountry" name="country_id" required>
                                    <option value="">Select a country</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['id']; ?>">
                                            <?php echo htmlspecialchars($country['cname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sidebar toggle
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');
        menuBar.addEventListener('click', () => sidebar.classList.toggle('hide'));

        // Profile dropdown
        const profileDropdown = document.getElementById('profileDropdown');
        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        document.addEventListener('click', () => profileDropdown.classList.remove('active'));

        // Theme switcher
        const switchMode = document.getElementById('switch-mode');
        switchMode.addEventListener('change', () => {
            document.body.classList.toggle('dark', switchMode.checked);
            localStorage.setItem('theme', switchMode.checked ? 'dark' : 'light');
        });
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            switchMode.checked = true;
        }

        // Search functionality for mobile
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

        // Profile image upload
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

        // Fetch business name
        document.addEventListener('DOMContentLoaded', () => {
            fetch('getBusinessName.php')
                .then(response => response.text())
                .then(bname => document.getElementById('businessName').textContent = bname || 'SkyLink Access')
                .catch(() => document.getElementById('businessName').textContent = 'SkyLink Access');
        });

        // Confirm delete function
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this vacancy?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3e64ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                width: 300
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_vacancy.php?id=' + id, { method: 'POST' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`tr[data-id="${id}"]`).remove();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Vacancy deleted successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#3e64ff',
                                    width: 300
                                });
                                if (!document.querySelectorAll('tbody tr').length) {
                                    document.querySelector('tbody').innerHTML = '<tr><td colspan="4" class="text-center">No vacancies found</td></tr>';
                                }
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error deleting vacancy: ' + (data.message || 'Unknown error'),
                                    icon: 'error',
                                    confirmButtonColor: '#3e64ff',
                                    width: 300
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while deleting the vacancy: ' + error.message,
                                icon: 'error',
                                confirmButtonColor: '#3e64ff',
                                width: 300
                            });
                        });
                }
            });
        }

        // Edit vacancy modal
        // Edit vacancy modal
        document.addEventListener('DOMContentLoaded', () => {
            const editModal = new bootstrap.Modal(document.getElementById('editVacancyModal'), {
                backdrop: 'static',
                keyboard: false
            });
            const editButtons = document.querySelectorAll('.action-btn.edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const jobPosition = this.getAttribute('data-job-position');
                    const countryId = this.getAttribute('data-country-id');

                    // Populate form fields
                    document.getElementById('editVacancyId').value = id;
                    document.getElementById('editJobPosition').value = jobPosition || '';
                    const countrySelect = document.getElementById('editCountry');
                    countrySelect.value = countryId || '';

                    // Ensure the select reflects the current value
                    if (!countrySelect.value) {
                        countrySelect.selectedIndex = 0; // Reset to "Select a country" if no match
                    }

                    editModal.show();
                });
            });

            document.getElementById('saveChangesBtn').addEventListener('click', () => {
                const form = document.getElementById('editVacancyForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(form);

                fetch('update_vacancy.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            editModal.hide();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Vacancy updated successfully!',
                                icon: 'success',
                                confirmButtonColor: '#4361ee',
                                width: 300
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Error updating vacancy',
                                icon: 'error',
                                confirmButtonColor: '#4361ee',
                                width: 300
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while updating the vacancy: ' + error.message,
                            icon: 'error',
                            confirmButtonColor: '#4361ee',
                            width: 300
                        });
                    });
            });
        });
    </script>
</body>

</html>