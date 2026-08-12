<?php
require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/jwt_helper.php';

$runner = new TestRunner();

// Test 1: User model - find by username
$runner->addTest('User Model - Find Admin User', function() {
    $userModel = new User();
    $user = $userModel->findByUsername('admin');
    
    if (!$user) {
        return 'Admin user not found in database';
    }
    
    if ($user['username'] !== 'admin') {
        return 'Username mismatch: expected admin, got ' . $user['username'];
    }
    
    return true;
});

// Test 2: User model - find by username (non-existent)
$runner->addTest('User Model - Find Non-existent User', function() {
    $userModel = new User();
    $user = $userModel->findByUsername('nonexistent_user_12345');
    
    if ($user !== null) {
        return 'Should not find non-existent user';
    }
    
    return true;
});

// Test 3: User model - find by ID
$runner->addTest('User Model - Find User by ID', function() {
    $userModel = new User();
    $user = $userModel->findById(1);
    
    if (!$user) {
        return 'User with ID 1 not found';
    }
    
    if (!isset($user['id']) || $user['id'] != 1) {
        return 'User ID mismatch';
    }
    
    return true;
});

// Test 4: JWT - generate and decode token
$runner->addTest('JWT - Generate and Decode Token', function() {
    $payload = [
        'id' => 1,
        'username' => 'admin',
        'role' => 'admin'
    ];
    
    $token = JWT::generateToken($payload);
    
    if (empty($token)) {
        return 'Token generation failed';
    }
    
    $decoded = JWT::decode($token);
    
    if (!$decoded) {
        return 'Token decoding failed';
    }
    
    if ($decoded['id'] != 1 || $decoded['username'] !== 'admin') {
        return 'Token payload mismatch';
    }
    
    return true;
});

// Test 5: JWT - invalid token
$runner->addTest('JWT - Invalid Token Rejected', function() {
    $invalidToken = 'invalid.token.here';
    $decoded = JWT::decode($invalidToken);
    
    if ($decoded !== null) {
        return 'Invalid token should be rejected';
    }
    
    return true;
});

// Run the tests
$runner->run();
?>