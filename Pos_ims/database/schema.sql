-- POS IMS MySQL schema (MySQL 5.7+/8.0+). Use InnoDB for FK and transactions.
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS pos_ims CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
USE pos_ims;

-- Roles
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  guard_name VARCHAR(100) DEFAULT 'web',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- role_user pivot
DROP TABLE IF EXISTS role_user;
CREATE TABLE role_user (
  role_id INT NOT NULL,
  user_id BIGINT NOT NULL,
  PRIMARY KEY (role_id, user_id),
  CONSTRAINT fk_role_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_role_user_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(180) DEFAULT NULL,
  description TEXT,
  parent_id INT DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Suppliers
DROP TABLE IF EXISTS suppliers;
CREATE TABLE suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  contact_name VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products
DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(100) DEFAULT NULL,
  name VARCHAR(255) NOT NULL,
  barcode VARCHAR(120) DEFAULT NULL,
  category_id INT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  cost DECIMAL(13,2) DEFAULT 0.00,
  price DECIMAL(13,2) DEFAULT 0.00,
  taxable TINYINT(1) DEFAULT 1,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_barcode ON products(barcode);

-- product_supplier pivot
DROP TABLE IF EXISTS product_supplier;
CREATE TABLE product_supplier (
  product_id BIGINT NOT NULL,
  supplier_id INT NOT NULL,
  supplier_sku VARCHAR(120) DEFAULT NULL,
  PRIMARY KEY (product_id, supplier_id),
  CONSTRAINT fk_ps_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_ps_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stock levels (current available per product)
DROP TABLE IF EXISTS stock_levels;
CREATE TABLE stock_levels (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT NOT NULL,
  quantity DECIMAL(13,3) NOT NULL DEFAULT 0,
  reserved DECIMAL(13,3) NOT NULL DEFAULT 0,
  reorder_point DECIMAL(13,3) DEFAULT 0,
  location VARCHAR(120) DEFAULT 'default',
  updated_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_stock_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY uq_stock_product_location (product_id, location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stock Movements (audit trail)
DROP TABLE IF EXISTS stock_movements;
CREATE TABLE stock_movements (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT NOT NULL,
  change_qty DECIMAL(13,3) NOT NULL,
  movement_type ENUM('purchase','sale','adjustment','transfer','return','initial') NOT NULL,
  reference_type VARCHAR(100) DEFAULT NULL,
  reference_id BIGINT DEFAULT NULL,
  user_id BIGINT DEFAULT NULL,
  supplier_id INT DEFAULT NULL,
  note TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sm_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_sm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_sm_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_sm_product ON stock_movements(product_id);

-- Sales (header)
DROP TABLE IF EXISTS sales;
CREATE TABLE sales (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  sale_number VARCHAR(80) NOT NULL UNIQUE,
  user_id BIGINT NOT NULL,
  customer_name VARCHAR(255) DEFAULT NULL,
  subtotal DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  tax_total DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  discount_total DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  total_amount DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  payment_status ENUM('pending','paid','partial','refunded') DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_sales_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sale line items
DROP TABLE IF EXISTS sale_items;
CREATE TABLE sale_items (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  sale_id BIGINT NOT NULL,
  product_id BIGINT NOT NULL,
  quantity DECIMAL(13,3) NOT NULL DEFAULT 1,
  unit_price DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  line_discount DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  line_tax DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  line_total DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT fk_si_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  CONSTRAINT fk_si_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);

-- Payments
DROP TABLE IF EXISTS payments;
CREATE TABLE payments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  sale_id BIGINT NOT NULL,
  method VARCHAR(60) NOT NULL,
  amount DECIMAL(13,2) NOT NULL DEFAULT 0.00,
  transaction_reference VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pay_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit logs (generic)
DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT DEFAULT NULL,
  action VARCHAR(120) NOT NULL,
  auditable_type VARCHAR(150) DEFAULT NULL,
  auditable_id BIGINT DEFAULT NULL,
  before_json JSON DEFAULT NULL,
  after_json JSON DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings (key-value)
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
  `key` VARCHAR(150) PRIMARY KEY,
  `value` TEXT DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;

-- Notes:
-- - Use `stock_levels` as the canonical available quantity. Update it via transactions and write matching `stock_movements` records.
-- - For Laravel migrations, translate these CREATE TABLE statements into migration files and add seeders for roles and admin user.
