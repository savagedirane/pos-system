<?php
/**
 * Sales Transaction Page
 * Create and manage sales transactions
 */

session_start();
require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../app/controllers/AuthController.php';

$auth = new AuthController();
$user = $auth->getCurrentUser();

// Check access
if (!in_array($user['role'], ['admin', 'manager', 'cashier'])) {
    header('Location: dashboard.php');
    exit;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$message = '';
$message_type = '';
$sale_id = null;

// Handle sale creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    // Check if JSON request
    if (strpos($content_type, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'create_sale') {
            $customer_id = intval($input['customer_id'] ?? 0);
            $discount_percent = floatval($input['discount_percent'] ?? 0);
            $discount_amount = floatval($input['discount_amount'] ?? 0);
            $total_amount = floatval($input['total_amount'] ?? 0);
            $items = $input['items'] ?? [];
            
            if (empty($items)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                exit;
            }
            
            // Start transaction
            $mysqli->begin_transaction();
            
            try {
                // Create sale
                $stmt = $mysqli->prepare("INSERT INTO sales (customer_id, user_id, total_amount, discount_amount, number_of_items, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
                
                if (!$stmt->execute([
                    $customer_id > 0 ? $customer_id : null,
                    $user['user_id'],
                    $total_amount,
                    $discount_amount,
                    count($items)
                ])) {
                    throw new Exception('Error creating sale');
                }
                
                $sale_id = $mysqli->insert_id;
                
                // Add sale items and update inventory
                $stmt = $mysqli->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, discount_percent, line_total) VALUES (?, ?, ?, ?, ?, ?)");
                $inv_stmt = $mysqli->prepare("UPDATE products SET quantity_on_hand = quantity_on_hand - ? WHERE product_id = ?");
                
                foreach ($items as $item) {
                    $product_id = intval($item['product_id']);
                    $quantity = intval($item['quantity']);
                    $unit_price = floatval($item['selling_price']);
                    $line_total = $unit_price * $quantity;
                    
                    if (!$stmt->execute([$sale_id, $product_id, $quantity, $unit_price, 0, $line_total])) {
                        throw new Exception('Error adding sale item');
                    }
                    
                    if (!$inv_stmt->execute([$quantity, $product_id])) {
                        throw new Exception('Error updating inventory');
                    }
                }
                
                $mysqli->commit();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Sale completed successfully',
                    'sale_id' => $sale_id
                ]);
                exit;
                
            } catch (Exception $e) {
                $mysqli->rollback();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }
    } else {
        // Handle form POST
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create_sale') {
            $customer_id = intval($_POST['customer_id'] ?? 0);
            $product_id = intval($_POST['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 0);
            $discount_percent = floatval($_POST['discount_percent'] ?? 0);
            
            if (!$product_id || !$quantity || $quantity <= 0) {
                $message = 'Please select a product and enter a valid quantity';
                $message_type = 'danger';
            } else {
                // Get product price
                $result = $mysqli->query("SELECT selling_price, quantity_on_hand FROM products WHERE product_id = $product_id");
                $product = $result->fetch_assoc();
                
                if (!$product || $product['quantity_on_hand'] < $quantity) {
                    $message = 'Insufficient stock available';
                    $message_type = 'danger';
                } else {
                    // Calculate amounts
                    $unit_price = $product['selling_price'];
                    $subtotal = $unit_price * $quantity;
                    $discount_amount = ($subtotal * $discount_percent) / 100;
                    $total_amount = $subtotal - $discount_amount;
                    
                    // Create sale
                    $stmt = $mysqli->prepare("INSERT INTO sales (customer_id, user_id, total_amount, discount_amount, number_of_items, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
                    
                    if ($stmt->execute([
                        $customer_id > 0 ? $customer_id : null,
                        $user['user_id'],
                        $total_amount,
                        $discount_amount,
                        $quantity
                    ])) {
                        $sale_id = $mysqli->insert_id;
                        
                        // Add sale item
                        $stmt = $mysqli->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, discount_percent, line_total) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$sale_id, $product_id, $quantity, $unit_price, $discount_percent, $subtotal - (($subtotal * $discount_percent) / 100)]);
                        
                        // Update inventory
                        $stmt = $mysqli->prepare("UPDATE products SET quantity_on_hand = quantity_on_hand - ? WHERE product_id = ?");
                        $stmt->execute([$quantity, $product_id]);
                        
                        $message = "Sale #$sale_id created successfully - Total: " . CURRENCY_SYMBOL . number_format($total_amount, 2);
                        $message_type = 'success';
                    } else {
                        $message = 'Error creating sale: ' . $stmt->error;
                        $message_type = 'danger';
                    }
                }
            }
        }
    }
}

