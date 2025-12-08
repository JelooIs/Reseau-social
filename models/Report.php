<?php
require_once 'config/database.php';

class Report {
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    /**
     * Create a new report
     * @param int $reporter_id User ID of the person reporting
     * @param string $type 'subject' or 'comment'
     * @param int $target_id ID of the subject or comment being reported
     * @param string $reason Reason for the report
     * @return bool
     */
    public function create($reporter_id, $type, $target_id, $reason) {
        $sql = "INSERT INTO reports (reporter_id, type, target_id, reason, status) 
                VALUES (:reporter_id, :type, :target_id, :reason, 'pending')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reporter_id' => $reporter_id,
            ':type' => $type,
            ':target_id' => $target_id,
            ':reason' => $reason
        ]);
    }

    /**
     * Get all reports with filters
     */
    public function all($limit = 50, $offset = 0, $status = null) {
        $sql = "SELECT r.*, u.pseudo FROM reports r
            JOIN users u ON r.reporter_id = u.id";
        
        if ($status) {
            $sql .= " WHERE r.status = :status";
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count reports by status
     */
    public function count($status = null) {
        $sql = "SELECT COUNT(*) FROM reports";
        
        if ($status) {
            $sql .= " WHERE status = :status";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get a report by ID
     */
    public function findById($id) {
        $sql = "SELECT r.*, u.pseudo FROM reports r
            JOIN users u ON r.reporter_id = u.id
            WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user already reported this item
     */
    public function hasReported($reporter_id, $type, $target_id) {
        $sql = "SELECT COUNT(*) FROM reports 
                WHERE reporter_id = :reporter_id AND type = :type AND target_id = :target_id AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reporter_id' => $reporter_id,
            ':type' => $type,
            ':target_id' => $target_id
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Update report status
     */
    public function updateStatus($id, $status, $admin_note = null) {
        $sql = "UPDATE reports SET status = :status";
        $params = [':status' => $status, ':id' => $id];
        
        if ($admin_note !== null) {
            $sql .= ", admin_note = :admin_note";
            $params[':admin_note'] = $admin_note;
        }
        
        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a report
     */
    public function delete($id) {
        $sql = "DELETE FROM reports WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get report details with target information
     */
    public function getReportWithTarget($id) {
        $sql = "SELECT r.*, u.pseudo FROM reports r
            JOIN users u ON r.reporter_id = u.id
            WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) return null;
        
        // Get target details
        if ($report['type'] === 'subject') {
            $targetSql = "SELECT s.*, u2.pseudo as creator_pseudo 
                         FROM subjects s
                         JOIN users u2 ON s.user_id = u2.id
                         WHERE s.id = :target_id";
            $targetStmt = $this->db->prepare($targetSql);
            $targetStmt->execute([':target_id' => $report['target_id']]);
            $report['target'] = $targetStmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($report['type'] === 'comment') {
            $targetSql = "SELECT c.*, u2.pseudo as creator_pseudo 
                         FROM comments c
                         JOIN users u2 ON c.user_id = u2.id
                         WHERE c.id = :target_id";
            $targetStmt = $this->db->prepare($targetSql);
            $targetStmt->execute([':target_id' => $report['target_id']]);
            $report['target'] = $targetStmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($report['type'] === 'message') {
            $targetSql = "SELECT pm.*, u2.pseudo as creator_pseudo 
                         FROM private_messages pm
                         JOIN users u2 ON pm.sender_id = u2.id
                         WHERE pm.id = :target_id";
            $targetStmt = $this->db->prepare($targetSql);
            $targetStmt->execute([':target_id' => $report['target_id']]);
            $report['target'] = $targetStmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $report;
    }
}
?>
