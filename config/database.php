<?php
/**
 * File: database.php
 * Description: Database connection and configuration
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

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
        $this->connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->db_name
        );

        // Check connection
        if ($this->connection->connect_error) {
            error_log("Connection failed: " . $this->connection->connect_error);
            return null;
        }

        // Set charset
        $this->connection->set_charset($this->charset);

        return $this->connection;
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
