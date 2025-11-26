<?php
require_once 'config/database.php';

class Comment {
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($subject_id, $user_id, $message) {
        $sql = "INSERT INTO comments (subject_id, user_id, message) VALUES (:subject_id, :user_id, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':subject_id' => $subject_id,
            ':user_id' => $user_id,
            ':message' => $message
        ]);
    }

    public function allForSubject($subject_id, $limit = 50, $offset = 0) {
        $sql = "SELECT c.*, u.nom, u.prenoms FROM comments c JOIN users u ON c.user_id = u.id WHERE c.subject_id = :subject_id ORDER BY c.created_at ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countForSubject($subject_id) {
        $sql = "SELECT COUNT(*) FROM comments WHERE subject_id = :subject_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':subject_id' => $subject_id]);
        return (int) $stmt->fetchColumn();
    }

    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findById($id) {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
