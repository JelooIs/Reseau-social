<?php
require_once 'config/database.php';

class PrivateMessage {
    /** @var PDO */
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    // send a private message
    public function send($sender_id, $receiver_id, $message) {
        $sql = "INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (:sender, :receiver, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sender' => $sender_id,
            ':receiver' => $receiver_id,
            ':message' => $message
        ]);
    }

    // get list of threads (distinct partners) and last message for a user
    public function threadsForUser($user_id) {
        $sql = "SELECT u.id as partner_id, u.pseudo, MAX(pm.created_at) as last_date, MAX(pm.id) as last_msg_id
            FROM private_messages pm
            JOIN users u ON u.id = (CASE WHEN pm.sender_id = :uid THEN pm.receiver_id ELSE pm.sender_id END)
            WHERE pm.sender_id = :uid OR pm.receiver_id = :uid
            GROUP BY partner_id, u.pseudo
            ORDER BY last_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get messages between two users (with pagination)
    public function messagesBetween($userA, $userB, $limit = 50, $offset = 0) {
        $sql = "SELECT pm.*, us.pseudo AS sender_pseudo
            FROM private_messages pm
                JOIN users us ON us.id = pm.sender_id
                WHERE (sender_id = :a AND receiver_id = :b) OR (sender_id = :b AND receiver_id = :a)
                ORDER BY pm.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':a', $userA, PDO::PARAM_INT);
        $stmt->bindValue(':b', $userB, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)); // show oldest first
    }

    // Count messages between two users
    public function countBetween($userA, $userB) {
        $sql = "SELECT COUNT(*) FROM private_messages WHERE (sender_id = :a AND receiver_id = :b) OR (sender_id = :b AND receiver_id = :a)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':a' => $userA, ':b' => $userB]);
        return $stmt->fetchColumn();
    }

    // Update a private message (only by sender)
    public function update($id, $user_id, $message) {
        $sql = "UPDATE private_messages SET message = :message WHERE id = :id AND sender_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':message' => $message,
            ':id' => $id,
            ':user_id' => $user_id
        ]);
    }

    // Get a private message by ID
    public function findById($id) {
        $sql = "SELECT pm.*, us.pseudo AS sender_pseudo FROM private_messages pm
            JOIN users us ON us.id = pm.sender_id
            WHERE pm.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
