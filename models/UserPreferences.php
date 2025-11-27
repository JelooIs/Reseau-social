<?php
require_once 'config/database.php';

class UserPreferences {
    private $db;

    public function __construct($pdo = null) {
        if ($pdo instanceof PDO) {
            $this->db = $pdo;
            return;
        }
        $database = new Database();
        $this->db = $database->pdo;
    }

    // Get user preferences (create default if doesn't exist)
    public function getPreferences($user_id) {
        $sql = "SELECT * FROM user_preferences WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$prefs) {
            // Create default preferences for new user
            $this->createDefaultPreferences($user_id);
            return $this->getPreferences($user_id);
        }
        
        return $prefs;
    }

    // Create default preferences for a user
    public function createDefaultPreferences($user_id) {
        $sql = "INSERT INTO user_preferences (user_id, background_mode, custom_background_image) 
                VALUES (:user_id, 'light', NULL)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $user_id]);
    }

    // Update background mode
    public function setBackgroundMode($user_id, $mode) {
        $sql = "UPDATE user_preferences SET background_mode = :mode WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':mode' => $mode
        ]);
    }

    // Update custom background image
    public function setCustomBackgroundImage($user_id, $image_path) {
        $sql = "UPDATE user_preferences SET custom_background_image = :image_path, background_mode = 'custom' WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':image_path' => $image_path
        ]);
    }

    // Delete custom background image
    public function deleteCustomBackgroundImage($user_id) {
        $sql = "UPDATE user_preferences SET custom_background_image = NULL, background_mode = 'light' WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $user_id]);
    }
}
?>