// Get customers
$customers = [];
$result = $mysqli->query("SELECT customer_id, customer_name FROM customers ORDER BY customer_name");
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

// Get products
$products = [];
$result = $mysqli->query("SELECT product_id, product_name, sku, selling_price, quantity_on_hand FROM products WHERE is_active = 1 ORDER BY product_name");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get recent sales
$recent_sales = [];
$result = $mysqli->query("SELECT s.*, c.customer_name, u.first_name, u.last_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.customer_id LEFT JOIN users u ON s.user_id = u.user_id ORDER BY s.sale_date DESC LIMIT 10");
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
    <title>Sales Transaction - POS System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - POS System</title>
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
        
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .page-header {
            background: white;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid #e5e7eb;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 8px 0;
        }
        
        .page-subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        
        .page-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }
        
        /* Sales Container Layout */
        .sales-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
        
        /* Product Selection Section */
        .products-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        
        .section-header {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 8px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .search-box button {
            padding: 10px 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-box button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .products-list {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .product-item:hover {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .product-sku {
            font-size: 12px;
            color: #6b7280;
        }
        
        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            margin-right: 16px;
        }
        
        .product-stock {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 12px;
        }
        
        .stock-available {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .stock-low {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .btn-add-cart {
            padding: 8px 16px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 13px;
        }
        
        .btn-add-cart:hover {
            background: #059669;
            transform: scale(1.05);
        }
        
        /* Cart Section */
        .cart-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .cart-items {
            flex: 1;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid var(--primary);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .cart-item-price {
            font-size: 12px;
            color: #6b7280;
        }
        
        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .qty-control {
            display: flex;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .qty-btn {
            padding: 4px 8px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--dark);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .qty-btn:hover {
            background: var(--light);
        }
        
        .qty-display {
            padding: 4px 12px;
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }
        
        .item-total {
            font-weight: 700;
            color: var(--primary);
            min-width: 80px;
            text-align: right;
        }
        
        .btn-remove {
            padding: 6px 12px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-remove:hover {
            background: var(--danger);
            color: white;
        }
        
        /* Empty Cart Message */
        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            color: #d1d5db;
            text-align: center;
        }
        
        .empty-cart i {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        /* Summary Section */
        .summary-section {
            background: linear-gradient(135deg, var(--light) 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .summary-row.total {
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
        }
        
        .summary-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .summary-total-value {
            color: var(--success);
            font-size: 24px;
        }
        
        /* Customer Section */
        .customer-section {
            margin-bottom: 20px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .customer-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .customer-select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .customer-select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        /* Discount Section */
        .discount-section {
            margin-bottom: 20px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .discount-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .discount-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .btn-action {
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-complete {
            background: var(--success);
            color: white;
        }
        
        .btn-complete:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-cancel {
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }
        
        .btn-cancel:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }
        
        .btn-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Recent Sales Section */
        .recent-sales {
            margin-top: 32px;
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
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
        
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sales-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .main-wrapper {
                margin-left: 0;
            }
            
            .page-content {
                padding: 16px;
            }
            
            .sales-container {
                gap: 16px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
        
        /* Alert Styles */
        .alert-message {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: #065f46;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: #7f1d1d;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php require_once '../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title"><i class="bi bi-receipt"></i> Point of Sale</h1>
            <p class="page-subtitle">Quick and easy sales transaction processing</p>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <div class="container-fluid">
                <!-- Alert Box -->
                <div id="alertBox" class="alert-message" style="display: none;"></div>

                <!-- Sales Layout -->
                <div class="sales-container">
                    
                    <!-- LEFT SECTION: Products Browser -->
                    <div class="products-section">
                        <div class="section-header">
                            <h4><i class="bi bi-box-seam"></i> Products</h4>
                        </div>
                        
                        <!-- Search Products -->
                        <div class="search-box">
                            <input 
                                type="text" 
                                id="productSearch" 
                                class="search-input" 
                                placeholder="Search by name or SKU..."
                                autocomplete="off"
                            >
                            <i class="bi bi-search"></i>
                        </div>
                        
                        <!-- Product List -->
                        <div id="productList" class="product-list">
                            <div class="empty-products">
                                <i class="bi bi-inbox"></i>
                                <p>Loading products...</p>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SECTION: Shopping Cart -->
                    <div class="cart-section">
                        <div class="section-header">
                            <h4><i class="bi bi-cart3"></i> Shopping Cart</h4>
                            <span class="cart-badge"><span id="itemCount">0</span> items</span>
                        </div>
                        
                        <!-- Cart Items -->
                        <div class="cart-items-wrapper">
                            <div id="emptyCart" class="empty-cart" style="display: flex;">
                                <i class="bi bi-cart-x"></i>
                                <p>Cart is empty</p>
                                <small>Add products from the left</small>
                            </div>
                            <div id="cartList" class="cart-items"></div>
                        </div>
                        
                        <!-- Customer Selection -->
                        <div class="customer-section">
                            <div class="customer-label">Customer</div>
                            <select id="customerSelect" class="customer-select">
                                <option value="">Walk-in Customer</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['customer_id']; ?>">
                                        <?php echo htmlspecialchars($c['customer_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Discount -->
                        <div class="discount-section">
                            <div class="customer-label">Discount (%)</div>
                            <input 
                                type="number" 
                                id="discountPercent" 
                                class="discount-input" 
                                min="0" 
                                max="100" 
                                value="0" 
                                step="0.01"
                                placeholder="Enter discount percentage"
                            >
                        </div>
                        
                        <!-- Summary -->
                        <div class="summary-section">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotalAmount" class="summary-value">₱0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Discount:</span>
                                <span id="discountAmount" class="summary-value">₱0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>TOTAL:</span>
                                <span id="totalAmount" class="summary-total-value">₱0.00</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button id="btnCompleteSale" class="btn-action btn-complete" onclick="completeSale()" disabled>
                                <i class="bi bi-check-circle"></i> Complete Sale
                            </button>
                            <button class="btn-action btn-cancel" onclick="cancelSale()">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Sales History -->
                <div class="recent-sales">
                    <h5 class="mb-4"><i class="bi bi-clock-history"></i> Recent Sales</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_sales as $sale): ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($sale['sale_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                        <td><?php echo htmlspecialchars($sale['number_of_items'] ?? '-'); ?></td>
                                        <td class="text-success fw-bold"><?php echo CURRENCY_SYMBOL . number_format($sale['total_amount'], 2); ?></td>
                                        <td><small><?php echo date('H:i', strtotime($sale['sale_date'])); ?></small></td>
                                        <td><span class="badge badge-success">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global cart state
        let cart = [];
        let products = <?php echo json_encode($products); ?>;
        let customers = <?php echo json_encode($customers); ?>;
        
        /**
         * Add product to cart with quantity
         */
        function addToCart(productId, quantity = 1) {
            // Validate inputs
            if (!productId || quantity <= 0) {
                showAlert('danger', 'Invalid product or quantity');
                return false;
            }
            
            // Find product
            const product = products.find(p => p.product_id == productId);
            if (!product) {
                showAlert('danger', 'Product not found');
                return false;
            }
            
            // Check stock
            if (parseInt(quantity) > parseInt(product.quantity_on_hand)) {
                showAlert('danger', 'Insufficient stock. Available: ' + product.quantity_on_hand);
                return false;
            }
            
            // Check if product already in cart
            const existingItem = cart.find(item => item.product_id == productId);
            if (existingItem) {
                const newQty = existingItem.quantity + parseInt(quantity);
                if (newQty > parseInt(product.quantity_on_hand)) {
                    showAlert('danger', 'Cannot add more. Maximum stock: ' + product.quantity_on_hand);
                    return false;
                }
                existingItem.quantity = newQty;
            } else {
                // Add new item to cart
                cart.push({
                    product_id: productId,
                    product_name: product.product_name,
                    sku: product.sku,
                    selling_price: parseFloat(product.selling_price),
                    quantity: parseInt(quantity),
                    available_stock: parseInt(product.quantity_on_hand)
                });
            }
            
            renderCart();
            calculateTotal();
            clearSearchAndFilters();
            showAlert('success', product.product_name + ' added to cart!');
            return true;
        }
        
        /**
         * Remove item from cart
         */
        function removeFromCart(productId) {
            const index = cart.findIndex(item => item.product_id == productId);
            if (index > -1) {
                const productName = cart[index].product_name;
                cart.splice(index, 1);
                renderCart();
                calculateTotal();
                showAlert('success', productName + ' removed from cart');
            }
        }
        
        /**
         * Update quantity of cart item
         */
        function updateQuantity(productId, delta) {
            const item = cart.find(c => c.product_id == productId);
            if (!item) return;
            
            const newQty = item.quantity + delta;
            
            if (newQty <= 0) {
                removeFromCart(productId);
                return;
            }
            
            if (newQty > item.available_stock) {
                showAlert('danger', 'Cannot exceed available stock: ' + item.available_stock);
                return;
            }
            
            item.quantity = newQty;
            renderCart();
            calculateTotal();
        }
        
        /**
         * Render cart items in UI
         */
        function renderCart() {
            const cartList = document.getElementById('cartList');
            const emptyCart = document.getElementById('emptyCart');
            
            if (cart.length === 0) {
                cartList.innerHTML = '';
                emptyCart.style.display = 'flex';
                return;
            }
            
            emptyCart.style.display = 'none';
            cartList.innerHTML = cart.map(item => `
                <div class="cart-item" data-product-id="${item.product_id}">
                    <div class="item-header">
                        <div class="item-info">
                            <div class="item-name">${escapeHtml(item.product_name)}</div>
                            <div class="item-sku">SKU: ${escapeHtml(item.sku)}</div>
                        </div>
                        <button class="btn-remove" onclick="removeFromCart(${item.product_id})" title="Remove item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    
                    <div class="item-details">
                        <div class="price-section">
                            <span class="price-label">Unit Price:</span>
                            <span class="price-value"><?php echo CURRENCY_SYMBOL; ?>${item.selling_price.toFixed(2)}</span>
                        </div>
                        
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQuantity(${item.product_id}, -1)">−</button>
                            <input type="text" class="qty-input" value="${item.quantity}" readonly>
                            <button class="qty-btn" onclick="updateQuantity(${item.product_id}, 1)">+</button>
                        </div>
                        
                        <div class="line-total">
                            <span class="total-label">Subtotal:</span>
                            <span class="total-value"><?php echo CURRENCY_SYMBOL; ?>${(item.selling_price * item.quantity).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        /**
         * Calculate and display total with discount
         */
        function calculateTotal() {
            const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
            
            // Validate discount
            if (discountPercent < 0 || discountPercent > 100) {
                showAlert('danger', 'Discount must be between 0 and 100');
                document.getElementById('discountPercent').value = 0;
                return;
            }
            
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += (item.selling_price * item.quantity);
            });
            
            const discountAmount = (subtotal * discountPercent) / 100;
            const total = subtotal - discountAmount;
            
            // Update summary
            document.getElementById('subtotalAmount').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + subtotal.toFixed(2);
            document.getElementById('discountPercent').dataset.amount = discountAmount.toFixed(2);
            document.getElementById('discountAmount').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + discountAmount.toFixed(2);
            document.getElementById('totalAmount').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + total.toFixed(2);
            document.getElementById('itemCount').textContent = cart.length;
            
            // Enable/disable complete button based on cart
            document.getElementById('btnCompleteSale').disabled = cart.length === 0;
        }
        
        /**
         * Search products in real-time
         */
        function searchProducts(query) {
            if (!query.trim()) {
                renderProductList(products);
                return;
            }
            
            const term = query.toLowerCase();
            const filtered = products.filter(p => 
                p.product_name.toLowerCase().includes(term) ||
                p.sku.toLowerCase().includes(term)
            );
            
            renderProductList(filtered);
        }
        
        /**
         * Render product list
         */
        function renderProductList(productList) {
            const productContainer = document.getElementById('productList');
            
            if (productList.length === 0) {
                productContainer.innerHTML = '<div class="empty-products"><i class="bi bi-inbox"></i><p>No products found</p></div>';
                return;
            }
            
            productContainer.innerHTML = productList.map(p => {
                const stockStatus = parseInt(p.quantity_on_hand) > 10 ? 'good' : 
                                   parseInt(p.quantity_on_hand) > 0 ? 'low' : 'out';
                const stockText = parseInt(p.quantity_on_hand) > 0 ? 
                                 (parseInt(p.quantity_on_hand) + ' in stock') : 'Out of Stock';
                
                return `
                    <div class="product-item" data-product-id="${p.product_id}">
                        <div class="product-header">
                            <h6 class="product-name">${escapeHtml(p.product_name)}</h6>
                            <span class="stock-badge stock-${stockStatus}">${stockText}</span>
                        </div>
                        <div class="product-sku">SKU: ${escapeHtml(p.sku)}</div>
                        <div class="product-price"><?php echo CURRENCY_SYMBOL; ?>${parseFloat(p.selling_price).toFixed(2)}</div>
                        <button class="btn-add-cart" onclick="addToCart(${p.product_id})" 
                                ${parseInt(p.quantity_on_hand) === 0 ? 'disabled' : ''}>
                            <i class="bi bi-plus-circle"></i> Add to Cart
                        </button>
                    </div>
                `;
            }).join('');
        }
        
        /**
         * Complete sale and submit to backend
         */
        async function completeSale() {
            if (cart.length === 0) {
                showAlert('danger', 'Cart is empty. Please add products first.');
                return;
            }
            
            const customerId = document.getElementById('customerSelect').value || 0;
            const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
            
            // Calculate totals
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += (item.selling_price * item.quantity);
            });
            const discountAmount = (subtotal * discountPercent) / 100;
            const totalAmount = subtotal - discountAmount;
            
            // Prepare sale data
            const saleData = {
                customer_id: customerId,
                discount_percent: discountPercent,
                discount_amount: discountAmount,
                total_amount: totalAmount,
                items: cart
            };
            
            // Disable button during submission
            const btn = document.getElementById('btnCompleteSale');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'create_sale',
                        ...saleData
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Sale completed successfully! Sale ID: #' + result.sale_id);
                    cart = [];
                    renderCart();
                    calculateTotal();
                    document.getElementById('customerSelect').value = '';
                    document.getElementById('discountPercent').value = 0;
                    
                    // Reload page after 2 seconds
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('danger', result.message || 'Error processing sale');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Complete Sale';
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Network error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Complete Sale';
            }
        }
        
        /**
         * Cancel current sale
         */
        function cancelSale() {
            if (cart.length === 0) {
                showAlert('info', 'Cart is already empty');
                return;
            }
            
            if (confirm('Are you sure you want to cancel this sale and clear the cart?')) {
                cart = [];
                renderCart();
                calculateTotal();
                document.getElementById('customerSelect').value = '';
                document.getElementById('discountPercent').value = 0;
                document.getElementById('productSearch').value = '';
                showAlert('success', 'Sale cancelled');
            }
        }
        
        /**
         * Clear search and filters
         */
        function clearSearchAndFilters() {
            document.getElementById('productSearch').value = '';
            renderProductList(products);
        }
        
        /**
         * Display alert message
         */
        function showAlert(type, message) {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = 'alert-message alert-' + type;
            alertBox.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i> ${escapeHtml(message)}`;
            alertBox.style.display = 'block';
            
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 4000);
        }
        
        /**
         * Escape HTML to prevent XSS
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            renderProductList(products);
            calculateTotal();
            
            // Search event listener
            document.getElementById('productSearch').addEventListener('input', function(e) {
                searchProducts(e.target.value);
            });
            
            // Discount percentage listener
            document.getElementById('discountPercent').addEventListener('input', function() {
                calculateTotal();
            });
            
            // Enter key support in search
            document.getElementById('productSearch').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        });
    </script>

</body>
</html>
