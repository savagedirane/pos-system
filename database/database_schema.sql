-- =========================================================================
-- POS SYSTEM - DATABASE SCHEMA
-- MySQL 5.7+ / 8.0+
-- =========================================================================

-- Drop existing tables if they exist (for clean installation)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS chatbot_feedback;
DROP TABLE IF EXISTS chatbot_messages;
DROP TABLE IF EXISTS chatbot_conversations;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS discounts;
DROP TABLE IF EXISTS po_items;
DROP TABLE IF EXISTS purchase_orders;
DROP TABLE IF EXISTS return_items;
DROP TABLE IF EXISTS returns;
DROP TABLE IF EXISTS inventory_transactions;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================================
-- TABLE: users
-- Description: User account management for employees and administrators
-- =========================================================================
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
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: categories
-- Description: Product categories with hierarchical support
-- =========================================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_category_name (category_name),
    INDEX idx_parent_category (parent_category_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: suppliers
-- Description: Vendor and supplier information for procurement
-- =========================================================================
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
    INDEX idx_supplier_name (supplier_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: products
-- Description: Product inventory with pricing and stock management
-- =========================================================================
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
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    INDEX idx_sku (sku),
    INDEX idx_product_name (product_name),
    INDEX idx_category (category_id),
    INDEX idx_barcode (barcode),
    INDEX idx_quantity (quantity_on_hand),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: customers
-- Description: Customer database for CRM and loyalty management
-- =========================================================================
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
    INDEX idx_customer_type (customer_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: sales
-- Description: Main transaction records for all point-of-sale activities
-- =========================================================================
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
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_sale_number (sale_number),
    INDEX idx_sale_date (sale_date),
    INDEX idx_customer (customer_id),
    INDEX idx_user (user_id),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: sale_items
-- Description: Individual items in each sales transaction (line items)
-- =========================================================================
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
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_sale (sale_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: inventory_transactions
-- Description: Complete audit trail of all inventory movements
-- =========================================================================
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
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_product (product_id),
    INDEX idx_type (transaction_type),
    INDEX idx_date (created_at),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: returns
-- Description: Track product returns and refunds
-- =========================================================================
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
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_return_number (return_number),
    INDEX idx_sale (sale_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: return_items
-- Description: Individual items being returned
-- =========================================================================
CREATE TABLE return_items (
    return_item_id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES returns(return_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_return (return_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: purchase_orders
-- Description: Orders placed with suppliers for inventory replenishment
-- =========================================================================
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
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_po_number (po_number),
    INDEX idx_supplier (supplier_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: po_items
-- Description: Individual items in purchase orders
-- =========================================================================
CREATE TABLE po_items (
    po_item_id INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_po (po_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: discounts
-- Description: Manage promotional and seasonal discounts
-- =========================================================================
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
    INDEX idx_dates (start_date, end_date),
    INDEX idx_applicable (applicable_to, applicable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: audit_logs
-- Description: System audit trail for security and compliance
-- =========================================================================
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_date (created_at),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: reports
-- Description: Store frequently accessed reports for quick retrieval
-- =========================================================================
CREATE TABLE reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report_name VARCHAR(100) NOT NULL,
    report_type ENUM('sales', 'inventory', 'profitability', 'customer') NOT NULL,
    report_date DATE NOT NULL,
    generated_by INT NOT NULL,
    data_summary JSON,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_type (report_type),
    INDEX idx_date (report_date),
    INDEX idx_generated (generated_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: notifications
-- Description: Alert system for critical inventory and business events
-- =========================================================================
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_type (notification_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: settings
-- Description: Store application settings and preferences
-- =========================================================================
CREATE TABLE settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT NOT NULL,
    setting_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: chatbot_conversations
-- Description: AI chatbot conversation management
-- =========================================================================
CREATE TABLE chatbot_conversations (
    conversation_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    user_id INT,
    conversation_type ENUM('customer_service', 'operations') NOT NULL,
    status ENUM('active', 'closed', 'pending') DEFAULT 'active',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at DATETIME,
    summary TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_user (user_id),
    INDEX idx_type (conversation_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: chatbot_messages
-- Description: Chat messages and responses in chatbot conversations
-- =========================================================================
CREATE TABLE chatbot_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_type ENUM('user', 'bot') NOT NULL,
    message_text TEXT NOT NULL,
    intent VARCHAR(100),
    confidence DECIMAL(3,2),
    response_time INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chatbot_conversations(conversation_id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_type),
    INDEX idx_intent (intent),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- TABLE: chatbot_feedback
-- Description: User feedback on chatbot interactions
-- =========================================================================
CREATE TABLE chatbot_feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT,
    issue_resolved BOOLEAN,
    improvement_suggestions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chatbot_conversations(conversation_id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_rating (rating),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- END OF SCHEMA
-- =========================================================================
