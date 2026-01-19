<?php
/**
 * File: database.php
 * Description: Database connection and configuration
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

// Database Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'pos_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private $host = 'localhost';
    private $db_name = 'pos_system';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $connection;

    /**
     * Get database connection
     * @return mysqli|null
     */
    public function getConnection() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name
            );

            // Check connection
            if ($this->connection->connect_error) {
                $error_msg = "Connection failed: " . $this->connection->connect_error;
                error_log($error_msg);
                throw new Exception($error_msg);
            }

            // Set charset
            $this->connection->set_charset($this->charset);

            // Verify connection is active
            if (!$this->connection->ping()) {
                throw new Exception("Connection ping failed");
            }

            return $this->connection;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Close connection
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Execute prepared statement
     * @param string $query
     * @param array $params
     * @return mysqli_result|bool
     */
    public function execute($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->connection->error);
            return false;
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }

        return $stmt->get_result();
    }
}

// Create database instance
$database = new Database();
?>
