<?php
/**
 * Customer Management Page
 * CRUD operations for customers
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

// Handle customer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $customer_name = trim($_POST['customer_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        if (!$customer_name || !$email) {
            $message = 'Customer name and email are required';
            $message_type = 'danger';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO customers (customer_name, email, phone, address) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$customer_name, $email, $phone, $address])) {
                $message = "Customer '$customer_name' created successfully";
                $message_type = 'success';
            } else {
                $message = 'Error: ' . $stmt->error;
                $message_type = 'danger';
            }
        }
    }
}

// Get all customers
$customers = [];
$result = $mysqli->query("SELECT customer_id, customer_name, email, phone, address, total_purchases, created_at FROM customers ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - POS System</title>
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
            <h1 class="page-title"><i class="bi bi-person-check"></i> Customer Management</h1>
            <p class="page-subtitle">Create, view, and manage customers</p>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="bi bi-plus-lg"></i> Add Customer
                </button>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Customers Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Total Purchases</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No customers found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customers as $c): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($c['customer_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                                            <td><?php echo htmlspecialchars($c['phone'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars(substr($c['address'] ?? '', 0, 30)); ?></td>
                                            <td><span class="badge bg-success"><?php echo $c['total_purchases']; ?></span></td>
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

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
