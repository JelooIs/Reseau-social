<?php
require_once 'config/database.php';

class Message {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($user_id, $message){
        $sql = "INSERT INTO messages (user_id, message) VALUES (:user_id, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message
        ]);
    }

    public function all(){
        $sql = "SELECT m.*, u.nom, u.prenoms FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}