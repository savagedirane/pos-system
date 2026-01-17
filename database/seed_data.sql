-- =========================================================================
-- POS SYSTEM - SEED DATA
-- Sample data for development and testing
-- =========================================================================

-- =========================================================================
-- SEED DATA: users
-- =========================================================================
INSERT INTO users (username, email, password_hash, first_name, last_name, role, phone, is_active) VALUES
('admin_user', 'admin@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Admin', 'User', 'admin', '555-0001', TRUE),
('manager_john', 'john.manager@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'John', 'Manager', 'manager', '555-0002', TRUE),
('manager_sarah', 'sarah.manager@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Sarah', 'Manager', 'manager', '555-0003', TRUE),
('cashier_alice', 'alice.cashier@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Alice', 'Cashier', 'cashier', '555-0004', TRUE),
('cashier_bob', 'bob.cashier@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Bob', 'Cashier', 'cashier', '555-0005', TRUE),
('cashier_emily', 'emily.cashier@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Emily', 'Cashier', 'cashier', '555-0006', TRUE),
('inventory_mike', 'mike.inventory@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Mike', 'Inventory Staff', 'inventory_staff', '555-0007', TRUE),
('inventory_lisa', 'lisa.inventory@pos-system.local', '$2y$12$abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234', 'Lisa', 'Inventory Staff', 'inventory_staff', '555-0008', TRUE);

-- =========================================================================
-- SEED DATA: categories
-- =========================================================================
INSERT INTO categories (category_name, description, is_active, display_order) VALUES
('Electronics', 'Electronic devices and gadgets', TRUE, 1),
('Clothing', 'Apparel and fashion items', TRUE, 2),
('Home & Garden', 'Home furnishings and garden supplies', TRUE, 3),
('Sports & Outdoors', 'Sports equipment and outdoor gear', TRUE, 4),
('Health & Beauty', 'Health, fitness, and beauty products', TRUE, 5),
('Books & Media', 'Books, DVDs, and digital media', TRUE, 6),
('Toys & Games', 'Toys, games, and hobbies', TRUE, 7),
('Food & Beverages', 'Grocery items and beverages', TRUE, 8);

-- Insert subcategories
INSERT INTO categories (category_name, description, parent_category_id, is_active, display_order) VALUES
('Laptops', 'Desktop and laptop computers', 1, TRUE, 1),
('Smartphones', 'Mobile phones and accessories', 1, TRUE, 2),
('Tablets', 'Tablet computers and e-readers', 1, TRUE, 3),
('Mens Clothing', 'Clothing for men', 2, TRUE, 1),
('Womens Clothing', 'Clothing for women', 2, TRUE, 2),
('Furniture', 'Tables, chairs, and furniture', 3, TRUE, 1),
('Gardening Tools', 'Tools for gardening and landscaping', 3, TRUE, 2);

-- =========================================================================
-- SEED DATA: suppliers
-- =========================================================================
INSERT INTO suppliers (supplier_name, contact_person, email, phone, city, state, country, payment_terms, tax_id) VALUES
('TechSupply Corp', 'John Smith', 'sales@techsupply.com', '555-1000', 'New York', 'NY', 'USA', 'Net 30', 'TS-123456789'),
('Fashion Imports Ltd', 'Maria Garcia', 'orders@fashionimports.com', '555-2000', 'Los Angeles', 'CA', 'USA', 'Net 45', 'FI-987654321'),
('Home Goods International', 'Robert Chen', 'purchasing@homegoods-intl.com', '555-3000', 'Chicago', 'IL', 'USA', 'Net 30', 'HG-456789123'),
('Outdoor Equipment Co', 'David Wilson', 'wholesale@outdooreq.com', '555-4000', 'Denver', 'CO', 'USA', 'Net 60', 'OE-654321987'),
('Health & Wellness Supplier', 'Jennifer Lee', 'sales@healthwell.com', '555-5000', 'Miami', 'FL', 'USA', 'Net 30', 'HW-789123456'),
('Book & Media Distributors', 'Michael Brown', 'books@mediadistr.com', '555-6000', 'Boston', 'MA', 'USA', 'Net 45', 'BM-321654987'),
('Toys & Games Wholesaler', 'Amanda White', 'toys@toyswhole.com', '555-7000', 'Dallas', 'TX', 'USA', 'Net 30', 'TG-123789654');

-- =========================================================================
-- SEED DATA: products
-- =========================================================================
INSERT INTO products (product_name, sku, category_id, description, cost_price, selling_price, quantity_on_hand, reorder_level, reorder_quantity, supplier_id, barcode, is_active) VALUES
-- Electronics - Laptops
('Dell XPS 13 Laptop', 'LAPTOP-DELL-001', 9, 'High-performance 13-inch laptop', 800.00, 1299.99, 15, 5, 10, 1, '4938271849', TRUE),
('HP Pavilion 15 Laptop', 'LAPTOP-HP-001', 9, 'Everyday laptop for work and play', 600.00, 899.99, 20, 5, 10, 1, '5847293847', TRUE),
('Lenovo ThinkPad X1', 'LAPTOP-LNOV-001', 9, 'Professional business laptop', 750.00, 1199.99, 12, 5, 10, 1, '2938472983', TRUE),

-- Electronics - Smartphones
('iPhone 15 Pro', 'PHONE-APPLE-001', 10, 'Latest Apple smartphone', 900.00, 1299.99, 8, 3, 5, 1, '9283748293', TRUE),
('Samsung Galaxy S24', 'PHONE-SAMSUNG-001', 10, 'Premium Android smartphone', 850.00, 1199.99, 10, 3, 5, 1, '8374928374', TRUE),
('Google Pixel 8', 'PHONE-GOOGLE-001', 10, 'Google flagship phone', 800.00, 1099.99, 12, 3, 5, 1, '7384928374', TRUE),

-- Electronics - Tablets
('iPad Pro 12.9"', 'TABLET-APPLE-001', 11, 'Professional tablet computer', 700.00, 1099.99, 6, 3, 5, 1, '6374928374', TRUE),
('Samsung Galaxy Tab S9', 'TABLET-SAMSUNG-001', 11, 'Premium Android tablet', 500.00, 799.99, 8, 3, 5, 1, '5364928374', TRUE),

-- Clothing - Mens
('Cotton T-Shirt Blue', 'SHIRT-COTTON-001', 12, 'Basic blue cotton t-shirt', 8.00, 19.99, 150, 20, 100, 2, '4354928374', TRUE),
('Denim Jeans Blue', 'JEANS-DENIM-001', 12, 'Classic blue denim jeans', 25.00, 59.99, 85, 15, 50, 2, '3344928374', TRUE),
('Wool Sweater Grey', 'SWEATER-WOOL-001', 12, 'Warm grey wool sweater', 30.00, 79.99, 45, 10, 30, 2, '2334928374', TRUE),

-- Clothing - Womens
('Cotton Blouse Pink', 'BLOUSE-COTTON-001', 13, 'Elegant pink cotton blouse', 15.00, 39.99, 120, 15, 60, 2, '1324928374', TRUE),
('Black Dress Elegant', 'DRESS-ELEGANT-001', 13, 'Formal black evening dress', 45.00, 129.99, 35, 10, 25, 2, '0314928374', TRUE),

-- Home & Garden - Furniture
('Wooden Dining Table', 'TABLE-WOOD-001', 14, 'Solid wood 6-seater dining table', 300.00, 799.99, 5, 2, 5, 3, '9204928374', TRUE),
('Office Chair Ergonomic', 'CHAIR-ERGO-001', 14, 'Adjustable ergonomic office chair', 150.00, 399.99, 20, 5, 10, 3, '8194928374', TRUE),
('Bookshelf Unit', 'SHELF-BOOK-001', 14, 'Modern 5-shelf bookcase', 120.00, 299.99, 12, 5, 8, 3, '7184928374', TRUE),

-- Home & Garden - Gardening Tools
('Garden Spade', 'SPADE-GARDEN-001', 15, 'Professional garden spade', 15.00, 34.99, 80, 20, 50, 3, '6174928374', TRUE),
('Pruning Shears', 'SHEARS-PRUNE-001', 15, 'High-quality pruning shears', 20.00, 49.99, 60, 15, 40, 3, '5164928374', TRUE),
('Watering Can 5L', 'CAN-WATER-001', 15, 'Durable 5-liter watering can', 8.00, 19.99, 100, 25, 60, 3, '4154928374', TRUE),

-- Sports & Outdoors
('Running Shoes', 'SHOES-RUNNING-001', 4, 'Professional running shoes', 60.00, 149.99, 75, 20, 50, 4, '3144928374', TRUE),
('Yoga Mat Premium', 'MAT-YOGA-001', 4, '6mm premium yoga mat', 25.00, 59.99, 90, 20, 50, 4, '2134928374', TRUE),
('Bicycle Mountain 21-speed', 'BIKE-MOUNT-001', 4, 'Full suspension mountain bike', 250.00, 699.99, 8, 3, 5, 4, '1124928374', TRUE),

-- Health & Beauty
('Vitamin C Supplement', 'VITE-C-001', 5, 'High-potency vitamin C 500mg', 12.00, 24.99, 200, 50, 100, 5, '0114928374', TRUE),
('Face Moisturizer Gel', 'CREME-MOIST-001', 5, 'Hydrating facial moisturizer', 18.00, 44.99, 85, 20, 50, 5, '9004928374', TRUE),
('Protein Powder Vanilla', 'POWDER-PROT-001', 5, 'Whey protein powder 2kg', 35.00, 79.99, 45, 10, 30, 5, '8904928374', TRUE),

-- Books & Media
('The Great Gatsby Novel', 'BOOK-GATSBY-001', 6, 'Classic novel by F. Scott Fitzgerald', 8.00, 14.99, 120, 30, 60, 6, '7804928374', TRUE),
('Python Programming Guide', 'BOOK-PYTHON-001', 6, 'Comprehensive Python programming book', 35.00, 69.99, 40, 10, 20, 6, '6704928374', TRUE),
('Movie DVD Action Pack', 'DVD-ACTION-001', 6, 'Set of 3 action movies on DVD', 15.00, 29.99, 60, 15, 30, 6, '5604928374', TRUE),

-- Toys & Games
('Board Game Strategy', 'GAME-BOARD-001', 7, 'Classic strategy board game', 20.00, 44.99, 50, 15, 30, 7, '4504928374', TRUE),
('Action Figure Collectible', 'TOY-ACTION-001', 7, 'Limited edition action figure', 15.00, 34.99, 70, 20, 40, 7, '3404928374', TRUE);

-- =========================================================================
-- SEED DATA: customers
-- =========================================================================
INSERT INTO customers (customer_name, email, phone, city, state, country, customer_type, loyalty_points, total_purchases) VALUES
('John Anderson', 'john.anderson@email.com', '555-1100', 'New York', 'NY', 'USA', 'vip', 5000, 12500.00),
('Mary Johnson', 'mary.johnson@email.com', '555-1101', 'Los Angeles', 'CA', 'USA', 'regular', 1200, 3400.00),
('Robert Davis', 'robert.davis@email.com', '555-1102', 'Chicago', 'IL', 'USA', 'regular', 800, 2100.00),
('Patricia Wilson', 'patricia.wilson@email.com', '555-1103', 'Houston', 'TX', 'USA', 'vip', 4500, 11000.00),
('Michael Brown', 'michael.brown@email.com', '555-1104', 'Phoenix', 'AZ', 'USA', 'wholesale', 8000, 25000.00),
('Jennifer Garcia', 'jennifer.garcia@email.com', '555-1105', 'Philadelphia', 'PA', 'USA', 'regular', 600, 1800.00),
('William Miller', 'william.miller@email.com', '555-1106', 'San Antonio', 'TX', 'USA', 'vip', 3500, 8500.00),
('Elizabeth Taylor', 'elizabeth.taylor@email.com', '555-1107', 'San Diego', 'CA', 'USA', 'regular', 400, 950.00),
('David Martinez', 'david.martinez@email.com', '555-1108', 'Dallas', 'TX', 'USA', 'wholesale', 7500, 22000.00),
('Sarah White', 'sarah.white@email.com', '555-1109', 'San Jose', 'CA', 'USA', 'regular', 500, 1200.00);

-- =========================================================================
-- SEED DATA: sales
-- =========================================================================
INSERT INTO sales (sale_number, customer_id, user_id, sale_date, subtotal, tax_amount, discount_amount, total_amount, payment_method, payment_status) VALUES
('SALE-20260101-001', 1, 4, '2026-01-15 09:30:00', 1299.99, 104.00, 0.00, 1403.99, 'card', 'completed'),
('SALE-20260101-002', 2, 4, '2026-01-15 10:15:00', 299.96, 24.00, 0.00, 323.96, 'cash', 'completed'),
('SALE-20260101-003', 3, 5, '2026-01-15 11:00:00', 799.98, 64.00, 0.00, 863.98, 'card', 'completed'),
('SALE-20260101-004', 4, 5, '2026-01-15 14:30:00', 1199.99, 96.00, 50.00, 1245.99, 'card', 'completed'),
('SALE-20260102-001', 5, 6, '2026-01-16 09:00:00', 2499.96, 200.00, 100.00, 2599.96, 'card', 'completed'),
('SALE-20260102-002', 6, 4, '2026-01-16 13:45:00', 59.97, 4.80, 0.00, 64.77, 'cash', 'completed'),
('SALE-20260102-003', 7, 5, '2026-01-16 15:20:00', 899.98, 72.00, 0.00, 971.98, 'card', 'completed'),
('SALE-20260103-001', 8, 6, '2026-01-17 10:30:00', 149.99, 12.00, 0.00, 161.99, 'cash', 'completed'),
('SALE-20260103-002', 9, 4, '2026-01-17 12:00:00', 3599.94, 288.00, 200.00, 3687.94, 'card', 'completed'),
('SALE-20260103-003', 10, 5, '2026-01-17 16:45:00', 199.98, 16.00, 0.00, 215.98, 'card', 'completed');

-- =========================================================================
-- SEED DATA: sale_items
-- =========================================================================
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total, discount_percent) VALUES
(1, 1, 1, 1299.99, 1299.99, 0),
(2, 7, 2, 149.99, 299.98, 0),
(3, 5, 1, 799.99, 799.98, 0),
(4, 13, 1, 1199.99, 1199.99, 0),
(5, 2, 1, 899.99, 899.99, 0),
(5, 4, 1, 1299.99, 1299.99, 0),
(5, 6, 1, 299.99, 299.99, 0),
(6, 10, 3, 19.99, 59.97, 0),
(7, 16, 2, 449.99, 899.98, 0),
(8, 21, 1, 149.99, 149.99, 0),
(9, 3, 1, 1199.99, 1199.99, 0),
(9, 4, 1, 1299.99, 1299.99, 0),
(9, 5, 1, 799.99, 799.99, 0),
(9, 1, 1, 1299.99, 1299.99, 0),
(10, 22, 2, 99.99, 199.98, 0);

