<?php
/**
 * User Management Page
 * CRUD operations for user accounts
 */

session_start();
require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../app/controllers/AuthController.php';
require_once '../utils/helpers/SecurityHelper.php';

$auth = new AuthController();
$user = $auth->getCurrentUser();

// Check admin access
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$message = '';
$message_type = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'cashier';
        
        // Validate
        if (!$username || !$email || !$password || !$first_name || !$last_name) {
            $message = 'All fields are required';
            $message_type = 'danger';
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, role, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $role, $user['user_id']])) {
                $message = "User '$first_name $last_name' created successfully";
                $message_type = 'success';
            } else {
                $message = 'Error creating user: ' . $stmt->error;
                $message_type = 'danger';
            }
        }
    } elseif ($action === 'toggle') {
        $user_id = intval($_POST['user_id'] ?? 0);
        $is_active = intval($_POST['is_active'] ?? 0);
        $new_status = $is_active ? 0 : 1;
        
        $stmt = $mysqli->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
        if ($stmt->execute([$new_status, $user_id])) {
            $message = 'User status updated';
            $message_type = 'success';
        } else {
            $message = 'Error updating user';
            $message_type = 'danger';
        }
    }
}

// Get all users
$users = [];
$result = $mysqli->query("SELECT user_id, username, email, first_name, last_name, role, is_active, last_login FROM users ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - POS System</title>
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
        .status-active {
            color: var(--success);
            font-weight: 600;
        }
        .status-inactive {
            color: var(--danger);
            font-weight: 600;
        }
        .role-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .role-admin {
            background: #dbeafe;
            color: #1e40af;
        }
        .role-manager {
            background: #dcfce7;
            color: #166534;
        }
        .role-cashier {
            background: #fef3c7;
            color: #92400e;
        }
        .role-inventory {
            background: #f3e8ff;
            color: #6b21a8;
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
            <h1 class="page-title"><i class="bi bi-people"></i> User Management</h1>
            <p class="page-subtitle">Create, view, and manage system users</p>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="container-fluid">
                <!-- Users Table Card -->
                <div class="card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">System Users</h5>
                        <a href="register.php" class="btn btn-add">
                            <i class="bi bi-plus-lg"></i> Add User
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="role-badge role-<?php echo $u['role']; ?>">
                                                <?php echo ucfirst($u['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                                <input type="hidden" name="is_active" value="<?php echo $u['is_active']; ?>">
                                                <button type="submit" class="btn btn-sm btn-link p-0">
                                                    <span class="<?php echo $u['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                        <?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <?php if ($u['last_login']): ?>
                                                <?php echo date('M d, Y h:i A', strtotime($u['last_login'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
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
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="cashier">Cashier</option>
                                <option value="manager">Manager</option>
                                <option value="inventory_staff">Inventory Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
