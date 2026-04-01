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
        // Try to join with roles table if it exists
        try {
            $sql = "SELECT u.*, r.name as role_name, r.label as role_label 
                    FROM users u 
                    LEFT JOIN roles r ON u.role_id = r.id 
                    WHERE u.email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If roles table doesn't exist, query without join
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function findById($id) {
        // Try to join with roles table if it exists
        try {
            $sql = "SELECT u.*, r.name as role_name, r.label as role_label 
                    FROM users u 
                    LEFT JOIN roles r ON u.role_id = r.id 
                    WHERE u.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If roles table doesn't exist, query without join
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function verify($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function all() {
        // Try to join with roles table if it exists
        try {
            $sql = "SELECT u.*, r.name as role_name, r.label as role_label 
                    FROM users u 
                    LEFT JOIN roles r ON u.role_id = r.id 
                    ORDER BY u.id";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If roles table doesn't exist, query without join
            $sql = "SELECT * FROM users ORDER BY id";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Update user role
     */
    public function updateRole($userId, $roleId) {
        $sql = "UPDATE users SET role_id = :role_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':role_id' => $roleId,
            ':id' => $userId
        ]);
    }

    /**
     * Get user role information
     */
    public function getUserRole($userId) {
        $sql = "SELECT r.* FROM roles r 
                JOIN users u ON u.role_id = r.id 
                WHERE u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
