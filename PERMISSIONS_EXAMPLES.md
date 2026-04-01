<?php
/**
 * Exemples d'Intégration du Système de Permissions
 * 
 * Ce fichier contient des exemples concrets de comment utiliser
 * le nouveau système de permissions dans vos contrôleurs et vues.
 */

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 1: Protéger la création de sujets
// ═══════════════════════════════════════════════════════════════════

// Dans: controllers/SubjectController.php

require_once 'models/PermissionManager.php';

class SubjectControllerExample {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier que l'utilisateur peut créer un sujet
        if (!$pm->userCanCreateSubject()) {
            $_SESSION['error'] = "Vous n'avez pas la permission de créer un sujet.";
            header('Location: index.php?action=subject');
            exit();
        }
        
        // Si la méthode est POST, traiter la création
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation et création du sujet...
        }
        
        // Afficher le formulaire de création
        require 'views/subject_create.view.php';
    }
}

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 2: Contrôle d'accès à la création d'annonces
// ═══════════════════════════════════════════════════════════════════

class AnnouncementControllerExample {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Seuls les profs, BDE, et CA peuvent créer des annonces
        if (!$pm->userCanCreateAnnouncement()) {
            http_response_code(403);
            $_SESSION['error'] = "Seuls les professeurs et membres du BDE/CA peuvent créer des annonces.";
            header('Location: index.php');
            exit();
        }
        
        // Traiter la création...
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Code de création...
        }
        
        require 'views/announcement_create.view.php';
    }
}

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 3: Modération des sujets
// ═══════════════════════════════════════════════════════════════════

class ModeratorControllerExample {
    public function editSubject($subjectId) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier que c'est un modérateur
        if (!$pm->userCanModerate()) {
            http_response_code(403);
            $_SESSION['error'] = "Vous n'avez pas les droits de modération.";
            header('Location: index.php');
            exit();
        }
        
        // Charger et modifier le sujet...
    }
    
    public function deleteSubject($subjectId) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier la permission de suppression (modération)
        if (!$pm->userHasPermission('delete_subject_mod')) {
            http_response_code(403);
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer des sujets.";
            header('Location: index.php?action=subject&id=' . $subjectId);
            exit();
        }
        
        // Supprimer le sujet...
    }
}

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 4: Gestion des signalements
// ═══════════════════════════════════════════════════════════════════

class ReportControllerExample {
    public function viewReports() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier que l'utilisateur peut voir les signalements
        // Professeurs et modérateurs seulement
        if (!$pm->userCanViewReports()) {
            http_response_code(403);
            $_SESSION['error'] = "Vous n'avez pas l'accès aux signalements.";
            header('Location: index.php');
            exit();
        }
        
        // Charger et afficher les signalements...
        require 'views/reports.view.php';
    }
    
    public function closeReport($reportId) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Seul les modérateurs peuvent gérer les signalements
        if (!$pm->userCanManageReports()) {
            http_response_code(403);
            $_SESSION['error'] = "Vous n'avez pas la permission de gérer les signalements.";
            header('Location: index.php?action=reports');
            exit();
        }
        
        // Traiter la fermeture du signalement...
    }
}

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 5: Vérification de permissions multiples
// ═══════════════════════════════════════════════════════════════════

class AdvancedPermissionExample {
    public function sendMessageWithOptions() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier que l'utilisateur peut envoyer des messages
        if (!$pm->userCanSendMessage()) {
            die("Vous ne pouvez pas envoyer de messages.");
        }
        
        // Vérifier si c'est un prof ou un étudiant
        if ($pm->userHasPermission('message_teacher')) {
            // C'est un prof
            $canMessageTeachers = true;
        } else {
            // C'est un étudiant
            $canMessageTeachers = false;
        }
        
        // Autre exemple: vérifier plusieurs permissions (ET)
        if (!$pm->userHasAllPermissions(['create_subject', 'send_message'])) {
            die("Vous devez pouvoir créer des sujets ET envoyer des messages.");
        }
        
        // Autre exemple: vérifier au moins une permission (OU)
        if (!$pm->userHasAnyPermission(['create_announcement', 'manage_reports'])) {
            die("Vous devez être un prof/BDE/CA ou modérateur.");
        }
    }
}

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 6: Utilisation dans les Vues (Template PHP)
// ═══════════════════════════════════════════════════════════════════

// Dans: views/subject_list.view.php

?>

