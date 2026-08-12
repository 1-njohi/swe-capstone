<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/jwt_helper.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        try {
            // Get raw input
            $rawInput = file_get_contents('php://input');
            
            // Log for debugging
            error_log("=== Login attempt ===");
            error_log("Raw input: " . $rawInput);
            
            if (empty($rawInput)) {
                http_response_code(400);
                echo json_encode(['error' => 'No input data received']);
                return;
            }
            
            $data = json_decode($rawInput, true);
            
            if ($data === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
                return;
            }
            
            error_log("Decoded data: " . print_r($data, true));
            
            if (!isset($data['username']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Username and password required']);
                return;
            }
            
            // Find user
            $user = $this->userModel->findByUsername($data['username']);
            
            error_log("User found: " . ($user ? 'Yes' : 'No'));
            if ($user) {
                error_log("User data: " . print_r($user, true));
                error_log("Stored hash: " . $user['password']);
                error_log("Input password: " . $data['password']);
                error_log("Password verify result: " . (password_verify($data['password'], $user['password']) ? 'TRUE' : 'FALSE'));
            }
            
            if (!$user || !password_verify($data['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                return;
            }
            
            $token = JWT::generateToken($user);
            
            echo json_encode([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            echo json_encode([
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $required = ['username', 'password', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing required field: $field"]);
                return;
            }
        }
        
        if ($this->userModel->create($data)) {
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Username or email already exists']);
        }
    }
    
    public function me() {
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
        $payload = JWT::decode($token);
        
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            return;
        }
        
        $user = $this->userModel->findById($payload['id']);
        echo json_encode($user);
    }
}
?>