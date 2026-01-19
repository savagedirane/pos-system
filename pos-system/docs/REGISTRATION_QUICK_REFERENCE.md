# Quick Reference - User Registration

## Access the Registration Form

### Method 1: Via Navigation (Easiest)
1. Login as admin
2. Click **"Register User"** in the top navigation bar
3. Fill form and click **"Create User"**

### Method 2: Via User Management
1. Login as admin
2. Click **"Users"** in the navigation
3. Click **"Add User"** button
4. Fill form and click **"Create User"**

### Method 3: Direct URL
Visit: `http://localhost/pos-system/public/register.php`

## Quick Registration Steps

| Step | Action |
|------|--------|
| 1 | Fill in **Username** (3-20 chars, letters/numbers/- only) |
| 2 | Fill in **Email** (must be valid email address) |
| 3 | Enter **Password** (8+ chars, uppercase, lowercase, number) |
| 4 | Confirm **Password** (must match exactly) |
| 5 | Enter **First Name** |
| 6 | Enter **Last Name** |
| 7 | Optionally enter **Phone Number** |
| 8 | Select **Role** from dropdown |
| 9 | Click **"Create User"** |
| 10 | See success message and redirect to Users page |

## Login to Test

**Admin Account** (can register users):
- Username: `admin_user`
- Password: `Test@123`

**Other Accounts** (cannot register users):
- `manager_john`, `cashier_alice`, `inventory_mike`, etc.
- All use password: `Test@123`

## Valid Roles

| Role | Can Register Users | Permissions |
|------|------------------|-------------|
| Admin | ✅ Yes | Full system access |
| Manager | ❌ No | User and report management |
| Cashier | ❌ No | Sales and transactions |
| Inventory Staff | ❌ No | Product and inventory management |

## Password Requirements Checklist

✅ At least 8 characters  
✅ At least one UPPERCASE letter  
✅ At least one lowercase letter  
✅ At least one number  

**Good**: `MyPassword123`, `SecurePass2024`, `Admin@POS1`  
**Bad**: `password`, `PASSWORD123`, `Pass123` (only 7 chars)

## Possible Error Messages

| Error | Fix |
|-------|-----|
| "Please fill in all required fields" | Complete all fields |
| "Username must be 3-20 characters..." | Use valid username format |
| "Password must be at least 8 characters..." | Use stronger password |
| "Passwords do not match" | Make sure password fields match |
| "Invalid email format" | Use valid email address |
| "Username already exists" | Choose different username |
| "Email already exists" | Choose different email |
| "Invalid role selected" | Pick role from dropdown |
| "Only administrators can register..." | Must be logged in as admin |

## Form Structure

```
ACCOUNT INFORMATION SECTION
├── Username
├── Email
├── Password
└── Confirm Password

PERSONAL INFORMATION SECTION
├── First Name
├── Last Name
└── Phone Number (Optional)

ROLE ASSIGNMENT SECTION
└── Role (Dropdown)

ACTION BUTTONS
├── Create User
└── Cancel
```

## After Registration

✅ New user account created  
✅ User appears in user list  
✅ Can login immediately with credentials  
✅ Action logged for audit trail  

## Troubleshooting

**Q: Can't see register page?**  
A: Make sure you're logged in as admin

**Q: Getting access denied?**  
A: Only admins can register users

**Q: Form keeps rejecting password?**  
A: Password needs 8+ chars, uppercase, lowercase, and number

**Q: Username already taken?**  
A: Choose a different username

## Support

For detailed help, see:
- `docs/USER_REGISTRATION_GUIDE.md` - Complete guide
- `docs/REGISTRATION_IMPLEMENTATION.md` - Technical details
