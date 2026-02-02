<?php
require_once 'config/database.php';

class Announcement {
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    public function create($creator_id, $title, $body, $scope = 'global', $subject_id = null) {
        $sql = "INSERT INTO announcements (creator_id, title, body, scope, subject_id) VALUES (:creator_id, :title, :body, :scope, :subject_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':creator_id' => $creator_id,
            ':title' => $title,
            ':body' => $body,
            ':scope' => $scope,
            ':subject_id' => $subject_id
        ]);
    }

    public function allGlobal() {
        $sql = "SELECT a.*, u.pseudo as creator_pseudo FROM announcements a JOIN users u ON a.creator_id = u.id WHERE a.scope = 'global' ORDER BY a.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function forSubject($subject_id) {
        $sql = "SELECT a.*, u.pseudo as creator_pseudo FROM announcements a JOIN users u ON a.creator_id = u.id WHERE a.scope = 'subject' AND a.subject_id = :subject_id ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':subject_id' => $subject_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allVisibleToUser($user_id) {
        // For now return global + announcements tied to subjects created by this user
        $sql = "SELECT a.*, u.pseudo as creator_pseudo FROM announcements a JOIN users u ON a.creator_id = u.id WHERE a.scope = 'global' OR (a.scope = 'subject' AND a.subject_id IN (SELECT id FROM subjects WHERE user_id = :user_id)) ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
