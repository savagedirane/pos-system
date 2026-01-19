<?php
/**
 * File: register.php
 * Description: Admin-only user registration form
 * Author: POS Development Team
 * Created: 2026-01-17
 * Version: 1.0
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php');
    exit;
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ./dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #1e40af;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f3f4f6;
            --dark: #1f2937;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
        }
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }
        
        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .row-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .row-columns .form-group {
            margin-bottom: 0;
        }

        .password-requirements {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 12px;
            border-radius: 4px;
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }

        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .button-group .btn-register,
        .button-group .btn-cancel {
            flex: 1;
            margin-top: 0;
        }

        #alertContainer {
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 8px;
            border: none;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }

        .spinner-border {
            width: 20px;
            height: 20px;
            display: none;
            margin-right: 8px;
        }

        .required {
            color: #dc3545;
            margin-left: 3px;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .form-section:last-of-type {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #764ba2;
        }
        @media (max-width: 768px) {
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Include Sidebar Component -->
    <?php require_once '../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <div class="register-container">
            <div class="register-header">
                <h1><i class="fas fa-user-plus"></i> Register New User</h1>
                <p>Create a new user account for the POS system</p>
            </div>
            <div id="alertContainer"></div>

            <form id="registerForm\">
            <!-- Account Information Section -->
            <div class="form-section">
                <div class="section-title">Account Information</div>
                
                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Enter username" required>
                    <small class="text-muted">Must be unique and 3-20 characters</small>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter email address" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter password" required>
                    <div class="password-requirements">
                        <strong>Password must contain:</strong>
                        <ul class="mb-0" style="margin-top: 5px;">
                            <li>At least 8 characters</li>
                            <li>Uppercase and lowercase letters</li>
                            <li>At least one number</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" 
                           placeholder="Confirm password" required>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="section-title">Personal Information</div>
                
                <div class="row-columns">
                    <div class="form-group">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="first_name" 
                               placeholder="First name" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="last_name" 
                               placeholder="Last name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="text-muted">(Optional)</span></label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           placeholder="Enter phone number">
                </div>
            </div>

            <!-- Role Assignment Section -->
            <div class="form-section">
                <div class="section-title">Role Assignment</div>
                
                <div class="form-group">
                    <label for="role">User Role <span class="required">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select a role...</option>
                        <option value="admin">Admin - Full system access</option>
                        <option value="manager">Manager - Manage users and reports</option>
                        <option value="cashier">Cashier - Process sales and payments</option>
                        <option value="inventory_staff">Inventory Staff - Manage inventory</option>
                    </select>
                    <small class="text-muted d-block mt-2">
                        <strong>Role Permissions:</strong>
                        <ul class="mb-0">
                            <li><strong>Admin:</strong> Full system access, user management, reports</li>
                            <li><strong>Manager:</strong> User management, view reports, dashboards</li>
                            <li><strong>Cashier:</strong> Process sales, manage transactions</li>
                            <li><strong>Inventory Staff:</strong> Manage products and inventory</li>
                        </ul>
                    </small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <button type="submit" class="btn btn-register" id="registerBtn">
                    <span id="spinner" class="spinner-border"></span>
                    <span id="btnText">Create User</span>
                </button>
                <a href="./users.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>

            <a href="./dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');
        const alertContainer = document.getElementById('alertContainer');

        // Display alert message
        function showAlert(message, type = 'danger') {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
            const alertHTML = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle')}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            alertContainer.innerHTML = alertHTML;
        }

        // Validate password strength
        function validatePassword(password) {
            const minLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            return minLength && hasUppercase && hasLowercase && hasNumber;
        }

        // Validate username
        function validateUsername(username) {
            return username.length >= 3 && username.length <= 20 && /^[a-zA-Z0-9_-]+$/.test(username);
        }

        // Handle form submission
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Get form values
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const role = document.getElementById('role').value;

            // Validation
            if (!username || !email || !password || !confirmPassword || !firstName || !lastName || !role) {
                showAlert('Please fill in all required fields');
                return;
            }

            if (!validateUsername(username)) {
                showAlert('Username must be 3-20 characters and contain only letters, numbers, hyphens, and underscores');
                return;
            }

            if (!validatePassword(password)) {
                showAlert('Password must be at least 8 characters and contain uppercase, lowercase, and numbers');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Passwords do not match');
                return;
            }

            // Show loading state
            registerBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Creating User...';

            try {
                // Send registration request to auth.php
                const response = await fetch('./auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'register',
                        username: username,
                        email: email,
                        password: password,
                        first_name: firstName,
                        last_name: lastName,
                        phone: phone,
                        role: role
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showAlert(`User "${username}" created successfully! Redirecting...`, 'success');
                    setTimeout(() => {
                        window.location.href = './users.php';
                    }, 1500);
                } else {
                    showAlert(data.message || 'Registration failed. Please try again.');
                    registerBtn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.textContent = 'Create User';
                }
            } catch (error) {
                console.error('Registration error:', error);
                showAlert('An error occurred during registration. Please try again.');
                registerBtn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = 'Create User';
            }
        });

        // Clear alerts on input
        const inputFields = ['username', 'email', 'password', 'confirmPassword', 'firstName', 'lastName', 'phone', 'role'];
        inputFields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.addEventListener('focus', function() {
                    alertContainer.innerHTML = '';
                });
            }
        });

        // Real-time password validation feedback
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const isValid = validatePassword(password);
            
            if (password.length > 0) {
                if (isValid) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            }
        });

        // Match password validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            }
        });
    </script>
</body>
</html>
