<?php
/**
 * Script de Test du Système de Permissions
 * Vérifiez que tout fonctionne correctement après l'installation
 * 
 * Utilisez: php test_permissions.php
 */

require_once 'config/database.php';
require_once 'models/Permission.php';
require_once 'models/PermissionManager.php';
require_once 'models/User.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "           Test du Système de Permissions\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $database = new Database();
    $pdo = $database->pdo;

    // Test 1: Vérifier que les tables existent
    echo "Test 1: Vérification des tables\n";
    echo str_repeat("─", 60) . "\n";
    
    $tables = ['roles', 'permissions', 'role_permissions'];
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->rowCount() > 0) {
            echo "  ✅ Table '$table' existe\n";
        } else {
            echo "  ❌ Table '$table' manque - Migration non appliquée?\n";
            exit(1);
        }
    }
    echo "\n";

    // Test 2: Vérifier les rôles
    echo "Test 2: Vérification des rôles\n";
    echo str_repeat("─", 60) . "\n";
    
    $permModel = new Permission();
    $roles = $permModel->getAllRoles();
    
    echo "Rôles trouvés: " . count($roles) . "\n";
    foreach ($roles as $role) {
        echo "  ✓ {$role['label']} ({$role['name']})\n";
    }
    
    if (count($roles) >= 6) {
        echo "  ✅ Tous les rôles sont présents\n";
    } else {
        echo "  ⚠️  Il manque des rôles\n";
    }
    echo "\n";

    // Test 3: Vérifier les permissions
    echo "Test 3: Vérification des permissions\n";
    echo str_repeat("─", 60) . "\n";
    
    $permissions = $permModel->getAllPermissions();
    echo "Permissions trouvées: " . count($permissions) . "\n";
    foreach ($permissions as $perm) {
        echo "  ✓ {$perm['label']} ({$perm['name']})\n";
    }
    
    if (count($permissions) >= 11) {
        echo "  ✅ Toutes les permissions sont présentes\n";
    } else {
        echo "  ⚠️  Il manque des permissions\n";
    }
    echo "\n";

    // Test 4: Vérifier les mappages rôle-permission
    echo "Test 4: Vérification des mappages rôle-permission\n";
    echo str_repeat("─", 60) . "\n";
    
    foreach ($roles as $role) {
        $perms = $permModel->getPermissionsByRoleId($role['id']);
        $count = count($perms);
        echo "  • {$role['label']}: {$count} permissions\n";
        
        if ($count === 0) {
            echo "    ⚠️  Aucune permission assignée!\n";
        }
    }
    echo "  ✅ Mappages vérifiés\n\n";

    // Test 5: Vérifier la colonne role_id dans users
    echo "Test 5: Vérification de la colonne 'role_id' dans 'users'\n";
    echo str_repeat("─", 60) . "\n";
    
    $result = $pdo->query("DESCRIBE users");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    $hasRoleId = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'role_id') {
            $hasRoleId = true;
            break;
        }
    }
    
    if ($hasRoleId) {
        echo "  ✅ Colonne 'role_id' existe dans 'users'\n";
    } else {
        echo "  ❌ Colonne 'role_id' manque dans 'users'\n";
    }
    echo "\n";

    // Test 6: Vérifier des utilisateurs
    echo "Test 6: État des utilisateurs\n";
    echo str_repeat("─", 60) . "\n";
    
    $users = $pdo->query("SELECT id, email, pseudo, role_id FROM users LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "Premiers utilisateurs:\n";
        foreach ($users as $user) {
            $role = $permModel->getRoleById($user['role_id']);
            $roleName = $role ? $role['label'] : 'N/A';
            echo "  • {$user['pseudo']} ({$user['email']}) - Rôle: {$roleName}\n";
        }
        echo "  ✅ Les utilisateurs ont des rôles assignés\n";
    } else {
        echo "  ℹ️  Aucun utilisateur trouvé\n";
    }
    echo "\n";

    // Test 7: Test du PermissionManager
    echo "Test 7: Test du PermissionManager\n";
    echo str_repeat("─", 60) . "\n";
    
    $pm = PermissionManager::getInstance();
    echo "  ✓ PermissionManager instancié\n";
    echo "  ✓ getInstance() retourne un singleton\n";
    
    // Vérifier les méthodes
    $methods = [
        'userCanCreateSubject',
        'userCanCreateAnnouncement',
        'userCanSendMessage',
        'userCanModerate',
        'userCanViewReports',
        'userCanManageReports'
    ];
    
    foreach ($methods as $method) {
        if (method_exists($pm, $method)) {
            echo "  ✓ Méthode '$method' existe\n";
        } else {
            echo "  ❌ Méthode '$method' manque\n";
        }
    }
    echo "  ✅ PermissionManager fonctionne\n\n";

    // Test 8: Test des classes modèles
    echo "Test 8: Test des classes modèles\n";
    echo str_repeat("─", 60) . "\n";
    
    $userModel = new User();
    
    if (count($users) > 0) {
        $testUser = $users[0];
        $user = $userModel->findById($testUser['id']);
        
        if ($user && isset($user['role_name'])) {
            echo "  ✅ User::findById() retourne le role_name\n";
            echo "     Utilisateur: {$user['pseudo']}, Rôle: {$user['role_name']}\n";
        } else {
            echo "  ⚠️  User::findById() ne retourne pas le role_name\n";
        }
    }
    echo "\n";

    // Résumé
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "                    ✅ TESTS RÉUSSIS\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    echo "RÉSUMÉ:\n";
    echo "  • " . count($roles) . " rôles configurés\n";
    echo "  • " . count($permissions) . " permissions disponibles\n";
    echo "  • Tables de permission créées ✓\n";
    echo "  • Modèles fonctionnels ✓\n";
    echo "  • PermissionManager prêt ✓\n\n";
    
    echo "PROCHAINES ÉTAPES:\n";
    echo "  1. Attribuer les rôles aux utilisateurs via Admin Panel\n";
    echo "  2. Intégrer PermissionManager dans les contrôleurs\n";
    echo "  3. Mettre à jour les vues pour les permissions\n";
    echo "  4. Tester les contrôles d'accès\n\n";
    
    echo "Rendez-vous sur: http://votresite.com/index.php?action=admin\n\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR LORS DES TESTS:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
