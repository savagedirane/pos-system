<?php
/**
 * Generate correct bcrypt hashes for test users
 */

$password = 'Test@123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Password: $password\n";
echo "Bcrypt Hash: $hash\n";
echo "\nUse this hash in seed_data.sql for all test users.\n";

// Generate multiple hashes for verification
echo "\n--- Generated hashes for copy-paste ---\n";
for ($i = 0; $i < 5; $i++) {
    echo password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) . "\n";
}
?>
