<?php 

require_once 'config/database.php';

Class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($nom, $prenoms, $email, $password){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (nom, prenoms, email, password) VALUES (:nom, :prenoms, :email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':prenoms' => $prenoms,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }

    public function findByEmail($email){
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
