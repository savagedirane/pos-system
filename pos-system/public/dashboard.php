<?php
/**
 * Dashboard Page
 * Main application hub after login
 */

session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../app/controllers/AuthController.php';

$auth = new AuthController();
$user = $auth->getCurrentUser();

// Get dashboard statistics
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$stats = [];

// User count
$result = $mysqli->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $result->fetch_assoc()['count'];

// Product count
$result = $mysqli->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$stats['products'] = $result->fetch_assoc()['count'];

// Sales today
$result = $mysqli->query("SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()");
$stats['sales_today'] = $result->fetch_assoc()['count'];

// Total revenue today
$result = $mysqli->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
$stats['revenue_today'] = $result->fetch_assoc()['total'];

// Low stock products
$result = $mysqli->query("SELECT COUNT(*) as count FROM products WHERE quantity_on_hand <= reorder_level AND is_active = 1");
$stats['low_stock'] = $result->fetch_assoc()['count'];

// Recent sales
$recent_sales = [];
$result = $mysqli->query("
    SELECT s.*, c.customer_name, 
           COALESCE(COUNT(si.sale_item_id), 0) as number_of_items
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.customer_id
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    GROUP BY s.sale_id
    ORDER BY s.sale_date DESC 
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $recent_sales[] = $row;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #1e40af;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f3f4f6;
            --dark: #1f2937;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-header i {
            font-size: 28px;
        }
        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }
        .sidebar-nav {
            padding: 24px 0;
            list-style: none;
        }
        .sidebar-nav-item {
            margin: 0;
        }
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 28px;
        }
        .sidebar-nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-left: 4px solid white;
            padding-left: 20px;
        }
        .sidebar-nav-link i {
            font-size: 18px;
            min-width: 24px;
        }
        .sidebar-user {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .user-details {
            flex: 1;
        }
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        .user-role {
            font-size: 12px;
            opacity: 0.8;
        }
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        /* Main Content Area */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: white;
            padding: 20px 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }
        .live-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--success);
            font-weight: 600;
        }
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .main-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            border-color: var(--primary);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform-origin: left;
            animation: slideIn 0.6s ease;
        }
        @keyframes slideIn {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        .stat-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stat-icon {
            font-size: 40px;
            opacity: 0.15;
        }
        .stat-content {
            text-align: right;
            flex: 1;
        }
        .stat-label {
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin: 8px 0 4px;
        }
        .stat-change {
            font-size: 12px;
            color: var(--success);
            font-weight: 600;
        }
        .primary-color { color: var(--primary); }
        .success-color { color: var(--success); }
        .warning-color { color: var(--warning); }
        .danger-color { color: var(--danger); }
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .table-header h5 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: var(--dark);
        }
        .refresh-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .refresh-btn:hover {
            background: var(--secondary);
            transform: rotate(180deg);
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .table {
            color: var(--dark);
        }
        .table thead {
            background: var(--light);
            border-bottom: 2px solid #e5e7eb;
        }
        .table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.3s ease;
        }
        .table tbody tr:hover {
            background: #f9fafb;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 240px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php require_once '../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="welcome-text">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</div>
            <div class="live-indicator">
                <span class="live-dot"></span>
                <span>Live Updates</span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="main-content">
            <!-- Statistics Grid -->
            <div class="row mb-4" id="stats-grid">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-body">
                            <div class="stat-icon primary-color"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Total Users</div>
                                <div class="stat-value primary-color" id="stat-users"><?php echo $stats['users']; ?></div>
                                <div class="stat-change">Live</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-body">
                            <div class="stat-icon success-color"><i class="bi bi-box"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Active Products</div>
                                <div class="stat-value success-color" id="stat-products"><?php echo $stats['products']; ?></div>
                                <div class="stat-change">Live</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-body">
                            <div class="stat-icon warning-color"><i class="bi bi-receipt"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Sales Today</div>
                                <div class="stat-value warning-color" id="stat-sales-today"><?php echo $stats['sales_today']; ?></div>
                                <div class="stat-change">Live</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-body">
                            <div class="stat-icon danger-color"><i class="bi bi-exclamation-triangle"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Low Stock</div>
                                <div class="stat-value danger-color" id="stat-low-stock"><?php echo $stats['low_stock']; ?></div>
                                <div class="stat-change">Live</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="stat-card">
                        <div class="stat-body">
                            <div class="stat-icon primary-color"><i class="bi bi-cash-coin"></i></div>
                            <div class="stat-content">
                                <div class="stat-label">Today's Revenue</div>
                                <div class="stat-value primary-color" id="stat-revenue">
                                    <?php echo CURRENCY_SYMBOL . number_format($stats['revenue_today'], 2); ?>
                                </div>
                                <div class="stat-change">Real-time</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="row">
                <div class="col-12">
                    <div class="table-container">
                        <div class="table-header">
                            <h5><i class="bi bi-graph-up"></i> Recent Sales</h5>
                            <button class="refresh-btn" onclick="refreshStats()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="recent-sales-table">
                                <thead>
                                    <tr>
                                        <th>Sale ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Items</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_sales)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No sales recorded yet</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_sales as $sale): ?>
                                            <tr>
                                                <td><strong>#<?php echo $sale['sale_id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php echo CURRENCY_SYMBOL . number_format($sale['total_amount'], 2); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo ($sale['number_of_items'] ?? 0); ?> items</td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live statistics update
        const CURRENCY_SYMBOL = '<?php echo CURRENCY_SYMBOL; ?>';
        
        async function refreshStats() {
            try {
                const response = await fetch('dashboard.php?api=stats', {
                    credentials: 'same-origin'
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update stat cards with animation
                    updateStatCard('stat-users', data.data.users);
                    updateStatCard('stat-products', data.data.products);
                    updateStatCard('stat-sales-today', data.data.sales_today);
                    updateStatCard('stat-low-stock', data.data.low_stock);
                    updateStatCard('stat-revenue', CURRENCY_SYMBOL + parseFloat(data.data.revenue_today).toFixed(2));
                }
            } catch (error) {
                console.error('Error refreshing stats:', error);
            }
        }
        
        function updateStatCard(elementId, newValue) {
            const element = document.getElementById(elementId);
            if (element && element.textContent !== newValue) {
                element.style.animation = 'none';
                setTimeout(() => {
                    element.textContent = newValue;
                    element.style.animation = 'slideIn 0.6s ease';
                }, 10);
            }
        }
        
        // Auto-refresh every 10 seconds
        setInterval(refreshStats, 10000);
        
        // Highlight current page in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
            const navLinks = document.querySelectorAll('.sidebar-nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
