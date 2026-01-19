# Sprint 1 Development - Quick Reference Guide

**Date**: January 17, 2026  
**Sprint Duration**: 2 weeks  
**Current Status**: ✅ Foundation Complete - Development Phase Active

---

## 🚀 What's Available Right Now

### Core Management Interfaces (All Working!)

1. **User Management** → http://localhost/pos-system/public/users.php
   - View all users with roles
   - Create new users (admin only)
   - Toggle user status
   - Ready for: Edit user, Delete user, Password reset

2. **Product Management** → http://localhost/pos-system/public/products.php
   - View full product catalog
   - Create new products
   - See pricing and stock levels
   - Low stock indicators
   - Ready for: Edit product, Delete product, Bulk import

3. **Customer Management** → http://localhost/pos-system/public/customers.php
   - View all customers
   - Create new customers
   - See contact info and purchase history
   - Ready for: Edit customer, Delete customer, Segments

4. **Sales Transactions** → http://localhost/pos-system/public/sales.php
   - Create sales with real-time calculation
   - Automatic stock deduction
   - Customer assignment (optional)
   - Discount application
   - Recent sales view
   - Ready for: Receipt printing, Email receipt, Sale modifications

5. **Dashboard** → http://localhost/pos-system/public/dashboard.php
   - Real-time statistics
   - Quick navigation links
   - Recent transactions

---

## 🔑 Test Credentials

```
Username: admin_user      | Password: Test@123 | Role: Admin
Username: manager_user    | Password: Test@123 | Role: Manager
Username: cashier_user    | Password: Test@123 | Role: Cashier
Username: inventory_user  | Password: Test@123 | Role: Inventory Staff
```

---

## 📁 Where to Add Code

### For New CRUD Pages:
```php
// Template structure
<?php
session_start();
require_once '../config/database.php';
require_once '../app/controllers/AuthController.php';

// Check auth and role
$auth = new AuthController();
$user = $auth->getCurrentUser();

if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Your code here
?>
```

### For Database Operations:
```php
// Always use prepared statements
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$stmt = $mysqli->prepare("SELECT * FROM table WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->get_result();
```

### For Forms:
```php
// Handle POST with action field
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Create logic
    } elseif ($action === 'edit') {
        // Update logic
    } elseif ($action === 'delete') {
        // Delete logic
    }
}
```

---

## ✅ Week 1 Tasks (Jan 17-24)

### HIGH PRIORITY - Must Complete

- [ ] **Edit User** - Add edit form and update logic to `users.php`
- [ ] **Delete User** - Add delete confirmation and deletion logic
- [ ] **Edit Product** - Add edit form and update logic to `products.php`
- [ ] **Delete Product** - Add delete with inventory check
- [ ] **Edit Customer** - Add edit form to `customers.php`
- [ ] **Delete Customer** - Add delete functionality
- [ ] **Search Users** - Add search by name/username to users.php
- [ ] **Search Products** - Add search by name/SKU to products.php
- [ ] **Search Customers** - Add search by name/email to customers.php
- [ ] **Form Validation** - Enhance client + server validation

### MEDIUM PRIORITY - Nice to Have

- [ ] Product category filter
- [ ] Sales history search
- [ ] Logout confirmation dialog
- [ ] Dashboard charts
- [ ] CSV export

---

## 🔐 Security Checklist

Before submitting any code:

✅ Using prepared statements?
```php
$stmt = $mysqli->prepare("...");
$stmt->execute([$var]);  // NOT mysqli->query("... $var ...")
```

✅ Validating input?
```php
$name = trim($_POST['name'] ?? '');
if (empty($name)) { $error = 'Name required'; }
```

✅ Escaping output?
```php
<?php echo htmlspecialchars($user_input); ?>
```

✅ Checking authentication?
```php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```

✅ Checking roles?
```php
if (!in_array($user['role'], ['admin', 'manager'])) {
    header('Location: dashboard.php');
    exit;
}
```

---

## 🎨 UI Component Library

### Bootstrap Classes Used:
- Buttons: `btn btn-primary`, `btn btn-danger`, `btn btn-outline-*`
- Colors: `btn-primary` (blue), `btn-success` (green), `btn-danger` (red)
- Utilities: `w-100` (full width), `mt-4` (margin top), `mb-3` (margin bottom)
- Modals: `data-bs-toggle="modal"` on button, `class="modal fade"` on div
- Tables: `table table-hover`, `table-responsive` for mobile

### Custom Styles:
```css
.btn-add { background: #3b82f6; }
.status-active { color: #10b981; }
.status-inactive { color: #ef4444; }
.role-admin { background: #dbeafe; }
```

### Common Patterns:
```html
<!-- Message Alert -->
<div class="alert alert-success alert-dismissible fade show">
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <!-- Form fields -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Header</th></tr></thead>
        <tbody><tr><td>Data</td></tr></tbody>
    </table>
</div>
```

---

## 🐛 Common Debugging

### Issue: "Table doesn't exist"
**Solution**: Run http://localhost/pos-system/database/setup_database.php

### Issue: "Access Denied"
**Solution**: Check role permissions in user role check

### Issue: "SQLSTATE[23000]: Integrity constraint violation"
**Solution**: Foreign key violation - check referenced data exists

### Issue: "Headers already sent"
**Solution**: No output before `header()` calls - check for extra spaces

### Issue: Session not persisting
**Solution**: Ensure `session_start()` is first line in PHP

---

## 📊 Database Reference

### Key Tables:
```sql
users (user_id, username, email, password_hash, first_name, last_name, role, is_active)
products (product_id, product_name, sku, category_id, cost_price, selling_price, quantity_on_hand)
customers (customer_id, customer_name, email, phone, address, total_purchases)
sales (sale_id, customer_id, user_id, total_amount, discount_amount, number_of_items)
categories (category_id, category_name, is_active)
```

### User Roles:
- `admin` - Full system access
- `manager` - Can manage users, products, view reports
- `cashier` - Can create sales, view products
- `inventory_staff` - Can manage inventory and stock

---

## 🚀 Development Workflow

1. **Pick a task** from Week 1 list
2. **Create/edit PHP file** in `public/` folder
3. **Test locally** at `http://localhost/pos-system/public/yourfile.php`
4. **Check security**:
   - Prepared statements?
   - Input validation?
   - Role checks?
5. **Test with different roles** (use test credentials)
6. **Check mobile view** (responsive design)
7. **Mark as complete** when working

---

## 📝 File Locations Quick Links

- **Login**: `/public/login.php`
- **Dashboard**: `/public/dashboard.php`
- **Users**: `/public/users.php` ← Start editing here for user edit/delete
- **Products**: `/public/products.php` ← Add product edit/delete
- **Customers**: `/public/customers.php` ← Add customer edit/delete
- **Sales**: `/public/sales.php` ← Add sales modifications
- **Database**: `/database/setup_database.php` (if needed)
- **Config**: `/config/database.php` (DB connection)
- **Security**: `/utils/helpers/SecurityHelper.php` (validation functions)

---

## 💪 You've Got This!

Everything is set up and working. Your job is to:

1. ✅ Keep using prepared statements
2. ✅ Always validate input
3. ✅ Check user roles before sensitive operations
4. ✅ Show helpful error messages
5. ✅ Test thoroughly

**Need help?** Check the comments in the existing code files - they follow best practices!

---

**Happy Coding! 🎉**

Sprint 1 Foundation is solid. Let's build amazing features!

---

*POS System | Sprint 1 Active | Development in Progress*
