<?php
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../middleware/auth.php';

class CourseController {
    private $courseModel;
    
    public function __construct() {
        $this->courseModel = new Course();
    }
    
    public function getAll() {
        authenticate(); // Any authenticated user can view courses
        $courses = $this->courseModel->getAll();
        echo json_encode($courses);
    }
    
    public function getById($id) {
        authenticate();
        $course = $this->courseModel->findById($id);
        
        if (!$course) {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
            return;
        }
        
        echo json_encode($course);
    }
    
    public function create() {
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        
        $required = ['code', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing required field: $field"]);
                return;
            }
        }
        
        if ($this->courseModel->create($data)) {
            http_response_code(201);
            echo json_encode(['message' => 'Course created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Course creation failed']);
        }
    }
    
    public function update($id) {
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->courseModel->update($id, $data)) {
            echo json_encode(['message' => 'Course updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Course update failed']);
        }
    }
}
?>