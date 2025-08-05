<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate CV - SkyLink</title>
    <link rel="icon" type="image/x-icon" href="../image/logo/icon.png">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light: #F9F9F9;
            --grey: #eee;
            --dark-grey: #AAAAAA;
            --dark: #342E37;
            --red: #DB504A;
            --yellow: #FFCE26;
            --light-yellow: #FFF2C6;
            --orange: #FD7238;
            --light-orange: #FFE0D3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            background: var(--grey);
            font-family: 'Poppins', sans-serif;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
            --dark-grey: #BBBBBB;
            background: var(--grey);
        }

        body.dark #sidebar,
        body.dark #content nav,
        body.dark .cv-container,
        body.dark .form-container,
        body.dark .cv-section {
            background: var(--light);
        }

        body.dark .cv-section h2,
        body.dark .cv-section p,
        body.dark .cv-section li,
        body.dark .form-group label,
        body.dark .form-group select,
        body.dark .head-title .left h1,
        body.dark .breadcrumb li {
            color: var(--dark);
        }

        body.dark .form-group select {
            background: var(--light);
            border-color: var(--dark-grey);
        }

        /* SIDEBAR STYLES */
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

        #sidebar .brand {
            font-size: 24px;
            height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
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
            text-decoration: none;
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
            text-decoration: none;
        }

        #sidebar .side-menu li a.logout {
            color: var(--red);
        }

        #sidebar .side-menu li a .bx {
            min-width: calc(60px - ((4px + 6px) * 2));
            display: flex;
            justify-content: center;
        }

        /* CONTENT AREA */
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
            background: var(--primary-color);
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
            border: 2px solid var(--primary-color);
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

        /* Theme Toggle */
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
            background: var(--primary-color);
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

        /* Form and CV Container */
        .form-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
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

        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--light);
            color: var(--dark);
        }

        .form-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }

        .form-group button {
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
        }

        .form-group button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* CV Preview */
        .cv-container {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .cv-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .cv-header h1 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .cv-header p {
            color: var(--dark);
            margin: 0.25rem 0;
        }

        .cv-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--grey);
            border-radius: 10px;
        }

        .cv-section h2 {
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .cv-section p,
        .cv-section li {
            color: var(--dark);
            font-size: 1rem;
            line-height: 1.6;
        }

        .cv-section ul {
            list-style-type: disc;
            margin-left: 20px;
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
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="addVacancy.php">
                    <i class='bx bxs-file-plus'></i>
                    <span class="text">Add Vacancy</span>
                </a>
            </li>
            <li>
                <a href="viewVacancy.php">
                    <i class='bx bxs-file'></i>
                    <span class="text">View Vacancy</span>
                </a>
            </li>
            <li>
                <a href="viewCandidate.php">
                    <i class='bx bxs-user-plus'></i>
                    <span class="text">View Candidates</span>
                </a>
            </li>
            <li class="active">
                <a href="cvGenerate.php">
                    <i class='bx bxs-file-pdf'></i>
                    <span class="text">Generate CV</span>
                </a>
            </li>
            <li>
                <a href="adminkyc.php">
                    <i class='bx bxs-check-shield'></i>
                    <span class="text">User KYC's</span>
                </a>
            </li>
            <li>
                <a href="addCities.php">
                    <i class='bx bxs-city'></i>
                    <span class="text">Add Cities</span>
                </a>
            </li>
            <li>
                <a href="adminuserManagement.php">
                    <i class='bx bxs-user-detail'></i>
                    <span class="text">User Management</span>
                </a>
            </li>
            <li>
                <a href="addTask.php">
                    <i class='bx bxs-plus-circle'></i>
                    <span class="text">Add Task</span>
                </a>
            </li>
            <li>
                <a href="taskManagement.php">
                    <i class='bx bxs-task'></i>
                    <span class="text">Task Management</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
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
                    <h1>Generate CV</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Generate CV</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-container">
                <div class="form-group">
                    <label>Select Candidate</label>
                    <select name="candidate">
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                    </select>
                </div>
                <div class="form-group">
                    <button><i class="fas fa-file-pdf me-2"></i>Generate CV</button>
                </div>
            </div>

            <div class="cv-container">
                <div class="cv-header">
                    <h1>John Doe</h1>
                    <p>Contact: +94 112 345 678 | Email: john@example.com</p>
                    <p>Address: 123 Main St, Colombo, Western Province, Sri Lanka</p>
                </div>

                <div class="cv-section">
                    <h2>Personal Information</h2>
                    <p><strong>Date of Birth:</strong> 1990-05-15</p>
                    <p><strong>Gender:</strong> Male</p>
                    <p><strong>Civil Status:</strong> Single</p>
                    <p><strong>NIC Number:</strong> 123456789V</p>
                    <p><strong>Passport Number:</strong> N1234567 (Valid, Issued: 2020-01-01, Expires: 2030-01-01, Country: Sri Lanka)</p>
                </div>

                <div class="cv-section">
                    <h2>Education & Qualifications</h2>
                    <p><strong>Highest Qualification:</strong> Bachelor's Degree</p>
                    <p><strong>Field of Study:</strong> Computer Science</p>
                    <p><strong>Institution:</strong> University of Colombo</p>
                    <p><strong>Year Completed:</strong> 2012</p>
                </div>

                <div class="cv-section">
                    <h2>Work Experience</h2>
                    <p><strong>Company Name:</strong> ABC Corp</p>
                    <p><strong>Position Held:</strong> Software Engineer</p>
                    <p><strong>Duration:</strong> 2013-2015</p>
                    <p><strong>Country (Abroad):</strong> UK</p>
                    <p><strong>Reason for Leaving:</strong> Better Opportunity</p>
                </div>

                <div class="cv-section">
                    <h2>Skills</h2>
                    <ul>
                        <li>Languages Spoken: English, Sinhala</li>
                        <li>Work Skills: Programming, Typing</li>
                        <li>Computer Skills: Advanced</li>
                    </ul>
                </div>

                <div class="cv-section">
                    <h2>Emergency Contact</h2>
                    <p><strong>Name:</strong> Jane Doe</p>
                    <p><strong>Relationship:</strong> Mother</p>
                    <p><strong>Mobile:</strong> +94 112 345 679</p>
                    <p><strong>Alternate Number:</strong> +94 112 345 680</p>
                    <p><strong>Address:</strong> 456 Main St, Colombo</p>
                </div>

                <div class="cv-section">
                    <h2>Medical & Health Information</h2>
                    <p><strong>Blood Group:</strong> A+</p>
                    <p><strong>Allergies:</strong> None</p>
                    <p><strong>Chronic Illness:</strong> None</p>
                    <p><strong>Physical Fitness Status:</strong> Fit</p>
                </div>

                <div class="cv-section">
                    <h2>Police & Legal Clearance</h2>
                    <p><strong>Issue Date:</strong> 2023-01-01</p>
                    <p><strong>Expiry Date:</strong> 2024-01-01</p>
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
        });

        // Form submission
        function saveCandidate(e) {
            e.preventDefault();

            const form = document.querySelector('form');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;

            fetch('save_candidate.php', {
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
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#4361ee'
                        }).then(() => {
                            form.reset();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            html: `<div style="text-align:left">
                                <p>${data.message}</p>
                                <small>Check server logs for more details</small>
                            </div>`,
                            icon: 'error',
                            confirmButtonColor: '#4361ee'
                        });
                        console.error('Error details:', data);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Network Error!',
                        text: 'Failed to connect to server. Check console for details.',
                        icon: 'error',
                        confirmButtonColor: '#4361ee'
                    });
                    console.error('Fetch error:', error);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        }

        document.querySelector('form').addEventListener('submit', saveCandidate);
    </script>
</body>
</html>