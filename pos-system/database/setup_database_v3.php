<?php
/**
 * Ultra-Robust Database Setup Script v3
 * Uses mysqli::multi_query() for proper SQL execution
 */

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "═══════════════════════════════════════════════════════════════\n";
echo "        POS SYSTEM - DATABASE SETUP (v3 - ULTRA-ROBUST)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Step 1: Connect
echo "[Step 1/4] Connecting to MySQL...\n";
$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_error) {
    die("✗ Connection failed: " . $mysqli->connect_error . "\n");
}
echo "✓ Connected\n\n";

// Step 2: Create and select database
echo "[Step 2/4] Creating and selecting database 'pos_system'...\n";
$mysqli->query("DROP DATABASE IF EXISTS pos_system");
$mysqli->query("CREATE DATABASE pos_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db('pos_system');
echo "✓ Database ready\n\n";

// Step 3: Import schema
echo "[Step 3/4] Importing schema from database_schema.sql...\n";
$schema_file = __DIR__ . '/database_schema.sql';

if (!file_exists($schema_file)) {
    die("✗ Schema file not found: $schema_file\n");
}

$sql_content = file_get_contents($schema_file);

// Use multi_query which is designed for multiple statements
if ($mysqli->multi_query($sql_content)) {
    // Consume all results
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->next_result());
    
    echo "✓ Schema imported successfully\n\n";
} else {
    echo "✗ Schema import error: " . $mysqli->error . "\n";
    echo "  Query: " . substr($sql_content, 0, 100) . "...\n\n";
}

// Step 4: Import seed data
echo "[Step 4/4] Importing seed data from seed_data.sql...\n";
$seed_file = __DIR__ . '/seed_data.sql';

if (!file_exists($seed_file)) {
    die("✗ Seed file not found: $seed_file\n");
}

$seed_content = file_get_contents($seed_file);

if ($mysqli->multi_query($seed_content)) {
    // Consume all results
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->next_result());
    
    echo "✓ Seed data imported successfully\n\n";
} else {
    echo "✗ Seed import error: " . $mysqli->error . "\n";
}

// Verification
echo "═══════════════════════════════════════════════════════════════\n";
echo "                    VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$result = $mysqli->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'pos_system'");
$row = $result->fetch_assoc();
$table_count = $row['count'];

echo "Database: pos_system\n";
echo "Tables created: $table_count/20\n\n";

// List tables
echo "Tables:\n";
$result = $mysqli->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'pos_system' ORDER BY TABLE_NAME");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  ✓ " . $row['TABLE_NAME'] . "\n";
    }
} else {
    echo "  No tables found!\n";
}

// Check data
echo "\n--- Data Records ---\n";
$checks = [
    'users' => 'User accounts',
    'categories' => 'Product categories',
    'products' => 'Products',
    'customers' => 'Customers',
    'sales' => 'Sales transactions',
    'suppliers' => 'Suppliers'
];

foreach ($checks as $table => $label) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "$label: " . $row['count'] . " records\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                    STATUS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if ($table_count == 20) {
    echo "✅ DATABASE SETUP COMPLETE!\n";
    echo "✓ All 20 tables created\n";
    echo "✓ Schema imported\n";
    echo "✓ Seed data loaded\n\n";
    echo "Next: Test connection at http://localhost/pos-system/database/test_connection.php\n";
} else {
    echo "⚠ Setup may not be complete\n";
    echo "✓ Tables found: $table_count/20\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";

$mysqli->close();
?>
