<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 50px 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-forgot a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .remember-forgot a:hover {
            color: #764ba2;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .demo-credentials {
            background-color: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 13px;
        }

        .demo-credentials h6 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .demo-credentials p {
            color: #666;
            margin: 5px 0;
        }

        .demo-credentials code {
            background-color: white;
            padding: 2px 6px;
            border-radius: 3px;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }

        .loading-spinner {
            display: none;
            margin-right: 8px;
        }

        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .demo-credentials {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🏪 POS System</h1>
            <p>Welcome Back! Please login to your account</p>
        </div>

        <!-- Error Messages -->
        <?php
        if (isset($_GET['timeout'])) {
            echo '<div class="alert alert-warning" role="alert">Your session has expired. Please login again.</div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-info" role="alert">' . htmlspecialchars($_GET['success']) . '</div>';
        }
        ?>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Login Form -->
        <form id="loginForm">
            <div class="form-group">
                <label for="username" class="form-label">Username or Email</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username or email"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="remember-forgot">
                <div class="checkbox-wrapper">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="remember" 
                        name="remember"
                    >
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#forgot">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="loading-spinner" id="spinner">
                    <div class="loader"></div>
                </span>
                <span id="btnText">Sign In</span>
            </button>
        </form>

        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <h6>📋 Demo Credentials</h6>
            <p><strong>Admin:</strong> <code>admin_user</code> / <code>password</code></p>
            <p><strong>Manager:</strong> <code>manager_john</code> / <code>password</code></p>
            <p><strong>Cashier:</strong> <code>cashier_alice</code> / <code>password</code></p>
            <p style="margin-top: 10px; font-size: 12px; color: #999;">
                ℹ️ For development purposes only
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');
        const alertContainer = document.getElementById('alertContainer');

        // Display alert message
        function showAlert(message, type = 'danger') {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
            const alertHTML = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            alertContainer.innerHTML = alertHTML;
        }

        // Handle form submission
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate inputs
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                showAlert('Please fill in all fields');
                return;
            }

            // Show loading state
            loginBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Signing In...';

            try {
                // Send login request to auth.php
                const response = await fetch('./auth.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'login',
                        username: username,
                        password: password
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showAlert('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = './dashboard.php';
                    }, 1000);
                } else {
                    showAlert(data.message || 'Login failed. Please try again.');
                    loginBtn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.textContent = 'Sign In';
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('An error occurred during login. Please try again.');
                loginBtn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = 'Sign In';
            }
        });

        // Clear alerts on input
        document.getElementById('username').addEventListener('focus', function() {
            alertContainer.innerHTML = '';
        });

        document.getElementById('password').addEventListener('focus', function() {
            alertContainer.innerHTML = '';
        });
    </script>
</body>
</html>
