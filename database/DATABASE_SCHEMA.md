# POS System - Database Schema Design

## Overview
This document outlines the complete relational database schema for the web-based Point of Sale (POS) system, designed using MySQL as the RDBMS.

---

## Table Structure

### 1. **users** - User Account Management
Stores employee/admin login credentials and role information.

```sql
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'manager', 'cashier', 'inventory_staff') NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);
```

### 2. **categories** - Product Categories
Hierarchical categorization of products for better inventory management.

```sql
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id),
    INDEX idx_category_name (category_name),
    INDEX idx_parent_category (parent_category_id)
);
```

### 3. **products** - Product Inventory
Comprehensive product information with pricing and stock management.

```sql
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(150) NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    quantity_on_hand INT DEFAULT 0,
    reorder_level INT DEFAULT 10,
    reorder_quantity INT DEFAULT 50,
    supplier_id INT,
    barcode VARCHAR(100) UNIQUE,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    is_discontinued BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id),
    INDEX idx_sku (sku),
    INDEX idx_product_name (product_name),
    INDEX idx_category (category_id),
    INDEX idx_barcode (barcode),
    INDEX idx_quantity (quantity_on_hand)
);
```

### 4. **suppliers** - Supplier Management
Vendor and supplier information for procurement.

```sql
CREATE TABLE suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(150) NOT NULL UNIQUE,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    payment_terms VARCHAR(100),
    tax_id VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_name (supplier_name)
);
```

### 5. **customers** - Customer Database
Persistent customer information for relationship management and loyalty programs.

```sql
CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    customer_type ENUM('regular', 'vip', 'wholesale') DEFAULT 'regular',
    loyalty_points INT DEFAULT 0,
    total_purchases DECIMAL(12,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_customer_type (customer_type)
);
```

### 6. **sales** - Sales Transactions
Main transaction record for all point-of-sale activities.

```sql
CREATE TABLE sales (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    sale_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    user_id INT NOT NULL,
    sale_date DATETIME NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'check', 'mobile_wallet') DEFAULT 'cash',
    payment_status ENUM('pending', 'completed', 'refunded') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_sale_number (sale_number),
    INDEX idx_sale_date (sale_date),
    INDEX idx_customer (customer_id),
    INDEX idx_user (user_id),
    INDEX idx_payment_status (payment_status)
);
```

### 7. **sale_items** - Sales Line Items
Individual items in each sales transaction.

```sql
CREATE TABLE sale_items (
    sale_item_id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    INDEX idx_sale (sale_id),
    INDEX idx_product (product_id)
);
```

### 8. **inventory_transactions** - Inventory Audit Trail
Complete history of all inventory movements for reconciliation and auditing.

```sql
CREATE TABLE inventory_transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    transaction_type ENUM('purchase', 'sale', 'adjustment', 'return', 'damage') NOT NULL,
    quantity_change INT NOT NULL,
    reference_id INT,
    reference_type VARCHAR(50),
    notes TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_product (product_id),
    INDEX idx_type (transaction_type),
    INDEX idx_date (created_at)
);
```

### 9. **returns** - Sales Returns Management
Track product returns and refunds.

```sql
CREATE TABLE returns (
    return_id INT PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) UNIQUE NOT NULL,
    sale_id INT NOT NULL,
    return_date DATETIME NOT NULL,
    reason ENUM('defective', 'wrong_item', 'customer_request', 'damaged') NOT NULL,
    refund_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'processed') DEFAULT 'pending',
    approved_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id),
    INDEX idx_return_number (return_number),
    INDEX idx_sale (sale_id),
    INDEX idx_status (status)
);
```

### 10. **return_items** - Return Line Items
Individual items being returned.

```sql
CREATE TABLE return_items (
    return_item_id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES returns(return_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    INDEX idx_return (return_id),
    INDEX idx_product (product_id)
);
```

