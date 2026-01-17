<?php
/**
 * File: SecurityHelper.php
 * Description: Security utility functions for authentication and authorization
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

class SecurityHelper {
    
    /**
     * Hash password using bcrypt
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
    }

    /**
     * Verify password against hash
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing
     * @param string $hash
     * @return bool
     */
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
    }

    /**
     * Sanitize user input
     * @param string $input
     * @return string
     */
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize output to prevent XSS
     * @param string $output
     * @return string
     */
    public static function sanitizeOutput($output) {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate CSRF token
     * @return string
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check user permission
     * @param string $user_id
     * @param string $permission
     * @return bool
     */
    public static function hasPermission($user_id, $permission) {
        // Implementation depends on your permission system
        // This is a placeholder
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check against user's role
        $user_role = $_SESSION['user_role'] ?? null;
        return self::checkRolePermission($user_role, $permission);
    }

    /**
     * Check role-based permissions
     * @param string $role
     * @param string $permission
     * @return bool
     */
    private static function checkRolePermission($role, $permission) {
        $permissions = [
            'admin' => ['create', 'read', 'update', 'delete', 'manage_users', 'view_reports', 'system_settings'],
            'manager' => ['read', 'update', 'view_reports', 'inventory_management', 'create_po'],
            'cashier' => ['read', 'create_sales', 'view_own_sales'],
            'inventory_staff' => ['read', 'update', 'inventory_management'],
        ];
        
        if (!isset($permissions[$role])) {
            return false;
        }
        
        return in_array($permission, $permissions[$role]);
    }

    /**
     * Validate email format
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     * @param string $url
     * @return bool
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Rate limiting check
     * @param string $identifier (IP, user_id, etc)
     * @param int $attempts
     * @param int $time_window
     * @return bool
     */
    public static function checkRateLimit($identifier, $attempts = 5, $time_window = 900) {
        $key = "rate_limit_" . $identifier;
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'timestamp' => time()
            ];
            return true;
        }
        
        $elapsed = time() - $_SESSION[$key]['timestamp'];
        
        if ($elapsed > $time_window) {
            $_SESSION[$key] = [
                'count' => 1,
                'timestamp' => time()
            ];
            return true;
        }
        
        $_SESSION[$key]['count']++;
        return $_SESSION[$key]['count'] <= $attempts;
    }

    /**
     * Encrypt sensitive data
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt sensitive data
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function decrypt($data, $key) {
        $data = base64_decode($data);
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}
?>
