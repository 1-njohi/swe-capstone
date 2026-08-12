<?php
require_once __DIR__ . '/../config/database.php';

class Registration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($userId, $courseId) {
        // Check if already registered
        $stmt = $this->db->prepare(
            "SELECT * FROM registrations WHERE user_id = ? AND course_id = ? AND status = 'active'"
        );
        $stmt->execute([$userId, $courseId]);
        if ($stmt->fetch()) {
            return ['error' => 'Already registered for this course'];
        }
        
        // Check course capacity
        $courseStmt = $this->db->prepare(
            "SELECT capacity, enrolled FROM courses WHERE id = ?"
        );
        $courseStmt->execute([$courseId]);
        $course = $courseStmt->fetch();
        
        if ($course && $course['enrolled'] >= $course['capacity']) {
            return ['error' => 'Course is full'];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Create registration
            $stmt = $this->db->prepare(
                "INSERT INTO registrations (user_id, course_id) VALUES (?, ?)"
            );
            $stmt->execute([$userId, $courseId]);
            
            // Increment enrolled count
            $updateStmt = $this->db->prepare(
                "UPDATE courses SET enrolled = enrolled + 1 WHERE id = ?"
            );
            $updateStmt->execute([$courseId]);
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Registration successful'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['error' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function drop($userId, $courseId) {
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE registrations SET status = 'dropped' 
                 WHERE user_id = ? AND course_id = ? AND status = 'active'"
            );
            $stmt->execute([$userId, $courseId]);
            
            if ($stmt->rowCount() === 0) {
                return ['error' => 'Registration not found or already dropped'];
            }
            
            $updateStmt = $this->db->prepare(
                "UPDATE courses SET enrolled = enrolled - 1 WHERE id = ? AND enrolled > 0"
            );
            $updateStmt->execute([$courseId]);
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Course dropped successfully'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['error' => 'Drop failed: ' . $e->getMessage()];
        }
    }
    
    public function getUserRegistrations($userId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, r.registered_at, r.status 
             FROM registrations r
             JOIN courses c ON r.course_id = c.id
             WHERE r.user_id = ? AND r.status = 'active'"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getCourseRegistrations($courseId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.username, u.name, u.email, r.registered_at 
             FROM registrations r
             JOIN users u ON r.user_id = u.id
             WHERE r.course_id = ? AND r.status = 'active'"
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
}
?>