<?php

require_once 'config/database.php';

class Permission {
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
     * Check if a user has a specific permission
     */
    public function hasPermission($userId, $permissionName) {
        $sql = "
            SELECT COUNT(*) as count
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = :user_id AND p.name = :permission_name
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':permission_name' => $permissionName
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Check if a user has multiple permissions (AND logic)
     */
    public function hasPermissions($userId, $permissionNames) {
        foreach ($permissionNames as $permission) {
            if (!$this->hasPermission($userId, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a user has any of the permissions (OR logic)
     */
    public function hasAnyPermission($userId, $permissionNames) {
        foreach ($permissionNames as $permission) {
            if ($this->hasPermission($userId, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all permissions for a role
     */
    public function getPermissionsByRoleId($roleId) {
        $sql = "
            SELECT p.* 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role_id' => $roleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions() {
        $sql = "SELECT * FROM permissions ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all roles
     */
    public function getAllRoles() {
        $sql = "SELECT * FROM roles ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get role by ID
     */
    public function getRoleById($roleId) {
        $sql = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $roleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get role by name
     */
    public function getRoleByName($name) {
        $sql = "SELECT * FROM roles WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update permissions for a role
     */
    public function updateRolePermissions($roleId, $permissionIds) {
        // Delete existing permissions
        $sql = "DELETE FROM role_permissions WHERE role_id = :role_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role_id' => $roleId]);

        // Insert new permissions
        $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
        $stmt = $this->db->prepare($sql);

        foreach ($permissionIds as $permissionId) {
            $stmt->execute([
                ':role_id' => $roleId,
                ':permission_id' => $permissionId
            ]);
        }

        return true;
    }

    /**
     * Get permission by ID
     */
    public function getPermissionById($id) {
        $sql = "SELECT * FROM permissions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get permissions for a role with detailed info
     */
    public function getRolePermissionsWithDetails($roleId) {
        $sql = "
            SELECT p.id, p.name, p.label, p.description
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
            ORDER BY p.label
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role_id' => $roleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
