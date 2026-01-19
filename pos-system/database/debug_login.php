<?php
/**
 * Login Debugging Script
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../config/settings.php';
require_once '../app/models/User.php';
require_once '../utils/helpers/SecurityHelper.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

echo "<h2>User Database Status</h2>";

// Check if users table exists
$result = $mysqli->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'><strong>ERROR: Users table does not exist!</strong></p>";
    exit;
}

echo "<p style='color: green;'><strong>✓ Users table exists</strong></p>";

// Count users
$result = $mysqli->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
$user_count = $row['count'];
echo "<p><strong>Total users in database:</strong> $user_count</p>";

if ($user_count === 0) {
    echo "<p style='color: red;'><strong>ERROR: No users in database! Run /database/setup_database.php</strong></p>";
    exit;
}

// List all users
echo "<h3>All Users:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Hash</th></tr>";

$result = $mysqli->query("SELECT user_id, username, first_name, last_name, email, role, is_active, password_hash FROM users");
while ($row = $result->fetch_assoc()) {
    $hash_preview = substr($row['password_hash'], 0, 30) . '...';
    $active = $row['is_active'] ? 'Yes' : 'No';
    echo "<tr>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['username']}</td>";
    echo "<td>{$row['first_name']} {$row['last_name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['role']}</td>";
    echo "<td>{$active}</td>";
    echo "<td><code style='font-size: 10px;'>{$hash_preview}</code></td>";
    echo "</tr>";
}
echo "</table>";

// Test login with known user
echo "<h3>Test Login</h3>";
$test_username = 'admin_user';
$test_password = 'Test@123';

echo "<p>Testing login with: <code>$test_username / $test_password</code></p>";

$userModel = new User($mysqli);
$user = $userModel->getByUsername($test_username);

if (!$user) {
    echo "<p style='color: red;'><strong>ERROR:</strong> User '$test_username' not found in database</p>";
    echo "<p>Available usernames:</p>";
    $result = $mysqli->query("SELECT username FROM users");
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['username']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green;'><strong>✓ User found:</strong> {$user['first_name']} {$user['last_name']}</p>";
    
    // Test password verification
    $password_match = password_verify($test_password, $user['password_hash']);
    if ($password_match) {
        echo "<p style='color: green;'><strong>✓ Password matches!</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR: Password does NOT match</strong></p>";
        echo "<p>Hash: {$user['password_hash']}</p>";
        
        // Try to hash the test password and see
        $test_hash = password_hash($test_password, PASSWORD_BCRYPT, ['cost' => 12]);
        echo "<p>Test hash of '$test_password': $test_hash</p>";
    }
}

$mysqli->close();
?>
