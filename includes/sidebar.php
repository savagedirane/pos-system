<?php
/**
 * Sidebar Navigation Component
 * Reusable sidebar for all pages
 */

// Get current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

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
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            width: 240px;
        }
        .sidebar.active {
            transform: translateX(0);
        }
    }
</style>

<!-- Left Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-shop"></i>
        <h2>POS System</h2>
    </div>
    
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="dashboard.php" class="sidebar-nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="users.php" class="sidebar-nav-link <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>
        </li>
        <?php if (isset($user) && $user['role'] === 'admin'): ?>
        <li class="sidebar-nav-item">
            <a href="register.php" class="sidebar-nav-link <?php echo ($current_page === 'register.php') ? 'active' : ''; ?>">
                <i class="bi bi-person-plus"></i>
                <span>Register User</span>
            </a>
        </li>
        <?php endif; ?>
        <li class="sidebar-nav-item">
            <a href="products.php" class="sidebar-nav-link <?php echo ($current_page === 'products.php') ? 'active' : ''; ?>">
                <i class="bi bi-box"></i>
                <span>Products</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="customers.php" class="sidebar-nav-link <?php echo ($current_page === 'customers.php') ? 'active' : ''; ?>">
                <i class="bi bi-person-check"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="sales.php" class="sidebar-nav-link <?php echo ($current_page === 'sales.php') ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i>
                <span>Sales</span>
            </a>
        </li>
    </ul>
    
    <!-- User Info at Bottom - Only for Admin -->
    <?php if (isset($user) && $user['role'] === 'admin'): ?>
    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($user['first_name']); ?></div>
                <div class="user-role"><?php echo ucfirst($user['role']); ?></div>
            </div>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
    <?php endif; ?>
</aside>

<script>
    // Highlight current page in sidebar on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
        const navLinks = document.querySelectorAll('.sidebar-nav-link');
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>