-- =========================================================================
-- SEED DATA: inventory_transactions
-- =========================================================================
INSERT INTO inventory_transactions (product_id, transaction_type, quantity_change, reference_type, user_id, notes) VALUES
(1, 'purchase', 15, 'PO', 7, 'Initial stock from supplier'),
(2, 'sale', -1, 'SALE', 4, 'Sold to customer'),
(3, 'adjustment', 5, 'INVENTORY', 8, 'Stock adjustment after count'),
(4, 'sale', -2, 'SALE', 5, 'Sold to customer'),
(5, 'purchase', 10, 'PO', 7, 'Restocking'),
(6, 'damage', -1, 'INCIDENT', 8, 'Item damaged in warehouse'),
(7, 'sale', -3, 'SALE', 6, 'Sold to customers'),
(8, 'purchase', 20, 'PO', 7, 'New shipment received'),
(9, 'sale', -1, 'SALE', 4, 'Sold to customer'),
(10, 'adjustment', 10, 'INVENTORY', 8, 'Stock reconciliation');

-- =========================================================================
-- SEED DATA: purchase_orders
-- =========================================================================
INSERT INTO purchase_orders (po_number, supplier_id, order_date, expected_delivery_date, status, user_id, subtotal, tax_amount, shipping_cost, total_amount) VALUES
('PO-20260101', 1, '2026-01-10 10:00:00', '2026-01-20 00:00:00', 'placed', 7, 3200.00, 256.00, 50.00, 3506.00),
('PO-20260102', 2, '2026-01-12 14:00:00', '2026-01-22 00:00:00', 'placed', 8, 1500.00, 120.00, 30.00, 1650.00),
('PO-20260103', 3, '2026-01-13 09:00:00', '2026-01-25 00:00:00', 'received', 7, 2000.00, 160.00, 40.00, 2200.00),
('PO-20260104', 4, '2026-01-14 11:00:00', '2026-01-28 00:00:00', 'draft', 8, 1200.00, 96.00, 25.00, 1321.00),
('PO-20260105', 5, '2026-01-15 15:00:00', '2026-01-30 00:00:00', 'placed', 7, 800.00, 64.00, 20.00, 884.00);

