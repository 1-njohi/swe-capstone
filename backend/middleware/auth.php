<?php
require_once __DIR__ . '/../helpers/jwt_helper.php';

function authenticate() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }
    
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $payload = JWT::decode($token);
    
    if (!$payload) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
    
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        http_response_code(401);
        echo json_encode(['error' => 'Token expired']);
        exit;
    }
    
    return $payload;
}

function requireAdmin() {
    $user = authenticate();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    return $user;
}
?>