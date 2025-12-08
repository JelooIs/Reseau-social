<?php 

require_once 'config/database.php';

Class User {
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

    public function create($email, $password, $pseudo, $role = 'user'){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        // Insert user with pseudo (no nom/prenoms)
        $sql = "INSERT INTO users (email, password, pseudo, role) VALUES (:email, :password, :pseudo, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $email,
            ':password' => $hashedPassword,
            ':pseudo' => $pseudo,
            ':role' => $role
        ]);
    }

    // delete a user by id
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findByEmail($email){
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verify($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function all() {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search users by pseudo (excluding the current user)
    public function searchByPseudo($pseudo, $exclude_user_id = null) {
        $sql = "SELECT id, pseudo, email FROM users WHERE pseudo LIKE :pseudo";
        
        if ($exclude_user_id) {
            $sql .= " AND id != :exclude_id";
        }
        
        $sql .= " LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $params = [':pseudo' => '%' . $pseudo . '%'];
        
        if ($exclude_user_id) {
            $params[':exclude_id'] = $exclude_user_id;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