-- =========================================================================
-- SEED DATA: po_items
-- =========================================================================
INSERT INTO po_items (po_id, product_id, quantity_ordered, quantity_received, unit_price, line_total) VALUES
(1, 1, 5, 5, 800.00, 4000.00),
(1, 2, 3, 3, 600.00, 1800.00),
(1, 3, 2, 0, 750.00, 1500.00),
(2, 10, 30, 30, 19.99, 599.70),
(2, 11, 20, 20, 59.99, 1199.80),
(3, 14, 5, 5, 799.99, 3999.95),
(3, 15, 8, 8, 399.99, 3199.92),
(4, 21, 10, 0, 149.99, 1499.90),
(4, 22, 15, 0, 34.99, 524.85),
(5, 26, 50, 50, 14.99, 749.50);

-- =========================================================================
-- SEED DATA: discounts
-- =========================================================================
INSERT INTO discounts (discount_name, discount_type, discount_value, applicable_to, applicable_id, start_date, end_date, is_active, usage_limit) VALUES
('Electronics Summer Sale', 'percentage', 15.00, 'category', 1, '2026-06-01 00:00:00', '2026-08-31 23:59:59', FALSE, 1000),
('VIP Customer Discount', 'percentage', 10.00, 'customer_type', 2, '2026-01-01 00:00:00', '2026-12-31 23:59:59', TRUE, NULL),
('Clearance - Old Stock', 'fixed_amount', 20.00, 'product', 5, '2026-01-15 00:00:00', '2026-02-28 23:59:59', TRUE, 500),
('Bulk Purchase Discount', 'percentage', 8.00, 'minimum_purchase', 500, '2026-01-01 00:00:00', '2026-12-31 23:59:59', TRUE, NULL),
('New Year Special', 'percentage', 20.00, 'category', 4, '2026-01-01 00:00:00', '2026-01-31 23:59:59', TRUE, 800);

