<?php
require_once __DIR__ . '/../config/database.php';

class Course {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query(
            "SELECT *, (capacity - enrolled) as available_spots FROM courses ORDER BY code"
        );
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT *, (capacity - enrolled) as available_spots FROM courses WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO courses (code, name, description, credits, capacity) 
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'],
            $data['credits'] ?? 3,
            $data['capacity'] ?? 30
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET name = ?, description = ?, credits = ?, capacity = ? 
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['credits'],
            $data['capacity'],
            $id
        ]);
    }
    
    public function incrementEnrolled($courseId) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET enrolled = enrolled + 1 WHERE id = ? AND enrolled < capacity"
        );
        return $stmt->execute([$courseId]);
    }
    
    public function decrementEnrolled($courseId) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET enrolled = enrolled - 1 WHERE id = ? AND enrolled > 0"
        );
        return $stmt->execute([$courseId]);
    }
}
?>