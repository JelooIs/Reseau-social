<?php
require_once 'models/UserPreferences.php';

class SettingsController {
    public function settings() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }

        $prefsModel = new UserPreferences();
        $user_id = $_SESSION['user']['id'];
        $preferences = $prefsModel->getPreferences($user_id);

        // Handle background mode change (light/dark)
        if (isset($_POST['background_mode'])) {
            $mode = $_POST['background_mode'];
            if (in_array($mode, ['light', 'dark', 'custom'])) {
                $prefsModel->setBackgroundMode($user_id, $mode);
                $_SESSION['user_preferences'] = $prefsModel->getPreferences($user_id);
                $_SESSION['flash_message'] = 'Thème mis à jour avec succès!';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?action=settings');
                exit();
            }
        }

        // Handle custom background image upload
        if (isset($_POST['upload_custom_bg']) && !empty($_FILES['custom_bg_image']['name'])) {
            $file = $_FILES['custom_bg_image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['flash_message'] = 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: index.php?action=settings');
                exit();
            }

            if ($file['size'] > 5242880) { // 5MB limit
                $_SESSION['flash_message'] = 'L\'image est trop grande. Maximum 5MB.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: index.php?action=settings');
                exit();
            }

            $filename = 'bg_' . $user_id . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $upload_path = 'uploads/backgrounds/' . $filename;
            
            // Create directory if it doesn't exist
            if (!is_dir('uploads/backgrounds')) {
                mkdir('uploads/backgrounds', 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Delete old custom background if exists
                $old_prefs = $prefsModel->getPreferences($user_id);
                if (!empty($old_prefs['custom_background_image']) && file_exists($old_prefs['custom_background_image'])) {
                    unlink($old_prefs['custom_background_image']);
                }

                // Save new image path
                $prefsModel->setCustomBackgroundImage($user_id, $upload_path);
                $_SESSION['user_preferences'] = $prefsModel->getPreferences($user_id);
                $_SESSION['flash_message'] = 'Fond d\'écran personnalisé appliqué avec succès!';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Erreur lors du téléchargement de l\'image.';
                $_SESSION['flash_type'] = 'danger';
            }
            
            header('Location: index.php?action=settings');
            exit();
        }

        // Handle delete custom background
        if (isset($_POST['delete_custom_bg'])) {
            $old_prefs = $prefsModel->getPreferences($user_id);
            if (!empty($old_prefs['custom_background_image']) && file_exists($old_prefs['custom_background_image'])) {
                unlink($old_prefs['custom_background_image']);
            }
            $prefsModel->deleteCustomBackgroundImage($user_id);
            $_SESSION['user_preferences'] = $prefsModel->getPreferences($user_id);
            $_SESSION['flash_message'] = 'Fond d\'écran personnalisé supprimé.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php?action=settings');
            exit();
        }

        // Handle color scheme preset selection
        if (isset($_POST['apply_color_preset']) && isset($_POST['color_preset'])) {
            $colorPresets = $prefsModel->getColorPresets();
            $preset = $_POST['color_preset'];
            
            if (isset($colorPresets[$preset])) {
                $colors = $colorPresets[$preset];
                $prefsModel->setColorPreferences(
                    $user_id,
                    $colors['primary'],
                    $colors['secondary'],
                    $colors['accent']
                );
                $_SESSION['user_preferences'] = $prefsModel->getPreferences($user_id);
                $_SESSION['flash_message'] = 'Palette de couleurs appliquée avec succès!';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?action=settings');
                exit();
            }
        }

        // Handle custom color selection
        if (isset($_POST['save_custom_colors'])) {
            $primary = $_POST['primary_color'] ?? '#0d6efd';
            $secondary = $_POST['secondary_color'] ?? '#6c757d';
            $accent = $_POST['accent_color'] ?? '#198754';
            
            // Validate hex colors
            if (preg_match('/^#[0-9A-F]{6}$/i', $primary) && 
                preg_match('/^#[0-9A-F]{6}$/i', $secondary) && 
                preg_match('/^#[0-9A-F]{6}$/i', $accent)) {
                
                $prefsModel->setColorPreferences($user_id, $primary, $secondary, $accent);
                $_SESSION['user_preferences'] = $prefsModel->getPreferences($user_id);
                $_SESSION['flash_message'] = 'Couleurs personnalisées enregistrées avec succès!';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Couleurs invalides. Veuillez utiliser le format hexadécimal (#RRGGBB).';
                $_SESSION['flash_type'] = 'danger';
            }
            header('Location: index.php?action=settings');
            exit();
        }

        $colorPresets = $prefsModel->getColorPresets();
        require 'views/settings.view.php';
    }
}
