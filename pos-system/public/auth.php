<?php
/**
 * Central Authentication Handler
 * Handles all authentication operations: login, logout, registration, password reset
 * Usage: POST requests to this file with 'action' parameter
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../utils/helpers/LoggerHelper.php';

// Initialize
$authController = new AuthController();
$logger = new LoggerHelper();
$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    // Get action from POST or GET
    $action = isset($_POST['action']) ? trim($_POST['action']) : (isset($_GET['action']) ? trim($_GET['action']) : '');

    switch ($action) {
        case 'login':
            $response = handleLogin();
            break;

        case 'logout':
            $response = handleLogout();
            break;

        case 'register':
            $response = handleRegister();
            break;

        case 'check_session':
            $response = handleCheckSession();
            break;

        case 'get_user':
            $response = handleGetUser();
            break;

        default:
            $response = ['status' => 'error', 'code' => 400, 'message' => 'Invalid action'];
    }

} catch (Exception $e) {
    $logger->logError("Auth handler error: " . $e->getMessage());
    $response = ['status' => 'error', 'code' => 500, 'message' => 'An error occurred'];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Handle login
 */
function handleLogin() {
    global $authController, $logger;

    // Validate input
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Username and password required'];
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Username and password cannot be empty'];
    }

    // Attempt login
    $result = $authController->login(['username' => $username, 'password' => $password]);

    if (isset($result['status']) && $result['status'] === 'success') {
        $logger->logInfo("User logged in: $username");
        return $result;
    } else {
        $logger->logWarning("Login failed for user: $username");
        return $result;
    }
}

/**
 * Handle logout
 */
function handleLogout() {
    global $logger;

    if (!isset($_SESSION['user_id'])) {
        return ['status' => 'error', 'code' => 401, 'message' => 'Not logged in'];
    }

    $user_id = $_SESSION['user_id'];
    $logger->logInfo("User logged out: ID $user_id");

    // Clear session
    session_destroy();

    return ['status' => 'success', 'code' => 200, 'message' => 'Logged out successfully'];
}

/**
 * Handle registration - Admin only
 */
function handleRegister() {
    global $authController, $logger;

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['status' => 'error', 'code' => 401, 'message' => 'You must be logged in to register users'];
    }

    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        $logger->logWarning("User registration attempt by non-admin user: {$_SESSION['username']}");
        return ['status' => 'error', 'code' => 403, 'message' => 'Only administrators can register new users'];
    }

    // Validate input
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'Staff';

    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name) || empty($role)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'All required fields must be filled'];
    }

    // Validate password strength
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['status' => 'error', 'code' => 400, 'message' => "Password must be at least " . PASSWORD_MIN_LENGTH . " characters"];
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Invalid email format'];
    }

    // Check if username already exists
    if ($authController->getUserModel()->usernameExists($username)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Username already exists'];
    }

    // Check if email already exists
    if ($authController->getUserModel()->emailExists($email)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Email already exists'];
    }

    // Validate role
    $valid_roles = ['admin', 'manager', 'cashier', 'inventory_staff'];
    if (!in_array($role, $valid_roles)) {
        return ['status' => 'error', 'code' => 400, 'message' => 'Invalid role selected'];
    }

    // Create user
    $user_data = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'role' => $role,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => $_SESSION['user_id']
    ];

    $result = $authController->getUserModel()->createUser($user_data);

    if ($result) {
        $logger->logInfo("New user registered by admin {$_SESSION['username']}: $username (Role: $role)");
        return [
            'status' => 'success',
            'code' => 201,
            'message' => "User '$username' created successfully",
            'user_id' => $result
        ];
    } else {
        $logger->logError("Failed to create user: $username");
        return ['status' => 'error', 'code' => 500, 'message' => 'Failed to create user. Please try again.'];
    }
}

/**
 * Check if user is logged in
 */
function handleCheckSession() {
    if (!isset($_SESSION['user_id'])) {
        return ['status' => 'error', 'code' => 401, 'message' => 'Not logged in', 'logged_in' => false];
    }

    return ['status' => 'success', 'code' => 200, 'message' => 'User is logged in', 'logged_in' => true, 'user_id' => $_SESSION['user_id']];
}

/**
 * Get current user data
 */
function handleGetUser() {
    global $authController;

    if (!isset($_SESSION['user_id'])) {
        return ['status' => 'error', 'code' => 401, 'message' => 'Not logged in'];
    }

    $user = $authController->getCurrentUser();

    if (!$user) {
        return ['status' => 'error', 'code' => 404, 'message' => 'User not found'];
    }

    // Remove sensitive data
    unset($user['password_hash']);

    return ['status' => 'success', 'code' => 200, 'message' => 'User retrieved', 'user' => $user];
}

?>
