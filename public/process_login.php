<?php
/**
 * File: process_login.php
 * Description: Process user login requests
 * Author: POS Development Team
 * Created: 2026-01-17
 * Version: 1.0
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../utils/helpers/LoggerHelper.php';

// Initialize components
$authController = new AuthController();
$logger = new LoggerHelper();

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: login.php?error=' . urlencode('Invalid request method'));
        exit;
    }

    // Get POST data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;

    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=' . urlencode('Username and password are required'));
        exit;
    }

    // Attempt login
    $response = $authController->login([
        'username' => $username,
        'password' => $password
    ]);

    // Check response status
    if (isset($response['status']) && $response['status'] === 'success') {
        // Login successful
        $logger->logInfo("Login successful for user: $username");

        // Handle remember me
        if ($remember) {
            // Set persistent cookie (not recommended without additional security)
            // setcookie('pos_remember', $username, time() + (30 * 24 * 60 * 60), '/');
        }

        // Redirect to dashboard
        header('Location: /pos-system/public/dashboard.php');
        exit;
    } else {
        // Login failed
        $error_message = $response['message'] ?? 'Login failed. Please try again.';
        $logger->logWarning("Login failed for user: $username - " . $error_message);
        
        header('Location: login.php?error=' . urlencode($error_message));
        exit;
    }

} catch (Exception $e) {
    $logger->logError("Login process error: " . $e->getMessage());
    header('Location: login.php?error=' . urlencode('An unexpected error occurred. Please try again.'));
    exit;
}

?>
