<?php
include "../DB/connection.php";

// Function to get billing reviews with request fullname
function getBillingReviews()
{
    Database::setupConnection();
    $query = "SELECT b.*, r.fullname 
              FROM billing b
              LEFT JOIN request r ON b.request_id = r.id
              ORDER BY b.id DESC";
    $result = Database::$connection->query($query);

    $reviews = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
    }
    return $reviews;
}

$reviews = getBillingReviews();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Reviews - SkyLink</title>
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
            --dark: #FFFFFF;
            --dark-grey: #FFFFFF;
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

        #content nav .fullscreen {
            font-size: 20px;
            cursor: pointer;
            color: var(--dark);
        }

        #content nav .fullscreen:hover {
            color: var(--blue);
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

        #content main .table-data {
            display: flex;
            flex-wrap: wrap;
            grid-gap: 24px;
            margin-top: 24px;
            width: 100%;
            color: var(--dark);
        }

        #content main .table-data .order {
            flex-grow: 1;
            flex-basis: 100%;
            border-radius: 12px;
            background: var(--light);
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        #content main .table-data .order .head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        #content main .table-data .order .head h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
        }

        #content main .table-data .order .head .bx {
            cursor: pointer;
            color: var(--dark-grey);
            font-size: 18px;
            transition: color 0.3s ease;
        }

        #content main .table-data .order .head .bx:hover {
            color: var(--blue);
        }

        /* New Table Design with Smaller Size */
        .billing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 5px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        .billing-table th {
            background: var(--dark);
            color: var(--light);
            font-weight: 600;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid var(--grey);
        }

        .billing-table td {
            background: var(--light);
            padding: 8px 10px;
            vertical-align: middle;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .billing-table tr {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .billing-table tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .billing-table .slip-image {
            max-width: 50px;
            height: auto;
            cursor: pointer;
            border-radius: 4px;
            transition: transform 0.3s ease;
        }

        .billing-table .slip-image:hover {
            transform: scale(1.1);
        }

        .billing-table .action-buttons {
            display: flex;
            gap: 6px;
        }

        .billing-table .action-buttons button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: var(--light);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .billing-table .action-buttons .approve-btn {
            background: var(--blue);
        }

        .billing-table .action-buttons .approve-btn:hover {
            background: var(--primary-color);
            transform: translateY(-1px);
        }

        .billing-table .action-buttons .reject-btn {
            background: var(--red);
        }

        .billing-table .action-buttons .reject-btn:hover {
            background: var(--orange);
            transform: translateY(-1px);
        }

        .billing-table .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 11px;
        }

        .billing-table .status.pending {
            color: var(--yellow);
            background: var(--light-yellow);
        }

        .billing-table .status.approved {
            color: var(--blue);
            background: var(--light-blue);
        }

        .billing-table .status.rejected {
            color: var(--orange);
            background: var(--light-orange);
        }

        /* Modal for Image Preview */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: var(--light);
            font-size: 30px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: var(--blue);
        }

        /* Animations */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Responsive Design */
        @media screen and (max-width: 1024px) {
            #content {
                width: calc(100% - 200px);
                left: 200px;
            }
            #sidebar {
                width: 200px;
            }
            .billing-table th,
            .billing-table td {
                padding: 6px 8px;
                font-size: 11px;
            }
            .billing-table .slip-image {
                max-width: 40px;
            }
        }

        @media screen and (max-width: 768px) {
            #sidebar {
                width: 60px;
            }
            #content {
                width: calc(100% - 60px);
                left: 60px;
            }
            #content nav .nav-link {
                display: none;
            }
            #content main .head-title .left h1 {
                font-size: 24px;
            }
            .billing-table th,
            .billing-table td {
                padding: 5px 6px;
                font-size: 10px;
            }
            .billing-table .slip-image {
                max-width: 35px;
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
            .billing-table {
                display: block;
                overflow-x: auto;
            }
            .billing-table th,
            .billing-table td {
                min-width: 100px;
                padding: 4px 5px;
                font-size: 9px;
            }
            .billing-table .slip-image {
                max-width: 30px;
            }
            .billing-table .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
            .billing-table .action-buttons button {
                width: 100%;
                padding: 6px;
            }
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <div class="brand">
            <img src="../image/logo/icon2.png" height="20" alt="">
            <div class="business-name">SkyLink Main</div>
        </div>
        <ul class="side-menu top">
            <li>
                <a href="adminDashboard.php">
                    <i class='bx bx-home-alt'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="registrationRequest.php">
                    <i class='bx bx-briefcase'></i>
                    <span class="text">Registration Requests</span>
                </a>
            </li>
            <li>
                <a href="paymentGatways.php">
                    <i class='bx bx-file-blank'></i>
                    <span class="text">Payment Gatways</span>
                </a>
            </li>
            <li>
                <a href="subcriptionPlane.php">
                    <i class='bx bx-file'></i>
                    <span class="text">Subscription Plans</span>
                </a>
            </li>
            <li>
                <a href="userManagement.php">
                    <i class='bx bx-file-blank'></i>
                    <span class="text">User Management</span>
                </a>
            </li>
            <li class="active">
                <a href="billingReview.php">
                    <i class='bx bx-user-plus'></i>
                    <span class="text">Billing Reviews</span>
                </a>
            </li>
            <li>
                <a href="reports.php">
                    <i class='bx bx-user-plus'></i>
                    <span class="text">Reports</span>
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

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Billing Reviews</h1>
                    <ul class="breadcrumb">
                        <li><a href="adminDashboard.php">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Billing Reviews</a></li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download Report</span>
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Pending Billing Slips</h3>
                       
                    </div>
                    <table class="billing-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Bill Type</th>
                                <th>Bill Date</th>
                                <th>Due Date</th>
                                <th>Payment Status</th>
                                <th>Bank Slip</th>
                                <th>Slip Status</th>
                                <th>Candidate Purchases</th>
                                <th>Payment Link</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($review['id']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($review['amount'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($review['bill_type']); ?></td>
                                    <td><?php echo htmlspecialchars($review['bill_date']); ?></td>
                                    <td><?php echo htmlspecialchars($review['due_date']); ?></td>
                                    <td><span class="status <?php echo strtolower($review['payment_status']); ?>">
                                            <?php echo htmlspecialchars($review['payment_status']); ?>
                                        </span></td>
                                    <td>
                                        <?php if (!empty($review['bnk_slip'])): ?>
                                            <img height="100" src="../skylinkSysterm/slips/<?php echo htmlspecialchars(basename($review['bnk_slip'])); ?>"
                                                alt="Bank Slip" class="slip-image"
                                                onclick="showImageModal('../skylinkSysterm/slips/<?php echo htmlspecialchars(basename($review['bnk_slip'])); ?>')">
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="status <?php echo strtolower($review['slip_status']); ?>">
                                            <?php echo htmlspecialchars($review['slip_status']); ?>
                                        </span></td>
                                    <td><?php echo htmlspecialchars($review['candidate_purchase_count']); ?></td>
                                    <td><?php echo htmlspecialchars($review['payment_link']); ?></td>
                                    <td><?php echo htmlspecialchars($review['fullname'] ?? 'N/A'); ?></td>
                                    <td class="action-buttons">
                                        <button class="approve-btn"
                                            onclick="approveSlip(<?php echo $review['id']; ?>)">Approve</button>
                                        <button class="reject-btn"
                                            onclick="rejectSlip(<?php echo $review['id']; ?>)">Reject</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($reviews)): ?>
                                <tr>
                                    <td colspan="12" style="text-align: center;">No billing reviews found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Image Preview Modal -->
            <div id="imageModal" class="modal">
                <span class="close" onclick="closeImageModal()">&times;</span>
                <img class="modal-content" id="modalImage">
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
</body>
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
                // Note: Server-side upload logic needed in PHP
            };
            reader.readAsDataURL(file);
        }
    });

    // Fullscreen functionality
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

    // Image Modal
    function showImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = 'flex';
        modalImg.src = src;
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }

    // Functions for approve/reject (to be implemented with PHP)
    function approveSlip(slipId) {
        if (confirm('Are you sure you want to approve this billing slip?')) {
            fetch('processBilling.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=approve&id=${slipId}`
            })
                .then(response => response.text())
                .then(data => {
                    if (data === "Success") {
                        alert('Billing slip approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }
    }

    function rejectSlip(slipId) {
        if (confirm('Are you sure you want to reject this billing slip?')) {
            fetch('processBilling.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=reject&id=${slipId}`
            })
                .then(response => response.text())
                .then(data => {
                    if (data === "Success") {
                        alert('Billing slip rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }
    }
</script>

</html>