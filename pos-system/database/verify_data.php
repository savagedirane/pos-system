<?php
/**
 * Quick Verification Script
 * Checks database connectivity and user data
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "═══════════════════════════════════════════════════════════════\n";
echo "        POS SYSTEM - QUICK VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Connect to database
$connection = new mysqli('localhost', 'root', '', 'pos_system');

if ($connection->connect_error) {
    echo "✗ Database Connection Failed\n";
    echo "  Error: " . $connection->connect_error . "\n";
    exit(1);
}

echo "✓ Database connected successfully\n\n";

// Check users table
echo "Users in Database:\n";
$result = $connection->query("SELECT user_id, username, email, role FROM users ORDER BY user_id");

if ($result && $result->num_rows > 0) {
    echo "┌─────┬──────────────────┬─────────────────────────────────────┬──────────────────┐\n";
    echo "│ ID  │ Username         │ Email                               │ Role             │\n";
    echo "├─────┼──────────────────┼─────────────────────────────────────┼──────────────────┤\n";
    
    while ($row = $result->fetch_assoc()) {
        printf("│ %-3d │ %-16s │ %-35s │ %-16s │\n", 
            $row['user_id'],
            $row['username'],
            $row['email'],
            $row['role']
        );
    }
    echo "└─────┴──────────────────┴─────────────────────────────────────┴──────────────────┘\n\n";
} else {
    echo "No users found in database\n\n";
}

// Check other key tables
echo "Database Statistics:\n";
$tables = ['users', 'categories', 'products', 'customers', 'sales', 'suppliers'];

foreach ($tables as $table) {
    $result = $connection->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        printf("  %-15s %5d records\n", $table, $row['count']);
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✓ Database is ready for use\n";
echo "═══════════════════════════════════════════════════════════════\n";

$connection->close();
?>
