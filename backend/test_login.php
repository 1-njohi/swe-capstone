<?php
require_once 'config/database.php';
require_once 'models/User.php';

$userModel = new User();

// Test 1: Find admin
$user = $userModel->findByUsername('admin');
if (!$user) {
    die("❌ Admin not found in database.\n");
}

echo "✅ User found:\n";
print_r($user);

echo "\n🔑 Password hash: " . $user['password'] . "\n";

// Test 2: Verify password directly
$inputPassword = 'password123';
$result = password_verify($inputPassword, $user['password']);
echo "password_verify('$inputPassword', hash): " . ($result ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";

// Test 3: Check if hash is valid
$info = password_get_info($user['password']);
echo "Hash algorithm: " . $info['algoName'] . "\n";
echo "Hash options: " . print_r($info['options'], true) . "\n";

// Test 4: Try rehashing
if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
    echo "⚠️ Hash needs rehash (old algorithm).\n";
    $newHash = password_hash($inputPassword, PASSWORD_DEFAULT);
    echo "New hash: $newHash\n";
} else {
    echo "✅ Hash is up to date.\n";
}

// Test 5: Try login via curl simulation
$data = ['username' => 'admin', 'password' => 'password123'];
$verify = password_verify($data['password'], $user['password']);
echo "\n🔐 Login simulation: " . ($verify ? "SUCCESS" : "FAILED") . "\n";
