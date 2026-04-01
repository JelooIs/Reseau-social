<?php

require_once 'models/Permission.php';

class PermissionManager {
    private static $instance = null;
    private $permissionModel;

    private function __construct() {
        $this->permissionModel = new Permission();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if current user has permission
     */
    public function userHasPermission($permissionName) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            return false;
        }

        // If user is admin, always grant permission
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }

        try {
            return $this->permissionModel->hasPermission($_SESSION['user']['id'], $permissionName);
        } catch (Exception $e) {
            // If permissions system not yet initialized, allow admin role, deny others
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                return true;
            }
            return false;
        }
    }

    /**
     * Check if current user has any of the permissions
     */
    public function userHasAnyPermission($permissionNames) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            return false;
        }

        // If user is admin, always grant permission
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }

        try {
            return $this->permissionModel->hasAnyPermission($_SESSION['user']['id'], $permissionNames);
        } catch (Exception $e) {
            // If permissions system not yet initialized, allow admin role
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                return true;
            }
            return false;
        }
    }

    /**
     * Check if current user has all permissions
     */
    public function userHasAllPermissions($permissionNames) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            return false;
        }

        // If user is admin, always grant permission
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }

        try {
            return $this->permissionModel->hasPermissions($_SESSION['user']['id'], $permissionNames);
        } catch (Exception $e) {
            // If permissions system not yet initialized, allow admin role
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                return true;
            }
            return false;
        }
    }

    /**
     * Check if specific user has permission
     */
    public function userIdHasPermission($userId, $permissionName) {
        return $this->permissionModel->hasPermission($userId, $permissionName);
    }

    /**
     * Check if user can moderate (create/edit/delete subjects and manage reports)
     */
    public function userCanModerate() {
        return $this->userHasAnyPermission([
            'manage_reports',
            'delete_subject_mod',
            'edit_subject_mod'
        ]);
    }

    /**
     * Check if user can create subjects
     */
    public function userCanCreateSubject() {
        return $this->userHasPermission('create_subject');
    }

    /**
     * Check if user can create announcements
     */
    public function userCanCreateAnnouncement() {
        return $this->userHasPermission('create_announcement');
    }

    /**
     * Check if user can send messages
     */
    public function userCanSendMessage() {
        return $this->userHasPermission('send_message');
    }

    /**
     * Check if user can view reports
     */
    public function userCanViewReports() {
        return $this->userHasPermission('view_reports') || $this->userHasPermission('manage_reports');
    }

    /**
     * Check if user can manage reports
     */
    public function userCanManageReports() {
        return $this->userHasPermission('manage_reports');
    }

    /**
     * Get all roles
     */
    public function getAllRoles() {
        return $this->permissionModel->getAllRoles();
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions() {
        return $this->permissionModel->getAllPermissions();
    }

    /**
     * Get role by ID
     */
    public function getRoleById($roleId) {
        return $this->permissionModel->getRoleById($roleId);
    }

    /**
     * Get permissions for role
     */
    public function getRolePermissions($roleId) {
        return $this->permissionModel->getRolePermissionsWithDetails($roleId);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions($roleId, $permissionIds) {
        return $this->permissionModel->updateRolePermissions($roleId, $permissionIds);
    }
}
