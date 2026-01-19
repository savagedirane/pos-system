# User Registration Guide

## Overview
The POS System now includes a secure admin-only user registration form that allows administrators to create new user accounts for the system.

## Features
- **Admin-Only Access**: Only users with the "Admin" role can access the registration page
- **Secure Password Handling**: Passwords are validated for strength and hashed using bcrypt
- **Comprehensive Validation**: Both client-side and server-side validation
- **Real-time Feedback**: Form provides immediate feedback on password strength
- **Role-Based User Creation**: Assign specific roles during registration

## How to Register a New User

### Step 1: Login as Admin
Log in to the POS System using an admin account:
- **Username**: `admin_user`
- **Password**: `Test@123`

### Step 2: Access Registration Page
Navigate to the user registration page using one of these methods:

**Method A - Via Dashboard Navigation**
1. Click "Register User" in the top navigation bar (only visible to admins)

**Method B - Via User Management**
1. Go to "Users" in the navigation
2. Click the "Add User" button

**Method C - Direct URL**
Navigate directly to: `/pos-system/public/register.php`

### Step 3: Fill in User Details

#### Account Information Section
- **Username**: Unique identifier (3-20 characters, alphanumeric with hyphens/underscores)
- **Email**: Valid email address (must be unique)
- **Password**: Strong password (minimum 8 characters)
  - Must contain uppercase letters
  - Must contain lowercase letters
  - Must contain at least one number
- **Confirm Password**: Re-enter password to verify

#### Personal Information Section
- **First Name**: User's first name
- **Last Name**: User's last name
- **Phone Number**: Optional contact number

#### Role Assignment Section
- **Role**: Select one of the following:
  - **Admin**: Full system access, user management, reports
  - **Manager**: User management, view reports, dashboards
  - **Cashier**: Process sales and payments
  - **Inventory Staff**: Manage products and inventory

### Step 4: Submit Form
Click the "Create User" button to create the user account.

The form will:
1. Validate all required fields
2. Check password strength
3. Verify the password matches the confirmation
4. Verify username uniqueness
5. Verify email uniqueness
6. Create the user account in the database
7. Log the registration action for audit purposes

### Step 5: Confirmation
On successful registration:
- A success message will display
- The system will redirect to the Users Management page
- The new user will appear in the user list

## Password Requirements
All passwords must meet the following criteria:
- ✓ Minimum 8 characters
- ✓ At least one uppercase letter (A-Z)
- ✓ At least one lowercase letter (a-z)
- ✓ At least one number (0-9)

**Examples of valid passwords:**
- `Secure@Pass123`
- `MyPassword2024`
- `Demo123Test`

**Examples of invalid passwords:**
- `password` (no uppercase or numbers)
- `PASSWORD123` (no lowercase)
- `Pass123` (only 7 characters)
- `Passw0rd` (missing uppercase or number)

## Default Test Users

The system comes with several test users. You can use these for testing:

| Username | Password | Role | Email |
|----------|----------|------|-------|
| admin_user | Test@123 | Admin | admin@pos-system.local |
| manager_john | Test@123 | Manager | john.manager@pos-system.local |
| manager_sarah | Test@123 | Manager | sarah.manager@pos-system.local |
| cashier_alice | Test@123 | Cashier | alice.cashier@pos-system.local |
| cashier_bob | Test@123 | Cashier | bob.cashier@pos-system.local |
| cashier_emily | Test@123 | Cashier | emily.cashier@pos-system.local |
| inventory_mike | Test@123 | Inventory Staff | mike.inventory@pos-system.local |
| inventory_lisa | Test@123 | Inventory Staff | lisa.inventory@pos-system.local |

## API Endpoint

The registration form submits to the centralized authentication handler via AJAX:

**Endpoint**: `POST /public/auth.php?action=register`

**Request Parameters**:
```json
{
  "action": "register",
  "username": "new_user",
  "email": "user@example.com",
  "password": "SecurePass123",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "555-1234",
  "role": "cashier"
}
```

**Response - Success**:
```json
{
  "status": "success",
  "code": 201,
  "message": "User 'new_user' created successfully",
  "user_id": 9
}
```

**Response - Error**:
```json
{
  "status": "error",
  "code": 400,
  "message": "Username already exists"
}
```

## Possible Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "Please fill in all required fields" | Missing required fields | Ensure all fields are completed |
| "Username must be 3-20 characters..." | Invalid username format | Use 3-20 alphanumeric characters, hyphens, or underscores |
| "Password must be at least 8 characters..." | Weak password | Follow password requirements |
| "Passwords do not match" | Password mismatch | Ensure password and confirm password match |
| "Invalid email format" | Malformed email | Enter a valid email address |
| "Username already exists" | Duplicate username | Choose a different username |
| "Email already exists" | Duplicate email | Choose a different email address |
| "Invalid role selected" | Unknown role | Select a valid role from the dropdown |
| "You must be logged in..." | Not authenticated | Log in first before registering users |
| "Only administrators can register..." | Not an admin | Contact your administrator |

## Security Notes

1. **Passwords are hashed**: All passwords are hashed using bcrypt (cost 12) before storage
2. **Session Required**: User must be logged in and have admin role to access registration
3. **Audit Logging**: All user registrations are logged with timestamp and admin ID
4. **Input Validation**: All inputs are validated server-side (not just client-side)
5. **Unique Constraints**: Usernames and emails are enforced as unique in the database
6. **Protected Fields**: Created timestamps and creator IDs are automatically set

## Troubleshooting

**Q: I'm getting a "403 Forbidden" error**
A: You must be logged in as an admin to access the registration page. Log in with an admin account.

**Q: The registration form is not showing up**
A: Check that you're logged in as an admin. The form is only visible to admin users.

**Q: I registered a user but can't log in**
A: Verify you used the exact password you set during registration. Passwords are case-sensitive.

**Q: The form keeps rejecting my password**
A: Ensure your password meets all requirements (8+ chars, uppercase, lowercase, number).

**Q: I'm getting database errors**
A: Ensure the database is running and accessible. Check your database configuration in `config/database.php`.

## Next Steps

After registering users, you can:
1. Manage user access via the Users Management page
2. View user activity logs
3. Deactivate or delete users if needed
4. Assign additional permissions based on roles

For more information, see the [User Management Guide](USER_MANAGEMENT.md).
