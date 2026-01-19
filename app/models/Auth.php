<?php
/**
 * File: Auth.php
 * Description: Authentication model for user credentials verification
 * Author: POS Development Team
 * Created: 2026-01-17
 * Version: 1.0
 */

require_once __DIR__ . '/../models/User.php';

class Auth {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Authenticate user with username and password
     * 
     * @param string $username Username or email
     * @param string $password Plain text password
     * @return array|null User data if authenticated, null otherwise
     */
    public function authenticate($username, $password) {
        // Try to find user by username or email
        $user = $this->userModel->getByUsername($username);
        
        if (!$user) {
            $user = $this->userModel->getByEmail($username);
        }

        if (!$user) {
            return null;
        }

        // Verify password using bcrypt
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Check if user is active
        if (!$user['is_active']) {
            return null;
        }

        return $user;
    }

    /**
     * Create new user account
     * 
     * @param array $userData User registration data
     * @return int|false User ID if successful, false otherwise
     */
    public function register($userData) {
        // Validate required fields
        if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
            return false;
        }

        // Check if username exists
        if ($this->userModel->usernameExists($userData['username'])) {
            return false;
        }

        // Check if email exists
        if ($this->userModel->emailExists($userData['email'])) {
            return false;
        }

        // Hash password
        $password_hash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        // Create user
        $user_data = [
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password_hash' => $password_hash,
            'first_name' => $userData['first_name'] ?? '',
            'last_name' => $userData['last_name'] ?? '',
            'role' => $userData['role'] ?? 'cashier',
            'phone' => $userData['phone'] ?? '',
            'is_active' => 1
        ];

        return $this->userModel->create($user_data);
    }

    /**
     * Validate password strength
     * 
     * @param string $password Password to validate
     * @return array Validation result with 'valid' and 'errors'
     */
    public function validatePassword($password) {
        $result = ['valid' => true, 'errors' => []];

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $result['valid'] = false;
            $result['errors'][] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = "Password must contain at least one uppercase letter";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = "Password must contain at least one lowercase letter";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = "Password must contain at least one number";
        }

        if (!preg_match('/[!@#$%^&*]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = "Password must contain at least one special character (!@#$%^&*)";
        }

        return $result;
    }

    /**
     * Change user password
     * 
     * @param int $user_id User ID
     * @param string $old_password Current password
     * @param string $new_password New password
     * @return bool True if successful
     */
    public function changePassword($user_id, $old_password, $new_password) {
        $user = $this->userModel->getById($user_id);

        if (!$user) {
            return false;
        }

        // Verify old password
        if (!password_verify($old_password, $user['password_hash'])) {
            return false;
        }

        // Validate new password
        $validation = $this->validatePassword($new_password);
        if (!$validation['valid']) {
            return false;
        }

        // Hash new password
        $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Update password
        return $this->userModel->update($user_id, ['password_hash' => $new_hash]);
    }

    /**
     * Reset password (for admin/forgot password)
     * 
     * @param int $user_id User ID
     * @param string $new_password New password to set
     * @return bool True if successful
     */
    public function resetPassword($user_id, $new_password) {
        // Validate new password
        $validation = $this->validatePassword($new_password);
        if (!$validation['valid']) {
            return false;
        }

        // Hash password
        $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Update password
        return $this->userModel->update($user_id, ['password_hash' => $new_hash]);
    }

    /**
     * Verify authentication token
     * 
     * @param string $token Token to verify
     * @return array|null Decoded token data or null
     */
    public function verifyToken($token) {
        // This would typically verify a JWT token
        // For now, we'll use session-based auth
        // Implement this when adding API authentication
        return null;
    }
}

?>
