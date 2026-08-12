<?php
// ============================================
// CORS CONFIGURATION - MUST BE AT THE VERY TOP
// ============================================

// Get the requesting origin
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// List of allowed origins (add your Vercel URL)
$allowed_origins = [
    'http://localhost:8000',
    'http://localhost:3000',
    'https://swe-capstone-tawny.vercel.app',   // <-- ADD YOUR VERCEL URL
    'https://*.vercel.app',                    // wildcard for all Vercel subdomains
];

// Allow the origin if it's in the list or ends with .vercel.app
$allow_origin = '';
if (in_array($origin, $allowed_origins) || strpos($origin, '.vercel.app') !== false) {
    $allow_origin = $origin;
} else {
    // Fallback: allow all (for local testing) - remove in production
    $allow_origin = '*';
}

// Send CORS headers
header('Access-Control-Allow-Origin: ' . $allow_origin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); // Cache preflight for 24 hours

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // No further execution needed for OPTIONS
}
// Autoload files
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
        __DIR__ . '/helpers/',
        __DIR__ . '/middleware/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';
$path = explode('/', trim($path, '/'));
$resource = $path[0] ?? '';
$id = $path[1] ?? null;

try {
    switch ($resource) {
        case 'auth':
            $controller = new AuthController();
            if ($method === 'POST' && ($id === 'login' || !$id)) {
                $controller->login();
            } elseif ($method === 'POST' && $id === 'register') {
                $controller->register();
            } elseif ($method === 'GET' && $id === 'me') {
                $controller->me();
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Auth endpoint not found']);
            }
            break;
            
        case 'courses':
            $controller = new CourseController();
            if ($method === 'GET' && !$id) {
                $controller->getAll();
            } elseif ($method === 'GET' && $id) {
                $controller->getById($id);
            } elseif ($method === 'POST' && !$id) {
                $controller->create();
            } elseif ($method === 'PUT' && $id) {
                $controller->update($id);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Course endpoint not found']);
            }
            break;
            
        case 'registrations':
            $controller = new RegistrationController();
            if ($method === 'POST' && $id === 'register') {
                $controller->register();
            } elseif ($method === 'POST' && $id === 'drop') {
                $controller->drop();
            } elseif ($method === 'GET' && ($id === 'my' || !$id)) {
                $controller->getMyRegistrations();
            } elseif ($method === 'GET' && $id === 'course' && isset($path[2])) {
                $controller->getCourseRegistrations($path[2]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Registration endpoint not found']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>