### 11. **purchase_orders** - Supplier Purchase Orders
Track orders placed with suppliers for inventory replenishment.

```sql
CREATE TABLE purchase_orders (
    po_id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    order_date DATETIME NOT NULL,
    expected_delivery_date DATETIME,
    actual_delivery_date DATETIME,
    subtotal DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0,
    shipping_cost DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('draft', 'placed', 'received', 'cancelled') DEFAULT 'draft',
    user_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_po_number (po_number),
    INDEX idx_supplier (supplier_id),
    INDEX idx_status (status)
);
```

### 12. **po_items** - Purchase Order Line Items
Individual items in purchase orders.

```sql
CREATE TABLE po_items (
    po_item_id INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    INDEX idx_po (po_id),
    INDEX idx_product (product_id)
);
```

### 13. **discounts** - Promotional Discounts
Manage promotional and seasonal discounts.

```sql
CREATE TABLE discounts (
    discount_id INT PRIMARY KEY AUTO_INCREMENT,
    discount_name VARCHAR(100) NOT NULL,
    discount_type ENUM('percentage', 'fixed_amount') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    applicable_to ENUM('product', 'category', 'customer_type', 'minimum_purchase') NOT NULL,
    applicable_id INT,
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    usage_limit INT,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_dates (start_date, end_date)
);
```

### 14. **audit_logs** - System Audit Trail
Comprehensive logging of all system activities for security and compliance.

```sql
CREATE TABLE audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user (user_id),
    INDEX idx_date (created_at),
    INDEX idx_entity (entity_type, entity_id)
);
```

### 15. **reports** - Pre-generated Reports
Store frequently accessed reports for quick retrieval.

```sql
CREATE TABLE reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report_name VARCHAR(100) NOT NULL,
    report_type ENUM('sales', 'inventory', 'profitability', 'customer') NOT NULL,
    report_date DATE NOT NULL,
    generated_by INT NOT NULL,
    data_summary JSON,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(user_id),
    INDEX idx_type (report_type),
    INDEX idx_date (report_date)
);
```

### 16. **notifications** - User Notifications
Alert system for critical inventory and business events.

```sql
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    notification_type ENUM('low_stock', 'sale_alert', 'system_alert', 'reminder') NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    reference_id INT,
    reference_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_type (notification_type)
);
```

### 17. **settings** - System Configuration
Store application settings and preferences.

```sql
CREATE TABLE settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT NOT NULL,
    setting_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
);
```

---

## Relationships & Constraints

### Primary Key Relationships
- Users (admin, manager, cashier, inventory staff)
- Products with Categories and Suppliers
- Customers linked to Sales
- Sales with line items (Sale Items)
- Purchase Orders from Suppliers
- Inventory Transaction Audit Trail
- Returns Management

### Foreign Key Constraints
All foreign keys use standard referential integrity with appropriate cascade delete rules.

### Indexes
Strategic indexes on frequently queried columns:
- User authentication (username, email)
- Sales date ranges
- Product SKU and barcode lookups
- Category hierarchies
- Payment and order status

---

## Data Types & Constraints

| Data Type | Usage |
|-----------|-------|
| INT | IDs, quantities, counts |
| VARCHAR | Names, codes, emails |
| DECIMAL(10,2) | Prices, costs |
| DECIMAL(12,2) | Totals and aggregates |
| BOOLEAN | Flags and status |
| ENUM | Predefined status values |
| DATETIME | Transaction timestamps |
| TEXT/LONGTEXT | Descriptions, notes, JSON data |
| JSON | Complex data storage (audit logs) |

---

## Normalization
Database is normalized to 3NF (Third Normal Form):
- Elimination of insertion, update, and deletion anomalies
- Proper decomposition of attributes
- Referential integrity maintained throughout

---

## Scaling Considerations
- Partitioning sales and inventory_transactions by date
- Archival strategy for old transactions
- Read replicas for reporting
- Indexing strategy for query optimization
