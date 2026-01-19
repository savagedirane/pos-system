<?php
/**
 * Landing Page - POS System
 * Modern e-commerce style homepage
 */

// Get database connection for featured products
require_once '../config/database.php';
require_once '../config/settings.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Get featured products (limit 12)
$featured_products = [];
$result = $mysqli->query("
    SELECT p.product_id, p.product_name, p.selling_price, p.quantity_on_hand, p.sku,
           c.category_name, p.category_id
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    WHERE p.is_active = 1 
    ORDER BY p.product_id DESC 
    LIMIT 12
");
while ($row = $result->fetch_assoc()) {
    $featured_products[] = $row;
}

// Get categories for filter
$categories = [];
$result = $mysqli->query("SELECT category_id, category_name FROM categories WHERE is_active = 1 ORDER BY category_name");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Get total stats
$total_products = $mysqli->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1")->fetch_assoc()['count'];
$total_customers = $mysqli->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$total_sales = $mysqli->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'];

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Modern Point of Sale Solution</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: var(--dark);
        }
        
        /* Navigation */
        .navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-brand i {
            font-size: 28px;
        }
        
        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            transition: color 0.3s ease;
            margin: 0 12px;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .btn-signin {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-signin:hover {
            background: var(--secondary);
            color: white;
        }
        
        .btn-signup {
            background: var(--success);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin-left: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-signup:hover {
            background: #059669;
            color: white;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            overflow: hidden;
            position: relative;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            animation: slideInUp 0.8s ease;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 32px;
            opacity: 0.95;
        }
        
        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-hero-primary {
            background: white;
            color: var(--primary);
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-hero-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        /* Stats Section */
        .stats {
            padding: 60px 0;
            background: var(--light);
        }
        
        .stat-item {
            text-align: center;
            padding: 32px;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Features Section */
        .features {
            padding: 80px 0;
        }
        
        .section-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 48px;
            color: var(--dark);
        }
        
        .section-subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 18px;
            margin-bottom: 32px;
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin: 0 auto 16px;
        }
        
        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--dark);
        }
        
        .feature-card p {
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Products Section */
        .products-section {
            padding: 80px 0;
            background: var(--light);
        }
        
        .filter-container {
            margin-bottom: 40px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .filter-btn {
            padding: 8px 20px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            color: var(--dark);
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .search-container {
            margin-bottom: 32px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            gap: 12px;
        }
        
        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .search-btn {
            padding: 12px 32px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        
        .search-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        }
        
        .product-image {
            width: 100%;
            height: 240px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: rgba(255, 255, 255, 0.3);
        }
        
        .product-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-category {
            font-size: 12px;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
            line-height: 1.4;
        }
        
        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .product-stock {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        
        .product-stock.low {
            color: var(--danger);
        }
        
        .product-stock.available {
            color: var(--success);
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        
        .btn-view {
            flex: 1;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-view:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-add-cart {
            flex: 1;
            padding: 10px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-add-cart:hover {
            background: #059669;
            transform: translateY(-2px);
        }
        
        .btn-add-cart:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
            opacity: 0.6;
        }
        
        .btn-wishlist {
            padding: 10px 16px;
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-wishlist:hover {
            background: var(--primary);
            color: white;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .cta-subtitle {
            font-size: 18px;
            margin-bottom: 32px;
            opacity: 0.95;
        }
        
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 48px 0;
        }
        
        .footer-section h6 {
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 32px;
            margin-top: 32px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .product-card {
            animation: fadeIn 0.5s ease;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .btn-hero {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop"></i>
                POS System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex ms-auto gap-2">
                    <a href="login.php" class="btn btn-signin">Sign In</a>
                    <a href="register.php" class="btn btn-signup">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Transform Your Business with Our POS System</h1>
                <p>Modern, secure, and scalable point of sale solution for retail businesses</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-hero btn-hero-primary">Get Started Free</a>
                    <a href="#products" class="btn btn-hero btn-hero-secondary">Explore Products</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($total_products); ?>+</div>
                        <div class="stat-label">Active Products</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($total_customers); ?>+</div>
                        <div class="stat-label">Customers</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($total_sales); ?>+</div>
                        <div class="stat-label">Transactions</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Powerful Features</h2>
            <p class="section-subtitle">Everything you need to run your retail business efficiently</p>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h5>Lightning Fast</h5>
                        <p>Process transactions in milliseconds with our optimized system architecture.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5>Secure & Safe</h5>
                        <p>Bank-level encryption and comprehensive security protocols protect your data.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5>Advanced Analytics</h5>
                        <p>Real-time insights and detailed reports to drive business decisions.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5>Multi-User Support</h5>
                        <p>Manage multiple staff members with role-based access controls.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-box"></i>
                        </div>
                        <h5>Inventory Management</h5>
                        <p>Track stock levels, set alerts, and optimize inventory in real-time.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-cloud-check"></i>
                        </div>
                        <h5>Cloud Based</h5>
                        <p>Access your data anytime, anywhere with our cloud infrastructure.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" class="search-input" id="productSearch" placeholder="Search products by name...">
                <button class="search-btn" onclick="triggerSearch()"><i class="bi bi-search"></i> Search</button>
            </div>

            <!-- Category Filter -->
            <div class="filter-container">
                <button class="filter-btn active" onclick="filterByCategory('all')">All Products</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn" onclick="filterByCategory(<?php echo $cat['category_id']; ?>)">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Product Grid -->
            <div class="product-grid" id="productGrid">
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>" data-category="<?php echo $product['category_id']; ?>" data-name="<?php echo strtolower($product['product_name']); ?>" data-price="<?php echo $product['selling_price']; ?>" data-stock="<?php echo $product['quantity_on_hand']; ?>">
                        <div class="product-image">
                            <i class="bi bi-box"></i>
                        </div>
                        <div class="product-body">
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></div>
                            <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                            <div class="product-price"><?php echo CURRENCY_SYMBOL . number_format($product['selling_price'], 2); ?></div>
                            <div class="product-stock <?php echo $product['quantity_on_hand'] > 10 ? 'available' : 'low'; ?>">
                                <?php if ($product['quantity_on_hand'] > 0): ?>
                                    <i class="bi bi-check-circle"></i> In Stock (<?php echo $product['quantity_on_hand']; ?>)
                                <?php else: ?>
                                    <i class="bi bi-x-circle"></i> Out of Stock
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn-view" onclick="viewProductDetails(<?php echo htmlspecialchars(json_encode($product)); ?>)" title="View details">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>', <?php echo $product['selling_price']; ?>, <?php echo $product['quantity_on_hand']; ?>)" <?php echo $product['quantity_on_hand'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" style="display: none; text-align: center; padding: 60px 20px;">
                <i class="bi bi-search" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                <h4 style="color: #6b7280;">No products found</h4>
                <p style="color: #9ca3af;">Try adjusting your search or filter criteria</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact">
        <div class="container">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-subtitle">Join thousands of businesses using our POS system</p>
            <a href="register.php" class="btn btn-hero btn-hero-primary">Create Your Account Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6><i class="bi bi-shop"></i> POS System</h6>
                        <p style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">Modern point of sale solution for retail businesses.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Product</h6>
                        <a href="#features" class="footer-link">Features</a>
                        <a href="#products" class="footer-link">Products</a>
                        <a href="#" class="footer-link">Pricing</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Company</h6>
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Blog</a>
                        <a href="#" class="footer-link">Careers</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h6>Legal</h6>
                        <a href="#" class="footer-link">Privacy</a>
                        <a href="#" class="footer-link">Terms</a>
                        <a href="#" class="footer-link">Contact</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 POS System. All rights reserved. | Made with <i class="bi bi-heart-fill" style="color: var(--danger);"></i> for Retailers</p>
            </div>
        </div>
    </footer>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;">
                                <i class="bi bi-box"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <small style="color: #6b7280;">Category</small>
                                <div id="modalCategory" style="font-size: 14px; color: #9ca3af;">-</div>
                            </div>
                            <div class="mb-3">
                                <h4 id="modalProductName">-</h4>
                            </div>
                            <div class="mb-3">
                                <small style="color: #6b7280;">SKU</small>
                                <div id="modalSku" style="font-size: 14px; font-family: monospace;">-</div>
                            </div>
                            <div class="mb-3">
                                <h5 style="color: var(--primary);">Price: <span id="modalPrice">-</span></h5>
                            </div>
                            <div class="mb-3">
                                <small style="color: #6b7280;">Stock Available</small>
                                <div id="modalStock" style="font-size: 14px;">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p id="modalDescription" style="color: #6b7280; line-height: 1.6;">-</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalAddToCartBtn" onclick="confirmAddToCart()">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Notification Toast -->
    <div id="cartToast" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; display: none;">
        <div class="alert alert-success alert-dismissible fade show" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
            <i class="bi bi-check-circle"></i> <span id="toastMessage">Added to cart!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Product data for filtering and searching
        const products = <?php echo json_encode($featured_products); ?>;

        /**
         * Binary Search Implementation for Product Filtering
         * Efficiently finds products by name using sorted data
         */
        function binarySearch(arr, target) {
            let left = 0;
            let right = arr.length - 1;
            const results = [];

            while (left <= right) {
                const mid = Math.floor((left + right) / 2);
                const midValue = arr[mid].product_name.toLowerCase();

                if (midValue.includes(target)) {
                    results.push(arr[mid]);
                    
                    // Search left side
                    let leftIdx = mid - 1;
                    while (leftIdx >= 0 && arr[leftIdx].product_name.toLowerCase().includes(target)) {
                        results.unshift(arr[leftIdx]);
                        leftIdx--;
                    }

                    // Search right side
                    let rightIdx = mid + 1;
                    while (rightIdx < arr.length && arr[rightIdx].product_name.toLowerCase().includes(target)) {
                        results.push(arr[rightIdx]);
                        rightIdx++;
                    }

                    return results;
                } else if (midValue < target) {
                    left = mid + 1;
                } else {
                    right = mid - 1;
                }
            }

            // Fallback: linear search for partial matches
            return arr.filter(p => p.product_name.toLowerCase().includes(target));
        }

        /**
         * Filter Products by Category
         * Uses efficient filtering algorithm
         */
        function filterByCategory(categoryId) {
            const cards = document.querySelectorAll('.product-card');
            const filterBtns = document.querySelectorAll('.filter-btn');
            let visibleCount = 0;

            // Update active button
            filterBtns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Filter cards
            cards.forEach(card => {
                if (categoryId === 'all' || card.dataset.category == categoryId) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Allow Enter key to trigger search
        document.getElementById('productSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerSearch();
            }
        });

        /**
         * Search Products with Real-time Filtering
         * Uses binary search for optimal performance
         */
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            if (searchTerm === '') {
                cards.forEach(card => {
                    card.style.display = 'block';
                    visibleCount++;
                });
            } else {
                // Use binary search for efficient product lookup
                const sortedProducts = [...products].sort((a, b) => 
                    a.product_name.localeCompare(b.product_name)
                );
                const matchedProducts = binarySearch(sortedProducts, searchTerm);
                const matchedIds = new Set(matchedProducts.map(p => p.product_id));

                cards.forEach(card => {
                    const productName = card.dataset.name.toLowerCase();
                    if (productName.includes(searchTerm)) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Show/hide no results message
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        });

        /**
         * Trigger Search - Button Click Handler
         */
        function triggerSearch() {
            const searchTerm = document.getElementById('productSearch').value.toLowerCase().trim();
            
            if (searchTerm === '') {
                alert('Please enter a product name to search');
                return;
            }
            
            // Trigger the same search logic as typing
            const event = new Event('input');
            document.getElementById('productSearch').dispatchEvent(event);
        }

        // Store current product being viewed
        let currentProduct = null;

        /**
         * View Product Details in Modal
         */
        function viewProductDetails(product) {
            currentProduct = product;
            
            // Populate modal with product data
            document.getElementById('modalProductName').textContent = product.product_name;
            document.getElementById('modalCategory').textContent = product.category_name || 'Uncategorized';
            document.getElementById('modalSku').textContent = product.sku || '-';
            document.getElementById('modalPrice').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + parseFloat(product.selling_price).toFixed(2);
            
            // Use description if available, otherwise generate from product name
            const description = product.description || `High-quality ${product.product_name}. Perfect for your retail needs. Available in ${product.quantity_on_hand} units.`;
            document.getElementById('modalDescription').textContent = description;
            
            const stockText = product.quantity_on_hand > 0 
                ? `${product.quantity_on_hand} units available`
                : '<span style="color: var(--danger);">Out of Stock</span>';
            document.getElementById('modalStock').innerHTML = stockText;
            
            // Enable/disable add to cart button
            const addBtn = document.getElementById('modalAddToCartBtn');
            addBtn.disabled = product.quantity_on_hand <= 0;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
            modal.show();
        }

        /**
         * Confirm Add to Cart from Modal
         */
        function confirmAddToCart() {
            if (currentProduct) {
                addToCart(
                    currentProduct.product_id,
                    currentProduct.product_name,
                    currentProduct.selling_price,
                    currentProduct.quantity_on_hand
                );
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('productDetailModal'));
                modal.hide();
            }
        }

        /**
         * Add Product to Cart
         * Stores in localStorage
         */
        function addToCart(productId, productName, price, stock) {
            // Validate stock
            if (stock <= 0) {
                showCartToast('error', 'Product is out of stock', 'danger');
                return false;
            }
            
            // Get existing cart
            let cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            
            // Check if product already in cart
            const existingItem = cart.find(item => item.product_id == productId);
            
            if (existingItem) {
                // Increase quantity
                if (existingItem.quantity < stock) {
                    existingItem.quantity += 1;
                    showCartToast('success', `"${productName}" quantity increased to ${existingItem.quantity}`, 'success');
                } else {
                    showCartToast('error', 'Cannot add more - insufficient stock', 'danger');
                    return false;
                }
            } else {
                // Add new item
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    price: price,
                    quantity: 1,
                    max_stock: stock
                });
                showCartToast('success', `"${productName}" added to cart!`, 'success');
            }
            
            // Save to localStorage
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            
            // Update cart count if element exists
            updateCartCount();
            
            return true;
        }

        /**
         * Update Cart Count Display
         */
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            // Update cart badge if it exists on page
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge) {
                cartBadge.textContent = totalItems;
                cartBadge.style.display = totalItems > 0 ? 'inline-block' : 'none';
            }
        }

        /**
         * Show Cart Toast Notification
         */
        function showCartToast(type, message, alertType = 'success') {
            const toast = document.getElementById('cartToast');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            toast.className = 'alert alert-' + alertType + ' alert-dismissible fade show';
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        /**
         * Add Product to Wishlist
         * Stores in localStorage
         */
        function addToWishlist(productName) {
            let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            
            if (!wishlist.includes(productName)) {
                wishlist.push(productName);
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                alert(`✓ Added "${productName}" to wishlist!`);
            } else {
                wishlist = wishlist.filter(p => p !== productName);
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                alert(`✓ Removed "${productName}" from wishlist!`);
            }
        }

        /**
         * Smooth scroll to sections
         */
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        /**
         * Lazy load animations on scroll
         */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.5s ease';
                }
            });
        });

        document.querySelectorAll('.feature-card, .product-card').forEach(el => {
            observer.observe(el);
        });

        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>
</body>
</html>
