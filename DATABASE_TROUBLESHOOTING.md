# 🔧 Database Connection - Troubleshooting Guide

## Quick Fix Steps

### Step 1: Create Database (If Not Created)
**Run this script to automatically create and import everything:**

```
http://localhost/pos-system/database/setup_database.php
```

This will:
- ✓ Connect to MySQL
- ✓ Create `pos_system` database
- ✓ Import 20 tables
- ✓ Load sample data

### Step 2: Verify Data
**Check that users and data exist:**

```
http://localhost/pos-system/database/verify_data.php
```

This will show:
- ✓ Connected users
- ✓ Record counts
- ✓ Database statistics

### Step 3: Test Connection
**Run the full connection test:**

```
http://localhost/pos-system/database/test_connection.php
```

Should show:
- ✓ Database connection: PASSED
- ✓ Schema verification: PASSED
- ✓ Sample data: LOADED
- ✓ Prepared statements: WORKING

---

## Common Issues & Solutions

### ❌ Issue: "Connection failed: Unknown database 'pos_system'"
**Solution:**
1. Run: http://localhost/pos-system/database/setup_database.php
2. Verify with: http://localhost/pos-system/database/verify_data.php
3. Test with: http://localhost/pos-system/database/test_connection.php

---

### ❌ Issue: "Access denied for user 'root'@'localhost'"
**Solution:**
1. Verify MySQL password in `/config/database.php` (should be blank for default XAMPP)
2. Check MySQL is running
3. In phpMyAdmin, create user with no password if needed

**In `/config/database.php`, check:**
```php
private $username = 'root';
private $password = '';  // Should be blank
```

---

### ❌ Issue: "No tables found"
**Solution:**
1. Run: http://localhost/pos-system/database/setup_database.php
2. Wait for "Database setup complete" message
3. Check phpMyAdmin: http://localhost/phpmyadmin/
4. Look for `pos_system` database with 20 tables

---

### ❌ Issue: "MySQL not running"
**Solution:**
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for green indicator
4. Try connection again

---

## ✅ Verification Checklist

- [ ] MySQL is running (check XAMPP Control Panel)
- [ ] `pos_system` database exists (check phpMyAdmin)
- [ ] 20 tables created (check phpMyAdmin > pos_system)
- [ ] Users table has data (check phpMyAdmin > users table)
- [ ] test_connection.php shows ✓ all tests pass
- [ ] Can access login page: http://localhost/pos-system/public/login.php

---

## 🚀 Once Database is Ready

### Test Login Page
```
http://localhost/pos-system/public/login.php
```

### Check Database Content
```
http://localhost/phpmyadmin/
```

### Review Users Created
```
http://localhost/pos-system/database/verify_data.php
```

---

## 📋 Test Credentials

If database setup completed successfully, you'll have:

**Admin User**
- Username: admin_user
- Email: admin@pos-system.local

**Manager**
- Username: manager_john
- Email: john.manager@pos-system.local

**Cashier**
- Username: cashier_alice
- Email: alice.cashier@pos-system.local

(Passwords are hashed with bcrypt in the database)

---

## 🔍 Manual Database Check

### Via phpMyAdmin:
1. Go to: http://localhost/phpmyadmin/
2. Look for database: `pos_system`
3. Check tables (should have 20):
   - users
   - categories
   - products
   - customers
   - sales
   - And 15 more...

### Via Terminal (if available):
```bash
mysql -u root -p
```

```sql
USE pos_system;
SHOW TABLES;
SELECT COUNT(*) FROM users;
```

---

## 🛠️ Advanced Troubleshooting

### Check MySQL Error Log
```
C:\XAMPP\mysql\data\mysql.log
```

### Reset Database
If database is corrupted, reset it:

1. Delete database:
   ```
   http://localhost/phpmyadmin/
   → Select pos_system
   → Click Operations
   → Click "Drop"
   ```

2. Recreate:
   ```
   http://localhost/pos-system/database/setup_database.php
   ```

### Rebuild Schema Only
If you want schema without sample data:
```
Open phpMyAdmin
Select pos_system
Go to Import
Choose database_schema.sql
Click Go
```

---

## 📞 Still Having Issues?

### Check These Files:
1. `/config/database.php` - Database credentials
2. `/database/database_schema.sql` - Schema file
3. `/database/seed_data.sql` - Sample data
4. `/logs/application.log` - Error logs

### Run These Tests (In Order):
1. http://localhost/pos-system/database/setup_database.php
2. http://localhost/pos-system/database/verify_data.php
3. http://localhost/pos-system/database/test_connection.php

### Check MySQL Status:
1. XAMPP Control Panel
2. Verify MySQL shows green indicator
3. Click "Admin" next to MySQL to open phpMyAdmin

---

## ✨ Everything Should Work Now!

Once all checks pass, you can:
- ✅ Access login page
- ✅ Query the database
- ✅ Build the application
- ✅ Start Sprint 1 development

**If still having issues, provide the exact error message from:**
- http://localhost/pos-system/database/test_connection.php

---

Generated: January 17, 2026
Status: Database Setup Guide

