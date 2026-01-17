<?php
/**
 * File: User.php
 * Description: User model for user management
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primary_key = 'user_id';

    /**
     * Get user by username
     * @param string $username
     * @return array|null
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get user by email
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get users by role
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        $query = "SELECT * FROM {$this->table} WHERE role = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $role);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $users = [];
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }

    /**
     * Update last login time
     * @param int $user_id
     * @return bool
     */
    public function updateLastLogin($user_id) {
        $query = "UPDATE {$this->table} SET last_login = NOW() WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        return $stmt->execute();
    }

    /**
     * Check if username exists
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        return $this->getByUsername($username) !== null;
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        return $this->getByEmail($email) !== null;
    }

    /**
     * Create user with hashed password
     * @param array $data
     * @return bool|int
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            unset($data['password']);
        }
        
        return $this->create($data);
    }

    /**
     * Get active users
     * @return array
     */
    public function getActiveUsers() {
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $result = $this->db->query($query);
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
}
?>
