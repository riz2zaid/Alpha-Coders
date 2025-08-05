<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyLink Access</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="logo.png" rel="icon">
    <style>
        :root {
            /* Modern color palette */
            --primary-color: #4361ee;
            --primary-light: #e0e7ff;
            --secondary-color: #3a0ca3;
            --accent-color: #7209b7;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            
            /* Light theme */
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --hover-bg: #f1f5f9;
            --divider-color: #f1f5f9;
            
            /* Top bar */
            --topbar-bg: #ffffff;
            --topbar-text: #1e293b;
            --topbar-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            
            /* Sidebar */
            --sidebar-width: 260px;
            --sidebar-shadow: 4px 0 10px -3px rgba(0, 0, 0, 0.05);
            
            /* Transitions */
            --transition-speed: 0.2s;
            --transition-easing: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Dark theme */
        [data-theme="dark"] {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --sidebar-bg: #1e293b;
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --hover-bg: #334155;
            --divider-color: #334155;
            
            --topbar-bg: #1e293b;
            --topbar-text: #f8fafc;
            --topbar-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color var(--transition-speed) var(--transition-easing), 
                        color var(--transition-speed) var(--transition-easing),
                        border-color var(--transition-speed) var(--transition-easing);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Top Navigation Bar - Modern Style */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background-color: var(--topbar-bg);
            color: var(--topbar-text);
            display: flex;
            align-items: center;
            padding: 0 24px;
            box-shadow: var(--topbar-shadow);
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .business-name {
            font-size: 1.65rem;
            font-weight: 700;
            background: linear-gradient(90deg, #4361ee, #3a0ca3, #7209b7);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientAnimation 6s ease infinite;
            margin-right: 20px;
        }

        .datetime {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .toggle-btn {
            font-size: 1.25rem;
            cursor: pointer;
            background: none;
            border: none;
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            transition: all var(--transition-speed) var(--transition-easing);
        }

        .toggle-btn:hover {
            background-color: var(--hover-bg);
            transform: rotate(90deg);
        }

        .search-bar {
            position: relative;
            margin-left: 8px;
        }

        .search-bar input {
            padding: 10px 15px 10px 40px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--bg-color);
            color: var(--text-color);
            width: 240px;
            outline: none;
            font-size: 0.9rem;
            transition: all var(--transition-speed) var(--transition-easing);
        }

        .search-bar input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .theme-toggle:hover {
            background-color: var(--hover-bg);
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all var(--transition-speed) var(--transition-easing);
        }

        .profile-btn:hover {
            background-color: var(--hover-bg);
        }

        .profile-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 220px;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all var(--transition-speed) var(--transition-easing);
            z-index: 101;
            border: 1px solid var(--border-color);
        }

        .profile-dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 10px 16px;
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            transition: all var(--transition-speed) var(--transition-easing);
        }

        .dropdown-item:hover {
            background-color: var(--hover-bg);
            padding-left: 20px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--divider-color);
            margin: 8px 0;
        }

        /* Sidebar - Modern Style */
        .sidebar {
            position: fixed;
            top: 34px;
            left: calc(-1 * var(--sidebar-width));
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--text-color);
            transition: left var(--transition-speed) var(--transition-easing);
            z-index: 99;
            overflow-y: auto;
            box-shadow: var(--sidebar-shadow);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding-top: 16px;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar-nav {
            padding: 16px 0;
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 4px;
            padding: 0 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            transition: all var(--transition-speed) var(--transition-easing);
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--text-color);
            transform: translateX(4px);
        }

        .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 500;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-top: 64px;
            margin-left: 0;
            padding: 24px;
            flex: 1;
            transition: margin-left var(--transition-speed) var(--transition-easing);
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Animations */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (min-width: 768px) {
            .sidebar {
                left: 0;
            }

            .main-content {
                margin-left: var(--sidebar-width);
            }

            .toggle-btn {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .search-bar input {
                width: 180px;
            }
            
            .sidebar.open + .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .topbar {
                padding: 0 16px;
            }
            
            .business-name {
                display: none;
            }
            
            .search-bar {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                padding: 12px;
                background-color: var(--topbar-bg);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                z-index: 102;
                border-bottom: 1px solid var(--border-color);
            }
            
            .search-bar.active {
                display: block;
                animation: fadeIn 0.2s ease-out;
            }
            
            .search-bar input {
                width: 100%;
            }
            
            .search-toggle {
                display: flex !important;
            }
        }

        /* Utility classes */
        .hidden {
            display: none !important;
        }

        .search-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.25rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
        }

        .search-toggle:hover {
            background-color: var(--hover-bg);
        }

        /* Modern focus styles */
        *:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <img src="../image/logo/icon2.png" height="40" alt="">
            <div class="business-name" id="businessName">SkyLink</div>
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <div class="topbar-right">
            <button class="search-toggle" onclick="toggleSearch()">
                <i class="bi bi-search"></i>
            </button>
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="bi bi-moon"></i>
            </button>
            <div class="profile-dropdown" id="profileDropdown">
                <button class="profile-btn">
                    <div class="profile-img">U</div>
                    <span>User</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="clientDashboard.php" class="nav-link active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="addVacancy.php" class="nav-link">
                    <i class="bi bi-file-earmark-plus"></i>
                    <span>Add Vacancy</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="viewVacancy.php" class="nav-link">
                    <i class="bi bi-files"></i>
                    <span>View Vacancy</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="registration.php" class="nav-link">
                    <i class="bi bi-person-plus"></i>
                    <span>Registration</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="adminkyc.php" class="nav-link">
                    <i class="bi bi-patch-check"></i>
                    <span>User KYC's</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="addCities.php" class="nav-link">
                    <i class="bi bi-geo-plus"></i>
                    <span>Add Cities</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="adminuserManagement.php" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="addTask.php" class="nav-link">
                    <i class="bi bi-plus-square"></i>
                    <span>Add Task</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="taskManagement.php" class="nav-link">
                    <i class="bi bi-clipboard2-check"></i>
                    <span>Task Management</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Your page content will go here -->
    </main>

    <script>
        // Toggle sidebar with smooth animation
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
            
            if (window.innerWidth < 768) {
                const mainContent = document.getElementById('mainContent');
                if (sidebar.classList.contains('open')) {
                    mainContent.style.position = 'fixed';
                    mainContent.style.width = '100%';
                } else {
                    mainContent.style.position = '';
                    mainContent.style.width = '';
                }
            }
        }

        // Toggle search bar on mobile with animation
        function toggleSearch() {
            const searchBar = document.querySelector('.search-bar');
            searchBar.classList.toggle('active');
            
            if (searchBar.classList.contains('active')) {
                searchBar.querySelector('input').focus();
            }
        }

        // Toggle theme with smooth transition
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.querySelector('.theme-toggle i');
            
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
            }
        }

        // // Update datetime display
        // function updateDateTime() {
        //     const now = new Date();
        //     const options = { 
        //         weekday: 'long', 
        //         year: 'numeric', 
        //         month: 'long', 
        //         day: 'numeric',
        //         hour: '2-digit',
        //         minute: '2-digit',
        //         second: '2-digit'
        //     };
        //     document.getElementById('datetime').textContent = now.toLocaleDateString('en-US', options);
        // }

        // Profile dropdown with better interaction
        document.addEventListener('DOMContentLoaded', function() {
            const profileDropdown = document.getElementById('profileDropdown');
            
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                profileDropdown.classList.remove('active');
            });
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                document.querySelector('.theme-toggle i').classList.remove('bi-moon');
                document.querySelector('.theme-toggle i').classList.add('bi-sun');
            }
            
            // Set active nav link based on current page
            const currentPage = window.location.pathname.split('/').pop();
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
            
            // Update datetime every second
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Fetch and display the business name from session
            fetch('getBusinessName.php')
                .then(response => response.text())
                .then(bname => {
                    if (bname) {
                        document.getElementById('businessName').textContent = bname;
                    }
                })
                .catch(error => {
                    console.error('Error fetching business name:', error);
                });
        });
    </script>
</body>

</html>