-- =========================================================================
-- SEED DATA: notifications
-- =========================================================================
INSERT INTO notifications (user_id, notification_type, title, message, is_read, reference_type) VALUES
(7, 'low_stock', 'Low Stock Alert', 'Product SKU-001 is below reorder level', FALSE, 'product'),
(8, 'low_stock', 'Low Stock Alert', 'Product SKU-004 is below reorder level', FALSE, 'product'),
(4, 'sale_alert', 'Large Sale Completed', 'Sale SALE-20260103-002 for $3,687.94 completed', TRUE, 'sale'),
(5, 'sale_alert', 'Sale Completed', 'Sale SALE-20260103-003 for $215.98 completed', TRUE, 'sale'),
(7, 'system_alert', 'Purchase Order Due', 'PO-20260101 expected delivery in 3 days', FALSE, 'purchase_order');

-- =========================================================================
-- SEED DATA: settings
-- =========================================================================
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('store_name', 'Main Retail Store', 'string'),
('store_address', '123 Commerce Street, New York, NY 10001', 'string'),
('store_phone', '555-0000', 'string'),
('tax_rate', '8.00', 'decimal'),
('currency', 'USD', 'string'),
('date_format', 'YYYY-MM-DD', 'string'),
('time_format', 'HH:MM:SS', 'string'),
('receipt_footer', 'Thank you for your business!', 'string'),
('loyalty_points_rate', '1', 'decimal'),
('max_login_attempts', '5', 'integer'),
('session_timeout', '3600', 'integer'),
('enable_barcode_scanner', 'true', 'boolean'),
('enable_chatbot', 'true', 'boolean'),
('minimum_order_amount', '0.00', 'decimal'),
('inventory_warning_threshold', '10', 'integer');

