<?php
require_once __DIR__ . '/TestRunner.php';

$runner = new TestRunner();

// Test the actual API endpoints (requires server running)
$baseUrl = 'https://1e2f-102-0-16-184.ngrok-free.app';

// Test 1: Login endpoint
$runner->addTest('API - Login Endpoint', function() use ($baseUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => 'admin',
        'password' => 'password123'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return 'Login returned status ' . $httpCode . ', expected 200';
    }
    
    $data = json_decode($response, true);
    if (!isset($data['token']) || !isset($data['user'])) {
        return 'Login response missing token or user';
    }
    
    return true;
});

// Test 2: Courses endpoint
$runner->addTest('API - Get Courses', function() use ($baseUrl) {
    // First, login to get token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => 'admin',
        'password' => 'password123'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $loginData = json_decode($response, true);
    if (!isset($loginData['token'])) {
        return 'Failed to get token';
    }
    
    // Get courses
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/courses');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $loginData['token']
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return 'Courses endpoint returned ' . $httpCode . ', expected 200';
    }
    
    $courses = json_decode($response, true);
    if (!is_array($courses) || count($courses) === 0) {
        return 'No courses returned';
    }
    
    return true;
});

$runner->run();
?>