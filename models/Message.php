<?php
require_once 'config/database.php';

class Message {
    private $db;

    // Allow injecting a PDO for testing; fallback to config/database.php for production
    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($user_id, $message, $image = null){
        $sql = "INSERT INTO messages (user_id, message, image) VALUES (:user_id, :message, :image)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message,
            ':image' => $image
        ]);
    }

    public function all($limit = 10, $offset = 0){
        $sql = "SELECT m.*, u.pseudo FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(){
        $sql = "SELECT COUNT(*) FROM messages";
        return $this->db->query($sql)->fetchColumn();
    }

    public function delete($id) {
        $sql = "DELETE FROM messages WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function update($id, $message, $image = null) {
        $sql = "UPDATE messages SET message = :message, image = :image WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':message' => $message,
            ':image' => $image,
            ':id' => $id
        ]);
    }
}