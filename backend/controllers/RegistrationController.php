<?php
require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../middleware/auth.php';

class RegistrationController {
    private $registrationModel;
    private $courseModel;
    
    public function __construct() {
        $this->registrationModel = new Registration();
        $this->courseModel = new Course();
    }
    
    public function register() {
        $user = authenticate();
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['course_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'course_id required']);
            return;
        }
        
        $result = $this->registrationModel->register($user['id'], $data['course_id']);
        
        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }
    
    public function drop() {
        $user = authenticate();
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['course_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'course_id required']);
            return;
        }
        
        $result = $this->registrationModel->drop($user['id'], $data['course_id']);
        
        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }
    
    public function getMyRegistrations() {
        $user = authenticate();
        $registrations = $this->registrationModel->getUserRegistrations($user['id']);
        echo json_encode($registrations);
    }
    
    public function getCourseRegistrations($courseId) {
        requireAdmin();
        $registrations = $this->registrationModel->getCourseRegistrations($courseId);
        echo json_encode($registrations);
    }
}
?>