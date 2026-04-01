# Guide d'Implémentation du Système de Permissions

## Vue d'Ensemble

Un système complet de gestion des permissions basé sur les rôles (RBAC - Role-Based Access Control) a été implémenté pour contrôler les actions des différents types d'utilisateurs.

## Architecture

### Base de Données

- **Table `roles`**: Définit les rôles disponibles (étudiant, professeur, BDE, CA, modérateur, admin)
- **Table `permissions`**: Définit les permissions disponibles
- **Table `role_permissions`**: Mappe les rôles à leurs permissions
- **Colonne `role_id` dans `users`**: Lie chaque utilisateur à un rôle

### Classes PHP

1. **Permission.php** - Modèle pour gérer les permissions
2. **PermissionManager.php** - Classe singleton pour accéder facilement aux permissions
3. **User.php** - Modèle utilisateur mis à jour avec support des rôles
4. **AdminController.php** - Contrôleur pour gérer les rôles et permissions

## Rôles Implémentés

| Rôle | Code | Permissions |
|------|------|------------|
| **Étudiant** | `student` | Création de sujets, Messages avec étudiants et profs |
| **Professeur** | `teacher` | Création de sujets, Messages, Annonces, Voir signalements |
| **BDE** | `bde` | Étudiant + Création d'annonces |
| **CA** | `ca` | Étudiant + Création d'annonces |
| **Modérateur** | `moderator` | Gestion des signalements, Suppression/Modification de sujets |
| **Admin** | `admin` | Toutes les permissions |

## Permissions Disponibles

### Gestion des Sujets
- `create_subject` - Créer un nouveau sujet
- `edit_subject` - Modifier ses propres sujets
- `delete_subject` - Supprimer ses propres sujets
- `edit_subject_mod` - Modifier les sujets (modération)
- `delete_subject_mod` - Supprimer les sujets (modération)

### Messagerie
- `send_message` - Envoyer des messages privés
- `message_student` - Communiquer avec les étudiants
- `message_teacher` - Communiquer avec les professeurs

### Annonces
- `create_announcement` - Créer des annonces publiques

### Modération
- `view_reports` - Voir les signalements
- `manage_reports` - Traiter les signalements

## Utilisation dans les Contrôleurs

### Vérifier les Permissions

```php
<?php
require_once 'models/PermissionManager.php';

class SubjectController {
    public function create() {
        $permissionManager = PermissionManager::getInstance();
        
        // Vérifier une permission simple
        if (!$permissionManager->userCanCreateSubject()) {
            http_response_code(403);
            die("Vous n'avez pas la permission de créer un sujet.");
        }
        
        // Vérifier plusieurs permissions (AND)
        if (!$permissionManager->userHasAllPermissions(['create_subject', 'send_message'])) {
            die("Permissions insuffisantes.");
        }
        
        // Vérifier l'une ou l'autre permission (OR)
        if (!$permissionManager->userHasAnyPermission(['create_announcement', 'create_subject'])) {
            die("Permissions insuffisantes.");
        }
        
        // Pour un utilisateur spécifique
        if (!$permissionManager->userIdHasPermission($userId, 'manage_reports')) {
            die("Cet utilisateur n'a pas la permission.");
        }
    }
}
?>
```

### Méthodes Courantes

```php
// Vérifier les permissions courantes
$pm = PermissionManager::getInstance();

$pm->userCanCreateSubject();      // Créer un sujet
$pm->userCanCreateAnnouncement(); // Créer une annonce
$pm->userCanSendMessage();        // Envoyer un message
$pm->userCanModerate();           // Modérer (signalements/sujets)
$pm->userCanViewReports();        // Voir les signalements
$pm->userCanManageReports();      // Gérer les signalements
```

## Utilisation dans les Vues

### Afficher du Contenu Conditionnel

```php
<?php
require_once 'models/PermissionManager.php';
$pm = PermissionManager::getInstance();
?>

<!-- Bouton de création de sujet (visible si permission) -->
<?php if ($pm->userCanCreateSubject()): ?>
    <a href="index.php?action=create_subject" class="btn btn-primary">
        Créer un sujet
    </a>
<?php endif; ?>

<!-- Bouton d'annonce (visible si permission) -->
<?php if ($pm->userCanCreateAnnouncement()): ?>
    <a href="index.php?action=create_announcement" class="btn btn-success">
        Créer une annonce
    </a>
<?php endif; ?>

<!-- Lien modération (visible si modérateur) -->
<?php if ($pm->userCanModerate()): ?>
    <a href="index.php?action=admin&section=moderation" class="btn btn-warning">
        Modération
    </a>
<?php endif; ?>
```