-- =========================================================================
-- SEED DATA: chatbot_conversations
-- =========================================================================
INSERT INTO chatbot_conversations (customer_id, user_id, conversation_type, status, started_at, summary) VALUES
(1, NULL, 'customer_service', 'closed', '2026-01-15 10:30:00', 'Customer inquiry about product warranty'),
(2, NULL, 'customer_service', 'closed', '2026-01-16 14:00:00', 'Order status tracking'),
(NULL, 7, 'operations', 'active', '2026-01-17 09:00:00', 'Inventory reconciliation assistance'),
(4, NULL, 'customer_service', 'active', '2026-01-17 11:30:00', 'Product recommendation request'),
(NULL, 8, 'operations', 'closed', '2026-01-16 15:00:00', 'Purchase order processing');

-- =========================================================================
-- SEED DATA: chatbot_messages
-- =========================================================================
INSERT INTO chatbot_messages (conversation_id, sender_type, message_text, intent, confidence, response_time) VALUES
(1, 'user', 'What is the warranty period for electronics?', 'warranty_inquiry', 0.95, 1200),
(1, 'bot', 'Electronics typically come with a 1-year manufacturer warranty. For extended coverage, we offer optional protection plans.', 'warranty_info', 0.92, 800),
(2, 'user', 'Where is my order SALE-20260102-001?', 'order_status', 0.98, 950),
(2, 'bot', 'Your order is being prepared for shipment and should arrive within 2-3 business days.', 'order_status_info', 0.89, 750),
(3, 'user', 'How many units of SKU-001 are in stock?', 'inventory_check', 0.93, 1100),
(3, 'bot', 'SKU-001 has 15 units currently in stock.', 'inventory_info', 0.96, 600),
(4, 'user', 'Recommend a good running shoe', 'product_recommendation', 0.87, 2500),
(4, 'bot', 'Based on your profile, I recommend our Running Shoes which are highly rated and competitively priced.', 'product_recommendation_info', 0.84, 1800),
(5, 'user', 'Process purchase order', 'po_processing', 0.91, 1500),
(5, 'bot', 'Purchase order PO-20260101 has been successfully processed and sent to the supplier.', 'po_confirmation', 0.88, 900);

