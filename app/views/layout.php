<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - POS System' : 'POS System'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/pos-system/public/css/main.css" rel="stylesheet">
    <link href="/pos-system/public/css/components.css" rel="stylesheet">
    <link href="/pos-system/public/css/responsive.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 4px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #3498db;
            color: white;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .notification-bell {
            cursor: pointer;
            font-size: 18px;
            color: #2c3e50;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .btn-primary {
            background: #3498db;
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-success {
            background: #27ae60;
            border: none;
        }

        .btn-danger {
            background: #e74c3c;
            border: none;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .table {
            background: white;
        }

        .table thead {
            background: #f8f9fa;
            color: #2c3e50;
        }

        .table th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .badge {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 20px;
        }

        .badge-success {
            background: #27ae60;
            color: white;
        }

        .badge-warning {
            background: #f39c12;
            color: white;
        }

        .badge-danger {
            background: #e74c3c;
            color: white;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .spinner-border {
            color: #3498db;
        }

        .text-muted {
            color: #7f8c8d;
        }

        .text-success {
            color: #27ae60;
        }

        .text-danger {
            color: #e74c3c;
        }

        .text-warning {
            color: #f39c12;
        }

        .text-info {
            color: #3498db;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar Navigation -->
        <nav class="sidebar" id="sidebar">
            <h2>
                <i class="fas fa-shopping-cart"></i> POS System
            </h2>
            <ul>
                <li>
                    <a href="/pos-system/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || strpos($_SERVER['REQUEST_URI'], '/index') !== false) ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/pos-system/sales" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/sales') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-cash-register"></i> Point of Sale
                    </a>
                </li>
                <li>
                    <a href="/pos-system/products" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/products') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i> Products
                    </a>
                </li>
                <li>
                    <a href="/pos-system/inventory" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/inventory') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-warehouse"></i> Inventory
                    </a>
                </li>
                <li>
                    <a href="/pos-system/customers" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/customers') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Customers
                    </a>
                </li>
                <li>
                    <a href="/pos-system/purchasing" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/purchasing') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-bag"></i> Purchasing
                    </a>
                </li>
                <li>
                    <a href="/pos-system/reports" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/reports') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li>
                    <a href="/pos-system/chatbot" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/chatbot') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-robot"></i> Chatbot
                    </a>
                </li>
                <li>
                    <a href="/pos-system/settings" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/settings') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="/pos-system/logout" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <h1>
                    <?php echo isset($page_title) ? $page_title : 'POS System'; ?>
                </h1>
                <div class="header-right">
                    <div class="notification-bell" id="notificationBell" data-bs-toggle="tooltip" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                    </div>
                    <div class="user-menu" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo isset($_SESSION['user_initial']) ? $_SESSION['user_initial'] : 'U'; ?>
                        </div>
                        <span><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?></span>
                        <i class="fas fa-chevron-down"></i>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/pos-system/profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="/pos-system/change-password">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/pos-system/logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content">
                <?php
                    // Include the actual page content
                    if (isset($viewFile) && file_exists($viewFile)) {
                        include $viewFile;
                    }
                ?>
            </div>

            <!-- Footer -->
            <footer>
                <p>&copy; 2024 POS System. All rights reserved. | Version 1.0 | 
                    <a href="/pos-system/help" style="color: #3498db; text-decoration: none;">Help</a>
                </p>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.9.1/chart.min.js"></script>
    <script src="/pos-system/public/js/main.js"></script>

    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').style.marginLeft = 
                document.getElementById('sidebar').classList.contains('collapsed') ? '0' : '250px';
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