## Migration de la Base de Données

Une migration SQL (`010_create_roles_and_permissions.sql`) a été créée pour initialiser le système.

Pour appliquer la migration:

```bash
# Depuis la ligne de commande MySQL
mysql -u root -p reseau_social < migrations/010_create_roles_and_permissions.sql
```

Ou manuellement via phpMyAdmin.

## Admin Panel

L'interface admin permet de:

### 1. Gérer les Utilisateurs
- Voir la liste de tous les utilisateurs
- Changer le rôle d'un utilisateur
- Supprimer un utilisateur

### 2. Configurer les Rôles
- Sélectionner un rôle
- Voir et modifier ses permissions
- Enregistrer les changements

### 3. Gérer les Messages
- Voir tous les messages
- Supprimer des messages

## Implémentation dans les Contrôleurs Existants

Vous pouvez mettre à jour les contrôleurs existants pour utiliser le nouveau système:

```php
<?php
require_once 'models/PermissionManager.php';

class SubjectController {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier la permission
        if (!$pm->userCanCreateSubject()) {
            header('Location: index.php?error=permission_denied');
            exit();
        }
        
        // Reste du code...
    }
    
    public function delete() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier si modérateur ou créateur
        if (!$pm->userCanModerate() && !$this->isCreator()) {
            header('Location: index.php?error=permission_denied');
            exit();
        }
        
        // Reste du code...
    }
}
?>
```

## Exemples d'Utilisation

### Exemple 1: Contrôle d'Accès à la Page de Création de Sujet

```php
<?php
class SubjectController {
    public function showCreateForm() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        if (!$pm->userCanCreateSubject()) {
            $_SESSION['error'] = "Vous n'avez pas la permission de créer un sujet.";
            header('Location: index.php?action=subject');
            exit();
        }
        
        require 'views/subject_create.view.php';
    }
}
?>
```

### Exemple 2: Contrôle d'Accès à l'Annonce

```php
<?php
class AnnouncementController {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        if (!$pm->userCanCreateAnnouncement()) {
            $_SESSION['error'] = "Seuls les profs et les membres du BDE/CA peuvent créer des annonces.";
            header('Location: index.php');
            exit();
        }
        
        // Traiter la création...
    }
}
?>
```

### Exemple 3: Affichage Conditionnel dans une Vue

```php
<?php
require_once 'models/PermissionManager.php';
$pm = PermissionManager::getInstance();
?>

<div class="action-buttons">
    <!-- Visible pour les étudiants et +  -->
    <?php if ($pm->userCanCreateSubject()): ?>
        <a href="index.php?action=create_subject" class="btn btn-primary">
            📝 Créer un sujet
        </a>
    <?php endif; ?>
    
    <!-- Visible pour les profs et BDE/CA -->
    <?php if ($pm->userCanCreateAnnouncement()): ?>
        <a href="index.php?action=create_announcement" class="btn btn-success">
            📢 Créer une annonce
        </a>
    <?php endif; ?>
    
    <!-- Visible pour les modérateurs et admins -->
    <?php if ($pm->userCanModerate()): ?>
        <a href="index.php?action=admin&section=moderation" class="btn btn-warning">
            ⚙️ Modération
        </a>
    <?php endif; ?>
</div>
```

## Points Importants

1. **Toujours utiliser le PermissionManager** - C'est une classe singleton qui gère tout
2. **Les permissions sont côté serveur** - N'oubliez pas de vérifier les permissions à la fois en affichage ET en traitement
3. **La session doit être démarrée** - Vérifiez que `session_start()` est appelé
4. **Les permissions peuvent être gérées via l'admin panel** - Facilement modifiable sans toucher au code

## Prochaines Étapes

1. Appliquer la migration SQL
2. Mettre à jour les contrôleurs existants pour utiliser le PermissionManager
3. Mettre à jour les vues pour afficher/masquer les options selon les permissions
4. Tester le système d'authentification et les contrôles d'accès

