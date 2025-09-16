<?php
require_once 'config/database.php';

class Message {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($nom, $prenoms, $message){
        $sql = "INSERT INTO messages (nom, prenoms, message) VALUES (:nom, :prenoms, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':prenoms' => $prenoms,
            ':message' => $message
        ]);
    }

    public function all(){
        $sql = "SELECT * FROM messages ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}