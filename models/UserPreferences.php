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

    // Update color preferences
    public function setColorPreferences($user_id, $primary_color, $secondary_color, $accent_color, $text_color = null) {
        $sql = "UPDATE user_preferences 
                SET primary_color = :primary, secondary_color = :secondary, accent_color = :accent, text_color = :text 
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':primary' => $primary_color,
            ':secondary' => $secondary_color,
            ':accent' => $accent_color,
            ':text' => $text_color
        ]);
    }

    // Get default color scheme
    public function getDefaultColors() {
        return [
            'primary_color' => '#0d6efd',
            'secondary_color' => '#6c757d',
            'accent_color' => '#198754',
            'text_color' => null
        ];
    }

    // Get color presets
    public function getColorPresets() {
        return [
            'default' => [
                'name' => 'Par défaut',
                'primary' => '#0d6efd',
                'secondary' => '#6c757d',
                'accent' => '#198754'
            ],
            'ocean' => [
                'name' => 'Océan',
                'primary' => '#0ea5e9',
                'secondary' => '#06b6d4',
                'accent' => '#14b8a6'
            ],
            'sunset' => [
                'name' => 'Coucher de soleil',
                'primary' => '#f97316',
                'secondary' => '#fb923c',
                'accent' => '#fbbf24'
            ],
            'forest' => [
                'name' => 'Forêt',
                'primary' => '#16a34a',
                'secondary' => '#22c55e',
                'accent' => '#84cc16'
            ],
            'grape' => [
                'name' => 'Raisin',
                'primary' => '#a855f7',
                'secondary' => '#d946ef',
                'accent' => '#ec4899'
            ],
            'cherry' => [
                'name' => 'Cerise',
                'primary' => '#ef4444',
                'secondary' => '#f87171',
                'accent' => '#fca5a5'
            ]
        ];
    }
}
?>