-- =========================================================================
-- SEED DATA: chatbot_feedback
-- =========================================================================
INSERT INTO chatbot_feedback (conversation_id, rating, feedback_text, issue_resolved) VALUES
(1, 5, 'Very helpful information about warranty options', TRUE),
(2, 4, 'Good response but could be faster', TRUE),
(3, 5, 'Exactly the information I needed', TRUE),
(4, 3, 'Helpful but limited options provided', FALSE),
(5, 5, 'Perfect for streamlining our workflow', TRUE);

-- =========================================================================
-- SEED DATA: audit_logs
-- =========================================================================
INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, created_at) VALUES
(1, 'CREATE', 'user', 4, NULL, '{"username":"cashier_alice","role":"cashier"}', '192.168.1.100', '2026-01-10 09:00:00'),
(1, 'CREATE', 'product', 1, NULL, '{"product_name":"Dell XPS 13 Laptop","sku":"LAPTOP-DELL-001"}', '192.168.1.100', '2026-01-11 10:00:00'),
(2, 'UPDATE', 'product', 1, '{"quantity_on_hand":20}', '{"quantity_on_hand":15}', '192.168.1.101', '2026-01-15 09:30:00'),
(4, 'CREATE', 'sale', 1, NULL, '{"sale_number":"SALE-20260101-001","total_amount":1403.99}', '192.168.1.102', '2026-01-15 09:30:00'),
(7, 'CREATE', 'purchase_order', 1, NULL, '{"po_number":"PO-20260101","status":"placed"}', '192.168.1.103', '2026-01-10 10:00:00'),
(8, 'UPDATE', 'inventory_transaction', 1, '{"quantity_change":10}', '{"quantity_change":15}', '192.168.1.104', '2026-01-15 14:00:00'),
(1, 'DELETE', 'discount', 1, '{"discount_id":1,"discount_name":"Electronics Summer Sale"}', NULL, '192.168.1.100', '2026-01-20 11:00:00');

-- =========================================================================
-- SEED DATA: reports
-- =========================================================================
INSERT INTO reports (report_name, report_type, report_date, generated_by, data_summary) VALUES
('Daily Sales Report', 'sales', '2026-01-15', 2, '{"total_sales":3933.93,"transaction_count":3,"top_product":"iPhone 15 Pro"}'),
('Weekly Sales Report', 'sales', '2026-01-17', 2, '{"total_sales":9065.83,"transaction_count":10,"revenue_per_transaction":906.58}'),
('Inventory Status Report', 'inventory', '2026-01-17', 8, '{"total_items":2156,"low_stock_items":5,"out_of_stock_items":0}'),
('Profitability Report', 'profitability', '2026-01-17', 2, '{"gross_profit":4532.92,"profit_margin":"50%","top_category":"Electronics"}'),
('Customer Analytics', 'customer', '2026-01-17', 2, '{"total_customers":10,"vip_customers":3,"wholesale_customers":2}');

-- =========================================================================
-- END OF SEED DATA
-- =========================================================================
