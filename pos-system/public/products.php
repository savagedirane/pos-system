<?php
/**
 * Product Management Page
 * CRUD operations for products
 */

session_start();
require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../app/controllers/AuthController.php';

$auth = new AuthController();
$user = $auth->getCurrentUser();

// Check access
if (!in_array($user['role'], ['admin', 'manager', 'inventory_staff'])) {
    header('Location: dashboard.php');
    exit;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$message = '';
$message_type = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_name = trim($_POST['product_name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $cost_price = floatval($_POST['cost_price'] ?? 0);
        $selling_price = floatval($_POST['selling_price'] ?? 0);
        $reorder_level = intval($_POST['reorder_level'] ?? 10);
        
        if (!$product_name || !$sku || !$category_id || !$cost_price || !$selling_price) {
            $message = 'All fields are required';
            $message_type = 'danger';
        } else {
            // Check if SKU already exists
            $check_stmt = $mysqli->prepare("SELECT product_id FROM products WHERE sku = ?");
            $check_stmt->execute([$sku]);
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $message = "Error: SKU '$sku' already exists. Please use a unique SKU.";
                $message_type = 'danger';
            } else {
                $stmt = $mysqli->prepare("INSERT INTO products (product_name, sku, category_id, cost_price, selling_price, reorder_level, quantity_on_hand) VALUES (?, ?, ?, ?, ?, ?, 0)");
                
                if ($stmt->execute([$product_name, $sku, $category_id, $cost_price, $selling_price, $reorder_level])) {
                    $message = "Product '$product_name' created successfully";
                    $message_type = 'success';
                } else {
                    $message = 'Error: ' . $stmt->error;
                    $message_type = 'danger';
                }
            }
            $check_stmt->close();
        }
    }
}

// Get categories for dropdown
$categories = [];
$result = $mysqli->query("SELECT category_id, category_name FROM categories WHERE is_active = 1 ORDER BY category_name");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Get all products
$products = [];
$result = $mysqli->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_name");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - POS System</title>
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
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        .btn-add {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-add:hover {
            background: var(--secondary);
            color: white;
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
        .price-cell {
            font-weight: 600;
        }
        .low-stock {
            color: #ef4444;
            font-weight: 600;
        }
        .adequate-stock {
            color: #10b981;
            font-weight: 600;
        }
        @media (max-width: 768px) {
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
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title"><i class="bi bi-box"></i> Product Management</h1>
            <p class="page-subtitle">Create, view, and manage products</p>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-lg"></i> Add Product
                </button>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No products found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($p['product_name']); ?></strong></td>
                                            <td><code><?php echo htmlspecialchars($p['sku']); ?></code></td>
                                            <td><?php echo htmlspecialchars($p['category_name'] ?? 'N/A'); ?></td>
                                            <td class="price-cell"><?php echo CURRENCY_SYMBOL . number_format($p['cost_price'], 2); ?></td>
                                            <td class="price-cell"><?php echo CURRENCY_SYMBOL . number_format($p['selling_price'], 2); ?></td>
                                            <td>
                                                <span class="<?php echo ($p['quantity_on_hand'] <= $p['reorder_level']) ? 'low-stock' : 'adequate-stock'; ?>">
                                                    <?php echo $p['quantity_on_hand']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cost Price</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" name="selling_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" name="reorder_level" class="form-control" value="10">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
