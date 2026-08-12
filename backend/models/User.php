<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();
        return $result ? $result : null;  // ← Convert false to null
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, username, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, name, email, role) 
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['name'],
            $data['email'] ?? null,
            $data['role'] ?? 'student'
        ]);
    }

    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT id, username, name, email, role, created_at FROM users"
        );
        return $stmt->fetchAll();
    }
}
?>