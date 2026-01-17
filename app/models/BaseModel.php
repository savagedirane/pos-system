<?php
/**
 * File: BaseModel.php
 * Description: Base model class with CRUD operations
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

class BaseModel {
    protected $db;
    protected $table = '';
    protected $primary_key = 'id';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all records
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 20, $offset = 0) {
        $query = "SELECT * FROM {$this->table} LIMIT {$limit} OFFSET {$offset}";
        $result = $this->db->query($query);
        
        $records = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
        }
        return $records;
    }

    /**
     * Get single record by ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get total count
     * @return int
     */
    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Create new record
     * @param array $data
     * @return bool|int
     */
    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            return false;
        }

        $values = array_values($data);
        $types = $this->getParameterTypes($values);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }

    /**
     * Update record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $set_clause = implode(', ', array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($data)));

        $query = "UPDATE {$this->table} SET {$set_clause}, updated_at = NOW() WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $values = array_values($data);
        $values[] = $id;
        
        $types = $this->getParameterTypes($values);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }

    /**
     * Delete record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /**
     * Search records
     * @param string $column
     * @param mixed $value
     * @param int $limit
     * @return array
     */
    public function search($column, $value, $limit = 20) {
        $query = "SELECT * FROM {$this->table} WHERE {$column} LIKE ? LIMIT {$limit}";
        $stmt = $this->db->prepare($query);
        
        $search_value = "%{$value}%";
        $stmt->bind_param('s', $search_value);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $records = [];
        
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        
        return $records;
    }

    /**
     * Get parameter types for binding
     * @param array $values
     * @return string
     */
    protected function getParameterTypes($values) {
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
}
?>
