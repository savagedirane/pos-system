<?php
/**
 * File: Product.php
 * Description: Product model for product management
 * Author: POS Development Team
 * Created: 2024-01-17
 * Version: 1.0
 */

require_once 'BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';
    protected $primary_key = 'product_id';

    /**
     * Get product by SKU
     * @param string $sku
     * @return array|null
     */
    public function getBySku($sku) {
        $query = "SELECT * FROM {$this->table} WHERE sku = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get product by barcode
     * @param string $barcode
     * @return array|null
     */
    public function getByBarcode($barcode) {
        $query = "SELECT * FROM {$this->table} WHERE barcode = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $barcode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get products by category
     * @param int $category_id
     * @return array
     */
    public function getByCategory($category_id) {
        $query = "SELECT * FROM {$this->table} WHERE category_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }

    /**
     * Get low stock products
     * @return array
     */
    public function getLowStockProducts() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE quantity_on_hand <= reorder_level AND is_active = 1";
        $result = $this->db->query($query);
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }

    /**
     * Get out of stock products
     * @return array
     */
    public function getOutOfStockProducts() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE quantity_on_hand = 0 AND is_active = 1";
        $result = $this->db->query($query);
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }

    /**
     * Update stock quantity
     * @param int $product_id
     * @param int $quantity_change
     * @return bool
     */
    public function updateStock($product_id, $quantity_change) {
        $query = "UPDATE {$this->table} 
                  SET quantity_on_hand = quantity_on_hand + ?, 
                      updated_at = NOW() 
                  WHERE product_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $quantity_change, $product_id);
        return $stmt->execute();
    }

    /**
     * Search products
     * @param string $search_term
     * @param int $limit
     * @return array
     */
    public function searchProducts($search_term, $limit = 20) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE (product_name LIKE ? OR sku LIKE ? OR barcode LIKE ?) 
                  AND is_active = 1 
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        
        $search = "%{$search_term}%";
        $stmt->bind_param('sssi', $search, $search, $search, $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }

    /**
     * Get top selling products
     * @param int $limit
     * @return array
     */
    public function getTopSellingProducts($limit = 10) {
        $query = "SELECT p.*, SUM(si.quantity) as total_sold 
                  FROM {$this->table} p
                  JOIN sale_items si ON p.product_id = si.product_id
                  GROUP BY p.product_id
                  ORDER BY total_sold DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }

    /**
     * Get total inventory value
     * @return float
     */
    public function getTotalInventoryValue() {
        $query = "SELECT SUM(quantity_on_hand * cost_price) as total_value 
                  FROM {$this->table} 
                  WHERE is_active = 1";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        
        return $row['total_value'] ?? 0;
    }
}
?>
