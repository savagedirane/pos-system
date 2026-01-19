<?php
/**
 * Quick login test script
 */

// Test 1: Check auth.php endpoint
echo "=== LOGIN TEST ===\n\n";

// Simulate login via curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/pos-system/public/auth.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=login&username=admin_user&password=Test@123');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'cookies.txt');

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
echo "Response:\n";
$response = json_decode($result, true);
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

if ($response['status'] === 'success') {
    echo "✓ LOGIN TEST PASSED!\n";
    echo "  User: " . $response['data']['username'] . "\n";
    echo "  Role: " . $response['data']['role'] . "\n";
    echo "  Email: " . $response['data']['email'] . "\n";
} else {
    echo "✗ LOGIN TEST FAILED!\n";
    echo "  Error: " . $response['message'] . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
