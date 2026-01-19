<?php
$mysqli = new mysqli('localhost', 'root', '', 'pos_system');

echo "Users in database:\n";
$result = $mysqli->query('SELECT user_id, username, first_name, email, is_active FROM users LIMIT 10');

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['user_id']} | Username: {$row['username']} | Name: {$row['first_name']} | Active: {$row['is_active']}\n";
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

echo "\n\nPassword hashes:\n";
$result = $mysqli->query('SELECT user_id, username, password_hash FROM users LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo "Username: {$row['username']} | Hash: " . substr($row['password_hash'], 0, 20) . "...\n";
}
?>
