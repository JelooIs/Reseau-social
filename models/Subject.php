<?php
require_once 'config/database.php';

class Subject {
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    // Create a new subject
    public function create($user_id, $title, $image = null) {
        $sql = "INSERT INTO subjects (user_id, title, image) VALUES (:user_id, :title, :image)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':image' => $image
        ]);
    }

    // Get all subjects with pagination
    public function all($limit = 12, $offset = 0) {
        $sql = "SELECT s.*, u.nom, u.prenoms FROM subjects s 
                JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single subject by ID
    public function findById($id) {
        $sql = "SELECT s.*, u.nom, u.prenoms FROM subjects s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Count total subjects
    public function count() {
        $sql = "SELECT COUNT(*) FROM subjects";
        return $this->db->query($sql)->fetchColumn();
    }

    // Delete a subject
    public function delete($id) {
        $sql = "DELETE FROM subjects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Get subject creator info for warning notification
    public function getCreator($id) {
        $sql = "SELECT s.user_id, u.email, u.nom, u.prenoms FROM subjects s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
