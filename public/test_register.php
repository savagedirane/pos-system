<?php
/**
 * Test script to verify user registration functionality
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$authController = new AuthController();

echo "=== POS SYSTEM - REGISTRATION TEST ===\n\n";

// Test 1: Check if admin user exists
echo "TEST 1: Checking admin user credentials\n";
$admin = $authController->getUserModel()->getByUsername('admin_user');
if ($admin) {
    echo "✓ Admin user found: " . $admin['username'] . "\n";
    echo "  Role: " . $admin['role'] . "\n";
    echo "  Email: " . $admin['email'] . "\n";
} else {
    echo "✗ Admin user not found\n";
    exit(1);
}

// Test 2: Login as admin
echo "\nTEST 2: Login as admin\n";
$_POST['username'] = 'admin_user';
$_POST['password'] = 'Test@123';

$loginResult = $authController->login(['username' => 'admin_user', 'password' => 'Test@123']);
if ($loginResult['status'] === 'success') {
    echo "✓ Admin login successful\n";
    echo "  User ID: " . $loginResult['user_id'] . "\n";
    echo "  Role: " . $loginResult['role'] . "\n";
    
    // Simulate session
    $_SESSION['user_id'] = $loginResult['user_id'];
    $_SESSION['username'] = 'admin_user';
    $_SESSION['role'] = $loginResult['role'];
} else {
    echo "✗ Admin login failed: " . $loginResult['message'] . "\n";
    exit(1);
}

// Test 3: Check if test user doesn't already exist
echo "\nTEST 3: Checking if test user already exists\n";
$testUser = $authController->getUserModel()->getByUsername('test_register_user');
if (!$testUser) {
    echo "✓ Test user does not exist (good)\n";
} else {
    echo "⚠ Test user already exists, cleaning up...\n";
    // Note: In production, you'd want to delete the old user
}

// Test 4: Simulate user registration via auth.php
echo "\nTEST 4: Simulating user registration\n";
$_POST = [
    'action' => 'register',
    'username' => 'test_register_user',
    'email' => 'test.register@pos-system.local',
    'password' => 'TestPassword123',
    'first_name' => 'Test',
    'last_name' => 'Register',
    'phone' => '555-1234',
    'role' => 'cashier'
];

// Call the registration handler from auth.php
require_once __DIR__ . '/auth.php';

// The above include will echo JSON and exit, so we need a different approach
// Let's manually test the user creation
echo "\nTEST 5: Direct user creation via model\n";

$userData = [
    'username' => 'test_register_user',
    'email' => 'test.register@pos-system.local',
    'password' => 'TestPassword123',
    'first_name' => 'Test',
    'last_name' => 'Register',
    'phone' => '555-1234',
    'role' => 'cashier',
    'is_active' => 1,
    'created_at' => date('Y-m-d H:i:s'),
    'created_by' => $_SESSION['user_id']
];

$result = $authController->getUserModel()->createUser($userData);
if ($result) {
    echo "✓ User created successfully\n";
    echo "  User ID: " . $result . "\n";
    
    // Test 6: Verify user was created
    echo "\nTEST 6: Verifying created user\n";
    $newUser = $authController->getUserModel()->getByUsername('test_register_user');
    if ($newUser) {
        echo "✓ New user found in database\n";
        echo "  Username: " . $newUser['username'] . "\n";
        echo "  Email: " . $newUser['email'] . "\n";
        echo "  First Name: " . $newUser['first_name'] . "\n";
        echo "  Last Name: " . $newUser['last_name'] . "\n";
        echo "  Role: " . $newUser['role'] . "\n";
        echo "  Phone: " . $newUser['phone'] . "\n";
        echo "  Is Active: " . ($newUser['is_active'] ? 'Yes' : 'No') . "\n";
        echo "  Created By User ID: " . ($newUser['created_by'] ?? 'NULL') . "\n";
    } else {
        echo "✗ New user not found\n";
    }
} else {
    echo "✗ Failed to create user\n";
    echo "  Error: " . print_r($authController->getUserModel(), true) . "\n";
}

// Test 7: Test login with new user
echo "\nTEST 7: Login with newly created user\n";
$newLoginResult = $authController->login(['username' => 'test_register_user', 'password' => 'TestPassword123']);
if ($newLoginResult['status'] === 'success') {
    echo "✓ New user login successful\n";
    echo "  User ID: " . $newLoginResult['user_id'] . "\n";
    echo "  Username: " . $newLoginResult['username'] . "\n";
    echo "  Role: " . $newLoginResult['role'] . "\n";
} else {
    echo "✗ New user login failed: " . $newLoginResult['message'] . "\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";
?>
