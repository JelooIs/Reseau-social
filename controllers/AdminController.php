<?php 
require_once 'models/User.php';
require_once 'models/Message.php';
require_once 'models/Permission.php';
require_once 'models/PermissionManager.php';

class AdminController {
    private $userModel;
    private $messageModel;
    private $permissionModel;
    private $permissionManager;

    public function __construct() {
        $this->userModel = new User();
        $this->messageModel = new Message();
        $this->permissionModel = new Permission();
        $this->permissionManager = PermissionManager::getInstance();
    }

    public function admin() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }

        $users = $this->userModel->all();
        $messages = $this->messageModel->all();
        
        // Initialize with empty arrays in case permissions system not yet set up
        $roles = [];
        $allPermissions = [];
        $rolesTableExists = false;
        
        try {
            $roles = $this->permissionManager->getAllRoles();
            $allPermissions = $this->permissionManager->getAllPermissions();
            $rolesTableExists = true;
        } catch (Exception $e) {
            // Permissions system not yet initialized - show a helpful message
            $error = "⚠️  Système de permissions non initialisé. Exécutez: <code>php run_migration_010.php</code>";
        }
        
        $selectedRole = null;
        $rolePermissions = [];
        $success = '';

        // Handle user role update
        if (isset($_POST['update_user_role'])) {
            if (!$rolesTableExists) {
                $error = "Le système de rôles n'est pas encore initié. Exécutez d'abord la migration.";
            } else {
                $userId = intval($_POST['user_id']);
                $roleId = intval($_POST['role_id']);
                if ($this->userModel->updateRole($userId, $roleId)) {
                    $success = "Rôle de l'utilisateur mis à jour avec succès.";
                    // Refresh users list
                    $users = $this->userModel->all();
                    header('Location: index.php?action=admin&section=users');
                    exit();
                } else {
                    $error = "Erreur lors de la mise à jour du rôle de l'utilisateur.";
                }
            }
        }

        // Handle delete user
        if (isset($_POST['delete_user'])) {
            $userId = intval($_POST['user_id']);
            if ($this->userModel->delete($userId)) {
                $success = "Utilisateur supprimé avec succès.";
                $users = $this->userModel->all();
                header('Location: index.php?action=admin&section=users');
                exit();
            } else {
                $error = "Erreur lors de la suppression de l'utilisateur.";
            }
        }

        // Handle delete message
        if (isset($_POST['delete_message'])) {
            $messageId = intval($_POST['message_id']);
            if ($this->messageModel->delete($messageId)) {
                $success = "Message supprimé avec succès.";
                $messages = $this->messageModel->all();
                header('Location: index.php?action=admin&section=messages');
                exit();
            } else {
                $error = "Erreur lors de la suppression du message.";
            }
        }

        // Handle role permissions management
        if (isset($_POST['manage_role_permissions']) && $rolesTableExists) {
            $roleId = intval($_POST['role_id']);
            $selectedRole = $this->permissionManager->getRoleById($roleId);
            $rolePermissions = $this->permissionManager->getRolePermissions($roleId);
        }

        // Handle update role permissions
        if (isset($_POST['save_role_permissions']) && $rolesTableExists) {
            $roleId = intval($_POST['role_id']);
            $permissionIds = isset($_POST['permissions']) ? array_map('intval', $_POST['permissions']) : [];
            
            if ($this->permissionManager->updateRolePermissions($roleId, $permissionIds)) {
                $success = "Permissions du rôle mises à jour avec succès.";
                $selectedRole = $this->permissionManager->getRoleById($roleId);
                $rolePermissions = $this->permissionManager->getRolePermissions($roleId);
            } else {
                $error = "Erreur lors de la mise à jour des permissions.";
            }
        }

        require 'views/admin.view.php';
    }
}