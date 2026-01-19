-- Seed data for POS IMS
USE pos_ims;

-- Roles
INSERT INTO roles (id, name, guard_name, created_at, updated_at) VALUES
(1, 'Admin', 'web', NOW(), NOW()),
(2, 'Manager', 'web', NOW(), NOW()),
(3, 'Cashier', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Admin user (password: "password")
-- Password hash corresponds to Laravel example bcrypt for "password".
INSERT INTO users (id, name, email, password, is_active, created_at, updated_at) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.OGG0wG6a3QJ9fV2u', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), email=VALUES(email);

-- Assign Admin role to admin user
INSERT INTO role_user (role_id, user_id) VALUES (1,1)
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Example categories
INSERT INTO categories (id, name, slug, description, created_at, updated_at) VALUES
(1, 'General', 'general', 'General products', NOW(), NOW()),
(2, 'Beverages', 'beverages', 'Drinks and beverages', NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Example supplier
INSERT INTO suppliers (id, name, contact_name, phone, email, address, created_at, updated_at) VALUES
(1, 'Acme Supplies', 'Procurement', '+1-555-0100', 'supplies@example.com', '123 Supplier St', NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Example products
INSERT INTO products (id, sku, name, barcode, category_id, description, cost, price, taxable, is_active, created_at, updated_at) VALUES
(1, 'P001', 'Bottled Water 500ml', '1234567890123', 2, '500ml mineral water', 0.30, 0.50, 1, 1, NOW(), NOW()),
(2, 'P002', 'Canned Soda 330ml', '2345678901234', 2, '330ml soda can', 0.40, 0.90, 1, 1, NOW(), NOW()),
(3, 'P003', 'Paper Bag', '3456789012345', 1, 'Small paper bag', 0.02, 0.10, 0, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), price=VALUES(price);

-- Link products to supplier
INSERT INTO product_supplier (product_id, supplier_id, supplier_sku) VALUES
(1,1,'ACM-PW-500'),
(2,1,'ACM-CS-330'),
(3,1,'ACM-PB-001')
ON DUPLICATE KEY UPDATE supplier_sku=VALUES(supplier_sku);

-- Initial stock levels
INSERT INTO stock_levels (product_id, quantity, reserved, reorder_point, location, created_at, updated_at) VALUES
(1, 200.000, 0.000, 20.000, 'default', NOW(), NOW()),
(2, 120.000, 0.000, 10.000, 'default', NOW(), NOW()),
(3, 500.000, 0.000, 50.000, 'default', NOW(), NOW())
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity), updated_at=VALUES(updated_at);

-- Initial stock movements (audit)
INSERT INTO stock_movements (product_id, change_qty, movement_type, reference_type, reference_id, user_id, supplier_id, note, created_at) VALUES
(1, 200.000, 'initial', 'seed', 1, 1, 1, 'Initial stock load', NOW()),
(2, 120.000, 'initial', 'seed', 1, 1, 1, 'Initial stock load', NOW()),
(3, 500.000, 'initial', 'seed', 1, 1, 1, 'Initial stock load', NOW())
ON DUPLICATE KEY UPDATE note=VALUES(note);

-- Simple settings
INSERT INTO settings (`key`, `value`, updated_at) VALUES
('store.name', 'Demo POS Store', NOW()),
('store.currency', 'USD', NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), updated_at=VALUES(updated_at);

-- End of seed data. For more complex seeding, convert these inserts to Laravel seeders and use model factories.