<div class="container mt-5">
    <h2>Sujets de Discussion</h2>
    
    <!-- Barre d'action avec permissions -->
    <div class="action-bar mb-4">
        <?php 
        require_once 'models/PermissionManager.php';
        $pm = PermissionManager::getInstance();
        ?>
        
        <!-- Bouton visible seulement pour les étudiants, profs, BDE/CA -->
        <?php if ($pm->userCanCreateSubject()): ?>
            <a href="index.php?action=create_subject" class="btn btn-primary">
                📝 Créer un sujet
            </a>
        <?php endif; ?>
        
        <!-- Bouton visible pour modérateurs -->
        <?php if ($pm->userCanModerate()): ?>
            <a href="index.php?action=moderation" class="btn btn-warning">
                ⚙️ Modération
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Liste des sujets -->
    <div class="subjects-list">
        <?php foreach ($subjects as $subject): ?>
            <div class="subject-card">
                <h4><?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p><?= htmlspecialchars(substr($subject['content'], 0, 100), ENT_QUOTES, 'UTF-8') ?>...</p>
                
                <div class="subject-actions">
                    <a href="index.php?action=view_subject&id=<?= $subject['id'] ?>" class="btn btn-info btn-sm">
                        Voir
                    </a>
                    
                    <!-- Bouton édition visible seulement pour le créateur ou modérateur -->
                    <?php if ($pm->userCanModerate() || $subject['creator_id'] == $_SESSION['user']['id']): ?>
                        <a href="index.php?action=edit_subject&id=<?= $subject['id'] ?>" class="btn btn-warning btn-sm">
                            ✏️ Éditer
                        </a>
                    <?php endif; ?>
                    
                    <!-- Bouton suppression visible seulement pour modérateur -->
                    <?php if ($pm->userCanModerate()): ?>
                        <form method="POST" style="display:inline;" 
                            onsubmit="return confirm('Êtes-vous sûr?');">
                            <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                            <button type="submit" name="delete_subject" class="btn btn-danger btn-sm">
                                🗑️ Supprimer
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 7: Vue avec Annonces (visibles selon rôle)
// ═══════════════════════════════════════════════════════════════════

// Dans: views/announcements.view.php

?>

<div class="container mt-5">
    <h2>Annonces</h2>
    
    <!-- Section: Créer une annonce (visible seulement pour autorisés) -->
    <?php 
    require_once 'models/PermissionManager.php';
    $pm = PermissionManager::getInstance();
    
    if ($pm->userCanCreateAnnouncement()): 
    ?>
        <div class="create-announcement-section mb-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                📢 Créer une nouvelle annonce
            </button>
        </div>
        
        <!-- Modal de création -->
        <div class="modal fade" id="createAnnouncementModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Créer une annonce</h5>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="create_announcement" class="btn btn-success">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Affichage des annonces -->
    <div class="announcements-list">
        <?php foreach ($announcements as $announcement): ?>
            <div class="alert alert-info">
                <h5><?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                <p><?= htmlspecialchars($announcement['content'], ENT_QUOTES, 'UTF-8') ?></p>
                <small class="text-muted">
                    Par: <?= htmlspecialchars($announcement['author'], ENT_QUOTES, 'UTF-8') ?> 
                    | <?= htmlspecialchars($announcement['created_at'], ENT_QUOTES, 'UTF-8') ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php

// ═══════════════════════════════════════════════════════════════════
// EXEMPLE 8: Vue avec Signalements (admin/modération)
// ═══════════════════════════════════════════════════════════════════

// Dans: views/reports.view.php

?>

<div class="container mt-5">
    <h2>Gestion des Signalements</h2>
    
    <?php 
    require_once 'models/PermissionManager.php';
    $pm = PermissionManager::getInstance();
    
    // Vérifier l'accès
    if (!$pm->userCanViewReports()): 
    ?>
        <div class="alert alert-danger">
            ❌ Vous n'avez pas accès à cette page.
        </div>
    <?php else: ?>
    
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sujet signalé</th>
                    <th>Raison</th>
                    <th>Signalé par</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <?php if ($pm->userCanManageReports()): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= $report['id'] ?></td>
                        <td><?= htmlspecialchars($report['subject_title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['reason'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['reporter_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $report['created_at'] ?></td>
                        <td>
                            <span class="badge bg-<?= $report['status'] === 'open' ? 'danger' : 'success' ?>">
                                <?= $report['status'] === 'open' ? 'Ouvert' : 'Fermé' ?>
                            </span>
                        </td>
                        
                        <!-- Boutons d'action pour modérateurs -->
                        <?php if ($pm->userCanManageReports()): ?>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                    data-bs-target="#viewReportModal" 
                                    onclick="setReportData(<?= $report['id'] ?>)">
                                    Voir
                                </button>
                                <form method="POST" style="display:inline;" 
                                    onsubmit="return confirm('Confirmer?');">
                                    <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                    <button type="submit" name="close_report" class="btn btn-sm btn-success">
                                        Fermer
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php endif; ?>
</div>

<?php
// ═══════════════════════════════════════════════════════════════════
// NOTES D'IMPLÉMENTATION
// ═══════════════════════════════════════════════════════════════════

/*
PATTERNS À UTILISER:

1. Toujours vérifier les permissions au début de la méthode du contrôleur
2. Afficher un message d'erreur clair si refusé
3. Rediriger vers une page appropriée en cas d'accès refusé
4. Dans les vues, utiliser PermissionManager pour afficher/masquer les boutons
5. Ne jamais faire confiance au frontend - toujours vérifier côté serveur

EXEMPLE DE STRUCTURE DE CONTRÔLEUR:

    public function action() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // 1. Vérifier authentication
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        // 2. Vérifier les permissions
        if (!$pm->userCanDoSomething()) {
            http_response_code(403);
            $_SESSION['error'] = "Permission refusée";
            header('Location: index.php');
            exit();
        }
        
        // 3. Traiter la requête
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation et traitement...
        }
        
        // 4. Afficher la vue
        require 'views/template.view.php';
    }
*/
?>
