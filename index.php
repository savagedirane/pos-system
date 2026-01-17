<?php
/**
 * File: index.php
 * Description: Main application entry point
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

// Set error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Define application constants
define('APP_DIR', dirname(__FILE__));
define('APP_NAME', 'POS System');
define('APP_VERSION', '1.0.0');

// Include configuration
require_once APP_DIR . '/config/database.php';
require_once APP_DIR . '/config/settings.php';

// Include core utilities and helpers
require_once APP_DIR . '/utils/helpers/SecurityHelper.php';
require_once APP_DIR . '/utils/helpers/LoggerHelper.php';
require_once APP_DIR . '/utils/helpers/ResponseHelper.php';

// Initialize logger
$logger = new LoggerHelper();

try {
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    // Simple routing based on REQUEST_URI
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = str_replace('/pos-system/', '', $uri);
    $uri = ltrim($uri, '/');
    
    // Route to API or Pages
    if (strpos($uri, 'api/') === 0) {
        // API route
        handleAPIRequest($uri, $db, $logger);
    } else {
        // Page route
        handlePageRequest($uri, $db, $logger);
    }
    
} catch (Exception $e) {
    $logger->logError("Application Error: " . $e->getMessage());
    ResponseHelper::error($e->getMessage(), 500);
}

/**
 * Handle API requests
 */
function handleAPIRequest($uri, $db, $logger) {
    $parts = explode('/', $uri);
    
    if (count($parts) < 3) {
        ResponseHelper::error("Invalid API endpoint", 400);
        return;
    }
    
    $version = $parts[1]; // v1
    $endpoint = $parts[2]; // products, sales, etc.
    
    // Route to appropriate controller
    $controllerFile = APP_DIR . "/api/{$version}/{$endpoint}.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        ResponseHelper::error("API endpoint not found", 404);
    }
}

/**
 * Handle page requests
 */
function handlePageRequest($uri, $db, $logger) {
    // Require authentication for non-login pages
    if (!isset($_SESSION['user_id']) && $uri !== 'login' && $uri !== '') {
        header("Location: /pos-system/login");
        exit;
    }
    
    // Default to dashboard
    if (empty($uri)) {
        $uri = 'dashboard';
    }
    
    // Load appropriate view
    $viewFile = APP_DIR . "/app/views/{$uri}.php";
    
    if (file_exists($viewFile)) {
        require_once APP_DIR . '/app/views/layout.php';
    } else {
        http_response_code(404);
        echo "Page not found";
    }
}

?>
