<?php
/**
 * Script de migration pour initialiser le système de rôles et permissions
 * Exécutez ce script une fois pour configurer la base de données
 * 
 * Utilisez: php run_migration_010.php
 */

require_once 'config/database.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "    Migration 010: Initialisation des Rôles et Permissions\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $database = new Database();
    $pdo = $database->pdo;

    // Check if migration already applied
    $result = $pdo->query("SHOW TABLES LIKE 'roles'");
    if ($result && $result->rowCount() > 0) {
        echo "⚠️  La migration semble déjà avoir été appliquée.\n";
        $confirm = readline("Voulez-vous continuer ? (yes/no): ");
        if ($confirm !== 'yes' && $confirm !== 'y') {
            echo "\nMigration annulée.\n";
            exit(0);
        }
    }

    // List of SQL statements to execute
    $statements = [
        // Create roles table
        "CREATE TABLE IF NOT EXISTS `roles` (
          `id` INT PRIMARY KEY AUTO_INCREMENT,
          `name` VARCHAR(50) UNIQUE NOT NULL,
          `label` VARCHAR(100) NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Create permissions table
        "CREATE TABLE IF NOT EXISTS `permissions` (
          `id` INT PRIMARY KEY AUTO_INCREMENT,
          `name` VARCHAR(100) UNIQUE NOT NULL,
          `label` VARCHAR(150) NOT NULL,
          `description` TEXT,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Create role_permission mapping table
        "CREATE TABLE IF NOT EXISTS `role_permissions` (
          `role_id` INT NOT NULL,
          `permission_id` INT NOT NULL,
          PRIMARY KEY (`role_id`, `permission_id`),
          FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
        )",
        
        // Insert roles
        "INSERT IGNORE INTO `roles` (`name`, `label`) VALUES
        ('student', 'Étudiant'),
        ('teacher', 'Professeur'),
        ('bde', 'Membre du BDE'),
        ('ca', 'Membre du CA'),
        ('moderator', 'Modérateur'),
        ('admin', 'Administrateur')",
        
        // Insert permissions
        "INSERT IGNORE INTO `permissions` (`name`, `label`, `description`) VALUES
        ('create_subject', 'Création de sujets', 'Pouvoir créer de nouveaux sujets de discussion'),
        ('edit_subject', 'Modification de sujets', 'Pouvoir modifier des sujets'),
        ('delete_subject', 'Suppression de sujets', 'Pouvoir supprimer des sujets'),
        ('send_message', 'Envoi de messages', 'Pouvoir envoyer des messages privés'),
        ('message_student', 'Messages avec étudiants', 'Pouvoir communiquer avec les étudiants'),
        ('message_teacher', 'Messages avec profs', 'Pouvoir communiquer avec les professeurs'),
        ('create_announcement', 'Créer des annonces', 'Pouvoir créer des annonces publiques'),
        ('view_reports', 'Voir les signalements', 'Pouvoir consulter les signalements'),
        ('manage_reports', 'Gérer les signalements', 'Pouvoir traiter les signalements'),
        ('delete_subject_mod', 'Supprimer un sujet (modération)', 'Pouvoir supprimer les sujets'),
        ('edit_subject_mod', 'Modifier un sujet (modération)', 'Pouvoir modifier les sujets')",
        
        // Add role_id column to users if not exists
        "ALTER TABLE `users` ADD COLUMN `role_id` INT DEFAULT 1",
        
        // Add foreign key if not exists (we need to handle this carefully)
    ];

    $executedCount = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }

        try {
            echo "Exécution: " . substr($statement, 0, 60) . "...\n";
            $pdo->exec($statement);
            $executedCount++;
            echo "  ✅ OK\n";
        } catch (PDOException $e) {
            // If it's a known ignorable error, skip it
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'Duplicate') !== false || 
                strpos($errorMsg, 'already exists') !== false ||
                strpos($errorMsg, 'Field') !== false) {
                echo "  ⚠️  Ignoré (déjà existe)\n";
                continue;
            }
            throw $e;
        }
    }

    // Now handle the foreign key separately with error handling
    try {
        $pdo->exec("ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL");
        $executedCount++;
        echo "Exécution: ALTER TABLE `users` ADD FOREIGN KEY...\n";
        echo "  ✅ OK\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') === false && strpos($e->getMessage(), 'Duplicate') === false) {
            echo "  ⚠️  Clé étrangère ignorée (déjà existe)\n";
        }
    }

    // Now assign permissions to roles
    $rolePermissions = [
        'student' => [1, 5, 6, 4],  // create_subject, message_student, message_teacher, send_message
        'teacher' => [1, 5, 6, 7, 4, 8],  // + create_announcement, view_reports
        'bde' => [1, 5, 6, 7, 4],  // + create_announcement
        'ca' => [1, 5, 6, 7, 4],  // + create_announcement
        'moderator' => [8, 9, 2, 11],  // view_reports, manage_reports, edit_subject_mod, delete_subject_mod
        'admin' => [1, 2, 3, 5, 6, 7, 4, 8, 9, 10, 11]  // All permissions
    ];

    echo "\nAssignation des permissions aux rôles...\n";
    foreach ($rolePermissions as $roleName => $permIds) {
        $roleResult = $pdo->query("SELECT id FROM roles WHERE name = '$roleName'");
        if ($roleResult && $roleResult->rowCount() > 0) {
            $roleId = $roleResult->fetch(PDO::FETCH_ASSOC)['id'];
            
            foreach ($permIds as $permId) {
                try {
                    $pdo->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES ($roleId, $permId)");
                } catch (PDOException $e) {
                    // Ignore duplicates
                }
            }
            echo "  ✅ {$roleName}\n";
        }
    }

    // Update existing users to have role_id based on role column
    echo "\nMise à jour des rôles des utilisateurs existants...\n";
    $updateSql = "UPDATE `users` SET `role_id` = 
      CASE 
        WHEN `role` = 'student' THEN (SELECT id FROM roles WHERE name = 'student' LIMIT 1)
        WHEN `role` = 'teacher' THEN (SELECT id FROM roles WHERE name = 'teacher' LIMIT 1)
        WHEN `role` = 'moderator' THEN (SELECT id FROM roles WHERE name = 'moderator' LIMIT 1)
        WHEN `role` = 'admin' THEN (SELECT id FROM roles WHERE name = 'admin' LIMIT 1)
        ELSE (SELECT id FROM roles WHERE name = 'student' LIMIT 1)
      END";
    
    try {
        $pdo->exec($updateSql);
        echo "  ✅ Utilisateurs mis à jour\n";
    } catch (PDOException $e) {
        echo "  ⚠️  Mise à jour des utilisateurs ignorée: " . $e->getMessage() . "\n";
    }

    echo "\n✅ Migration réussie !\n";
    echo "   - " . $executedCount . " commandes SQL exécutées\n";
    echo "   - Tables créées: roles, permissions, role_permissions\n";
    echo "   - Rôles créés: student, teacher, bde, ca, moderator, admin\n";
    echo "   - Permissions assignées aux rôles\n\n";

    // Show summary
    echo "Résumé des rôles créés:\n";
    echo str_repeat("─", 60) . "\n";
    
    $roles = $pdo->query("SELECT name, label FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roles as $role) {
        $perms = $pdo->prepare("
            SELECT COUNT(*) as count FROM role_permissions 
            WHERE role_id = (SELECT id FROM roles WHERE name = ?)
        ");
        $perms->execute([$role['name']]);
        $count = $perms->fetch(PDO::FETCH_ASSOC)['count'];
        printf("  %-15s - %s (%d permissions)\n", $role['name'], $role['label'], $count);
    }

    echo "\n" . str_repeat("─", 60) . "\n";
    echo "Rendez-vous sur /index.php?action=admin pour gérer les permissions!\n\n";

} catch (Exception $e) {
    echo "\n❌ Erreur lors de la migration:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}

