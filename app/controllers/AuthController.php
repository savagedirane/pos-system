<?php
/**
 * File: AuthController.php
 * Description: Authentication and user login/logout operations
 * Author: POS Development Team
 * Created: 2026-01-17
 * Version: 1.0
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../utils/helpers/SecurityHelper.php';
require_once __DIR__ . '/../../utils/helpers/LoggerHelper.php';
require_once __DIR__ . '/../../utils/helpers/ResponseHelper.php';

class AuthController {
    private $userModel;
    private $securityHelper;
    private $logger;
    private $db;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die('Database connection failed: ' . $this->db->connect_error);
        }
        $this->userModel = new User($this->db);
        $this->securityHelper = new SecurityHelper();
        $this->logger = new LoggerHelper();
    }

    /**
     * Get user model for direct access
     * @return User
     */
    public function getUserModel() {
        return $this->userModel;
    }

    /**
     * Handle user login
     * POST /api/v1/auth/login
     * 
     * @param array $data Username/email and password
     * @return array Response with success status and token
     */
    public function login($data = null) {
        if ($data === null) {
            $data = $_POST;
        }

        // Validate input
        if (empty($data['username']) || empty($data['password'])) {
            $this->logger->logWarning("Login attempt with missing credentials from IP: " . $this->getClientIP());
            return ResponseHelper::validationError('Username and password are required');
        }

        $username = trim($data['username']);
        $password = $data['password'];

        // Check login attempts
        if ($this->isLoginAttemptExceeded($username)) {
            $this->logger->logWarning("Login attempt exceeded for user: $username");
            return ResponseHelper::error('Too many failed login attempts. Please try again later.', 429);
        }

        // Get user from database
        $user = $this->userModel->getByUsername($username);

        if (!$user) {
            $this->recordFailedLogin($username);
            $this->logger->logWarning("Login failed: User not found - $username");
            return ResponseHelper::error('Invalid username or password', 401);
        }

        // Verify password
        if (!$this->securityHelper->verifyPassword($password, $user['password_hash'])) {
            $this->recordFailedLogin($username);
            $this->logger->logWarning("Login failed: Invalid password for user - $username");
            return ResponseHelper::error('Invalid username or password', 401);
        }

        // Check if user is active
        if (!$user['is_active']) {
            $this->logger->logWarning("Login attempt for inactive user: $username");
            return ResponseHelper::error('User account is disabled', 403);
        }

        // Clear failed login attempts
        $this->clearFailedLogins($username);

        // Update last login
        $this->userModel->updateLastLogin($user['user_id']);

        // Create session
        $this->createUserSession($user);

        // Log successful login
        $this->logger->logInfo("User logged in successfully: " . $user['username'], [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'ip_address' => $this->getClientIP()
        ]);

        return ResponseHelper::success([
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ], 200, 'Login successful');
    }

    /**
     * Handle user logout
     * POST /api/v1/auth/logout
     * 
     * @return array Response with success status
     */
    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $username = $_SESSION['username'] ?? 'Unknown';
            $user_id = $_SESSION['user_id'] ?? 0;

            // Log logout event
            $this->logger->logInfo("User logged out: $username", [
                'user_id' => $user_id,
                'username' => $username
            ]);

            // Destroy session
            session_destroy();
        }

        return ResponseHelper::success(null, 'Logged out successfully');
    }

    /**
     * Get current logged-in user
     * GET /api/v1/auth/user
     * 
     * @return array User data or error
     */
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);

        if (!$user) {
            return null;
        }

        // Don't return password hash
        unset($user['password_hash']);

        return $user;
    }

    /**
     * Get current user as JSON API response
     * @return array JSON response with user data
     */
    public function getCurrentUserResponse() {
        if (!isset($_SESSION['user_id'])) {
            return ResponseHelper::error('Not authenticated', 401);
        }

        $user = $this->userModel->getById($_SESSION['user_id']);

        if (!$user) {
            return ResponseHelper::error('User not found', 404);
        }

        // Don't return password hash
        unset($user['password_hash']);

        return ResponseHelper::success($user);
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if valid
     */
    public function verifyCsrfToken($token) {
        return $this->securityHelper->verifyCSRFToken($token);
    }

    /**
     * Get CSRF token for form
     * 
     * @return string CSRF token
     */
    public function getCsrfToken() {
        return $this->securityHelper->generateCSRFToken();
    }

    /**
     * Record failed login attempt
     * 
     * @param string $username Username that failed login
     */
    private function recordFailedLogin($username) {
        $cache_file = sys_get_temp_dir() . '/pos_failed_login_' . md5($username);
        $attempts = 0;

        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            $attempts = $data['attempts'] ?? 0;
            
            // Reset if more than 1 hour has passed
            if (time() - $data['timestamp'] > 3600) {
                $attempts = 0;
            }
        }

        $attempts++;
        file_put_contents($cache_file, json_encode([
            'attempts' => $attempts,
            'timestamp' => time()
        ]));
    }

    /**
     * Check if login attempts exceeded
     * 
     * @param string $username Username to check
     * @return bool True if exceeded
     */
    private function isLoginAttemptExceeded($username) {
        $cache_file = sys_get_temp_dir() . '/pos_failed_login_' . md5($username);

        if (!file_exists($cache_file)) {
            return false;
        }

        $data = json_decode(file_get_contents($cache_file), true);
        $attempts = $data['attempts'] ?? 0;

        // Reset if more than 1 hour has passed
        if (time() - $data['timestamp'] > 3600) {
            return false;
        }

        return $attempts >= MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Clear failed login attempts
     * 
     * @param string $username Username to clear
     */
    private function clearFailedLogins($username) {
        $cache_file = sys_get_temp_dir() . '/pos_failed_login_' . md5($username);
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }

    /**
     * Create user session
     * 
     * @param array $user User data
     */
    private function createUserSession($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['login_time'] = time();
        $_SESSION['csrf_token'] = $this->securityHelper->generateCSRFToken();
    }

    /**
     * Get client IP address
     * 
     * @return string Client IP
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        return trim($ip);
    }

    /**
     * Check if user has permission
     * 
     * @param string $permission Permission to check
     * @return bool True if has permission
     */
    public function hasPermission($permission) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        return $this->securityHelper->hasPermission($_SESSION['role'], $permission);
    }

    /**
     * Require authenticated user
     * Redirects to login if not authenticated
     */
    public static function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /pos-system/public/login.php');
            exit;
        }

        // Check session timeout
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: /pos-system/public/login.php?timeout=1');
            exit;
        }

        // Update last activity time
        $_SESSION['login_time'] = time();
    }

    /**
     * Require specific role
     * 
     * @param string|array $roles Required role(s)
     */
    public static function requireRole($roles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role'])) {
            header('Location: /pos-system/public/login.php');
            exit;
        }

        $allowed_roles = is_array($roles) ? $roles : [$roles];

        if (!in_array($_SESSION['role'], $allowed_roles)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access denied. Required role: " . implode(', ', $allowed_roles);
            exit;
        }
    }
}

?>
