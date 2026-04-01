<?php // session started centrally in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Gestion du réseau social</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <?php if (!empty($_SESSION['color_styles'])): ?>
        <style><?= $_SESSION['color_styles'] ?></style>
    <?php endif; ?>
    <style>
        .nav-tabs { border-bottom: 2px solid #dee2e6; }
        .nav-tabs .nav-link { color: #495057; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #0d6efd; border-bottom-color: #0d6efd; background: none; }
        .permission-group { margin-bottom: 20px; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; }
        .permission-group h6 { margin-bottom: 10px; font-weight: bold; color: #495057; }
    </style>
</head>
<body class="<?= isset($_SESSION['user_preferences']) ? 'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 'bg-light' ?>" <?php if (isset($_SESSION['user_preferences']) && $_SESSION['user_preferences']['background_mode'] === 'custom' && !empty($_SESSION['user_preferences']['custom_background_image'])): ?>style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); background-size: cover; background-attachment: fixed; background-position: center;"<?php endif; ?>>
<div class="container mt-5">
    <?php include __DIR__ . '/_nav.php'; ?>
    
    <!-- Navigation Buttons -->
    <div class="action-bar mb-4">
        <a href="index.php?action=subject" class="btn btn-info btn-small">📚 Catalogue de Sujets</a>
        <a href="index.php?action=reports" class="btn btn-warning btn-small">📋 Gestion des Signalements</a>
    </div>
    
    <h2 class="mb-4">Interface Admin</h2>

    <!-- Messages d'erreur/succès -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Warning if permissions system not initialized -->
    <?php if (!empty($error) && strpos($error, 'non initialisé') !== false): ?>
        <div class="alert alert-warning" role="alert">
            <strong>⚠️ Système de permissions non encore initialisé</strong>
            <p class="mb-0">Pour initialiser le système, exécutez la commande suivante dans le terminal:</p>
            <code>php run_migration_010.php</code>
            <p class="mt-2 mb-0">Après l'exécution, rafraîchissez cette page.</p>
        </div>
    <?php endif; ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                👥 Utilisateurs
            </button>
        </li>
        <?php if (!empty($roles) && count($roles) > 0): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">
                🔐 Rôles & Permissions
            </button>
        </li>
        <?php endif; ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab">
                💬 Messages
            </button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <h4>Gestion des Utilisateurs</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($user['role_label'] ?? $user['role'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($roles) && count($roles) > 0): ?>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#changeRoleModal" 
                                onclick="setUserRole(<?= $user['id'] ?>, '<?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>', <?= $user['role_id'] ?? 1 ?>)">
                                Changer le rôle
                            </button>
                            <?php else: ?>
                            <button class="btn btn-sm btn-warning" disabled title="Migration requise">
                                Changer le rôle
                            </button>
                            <?php endif; ?>
                            <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr?');">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Roles & Permissions Tab -->
        <?php if (!empty($roles) && count($roles) > 0): ?>
        <div class="tab-pane fade" id="roles" role="tabpanel">
            <h4>Configuration des Rôles et Permissions</h4>
            
            <div class="row">
                <div class="col-md-4">
                    <h5>Rôles disponibles</h5>
                    <div class="list-group">
                        <?php foreach ($roles as $role): ?>
                        <button type="button" class="list-group-item list-group-item-action" 
                            onclick="selectRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['label'], ENT_QUOTES, 'UTF-8') ?>')">
                            <strong><?= htmlspecialchars($role['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></small>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-8">
                    <h5>Permissions du rôle sélectionné</h5>
                    <?php if ($selectedRole): ?>
                    <form method="POST">
                        <input type="hidden" name="role_id" value="<?= $selectedRole['id'] ?>">
                        
                        <h6 class="mb-3">
                            <span class="badge bg-primary"><?= htmlspecialchars($selectedRole['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        </h6>

                        <div class="permission-list">
                            <?php
                            // Group permissions by category
                            $groupedPermissions = [];
                            foreach ($allPermissions as $perm) {
                                $category = explode('_', $perm['name'])[0];
                                if (!isset($groupedPermissions[$category])) {
                                    $groupedPermissions[$category] = [];
                                }
                                $groupedPermissions[$category][] = $perm;
                            }
                            
                            $categoryLabels = [
                                'create' => '📝 Création',
                                'edit' => '✏️ Modification',
                                'delete' => '🗑️ Suppression',
                                'message' => '💬 Messagerie',
                                'send' => '📤 Envoi',
                                'view' => '👁️ Consultation',
                                'manage' => '⚙️ Gestion'
                            ];
                            ?>
                            <?php foreach ($groupedPermissions as $category => $perms): ?>
                            <div class="permission-group">
                                <h6><?= $categoryLabels[$category] ?? ucfirst($category) ?></h6>
                                <?php foreach ($perms as $perm): ?>
                                    <?php 
                                    $isChecked = false;
                                    foreach ($rolePermissions as $rp) {
                                        if ($rp['id'] == $perm['id']) {
                                            $isChecked = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="permissions[]" 
                                            value="<?= $perm['id'] ?>" 
                                            id="perm_<?= $perm['id'] ?>"
                                            <?= $isChecked ? ' checked' : '' ?>>
                                        <label class="form-check-label" for="perm_<?= $perm['id'] ?>">
                                            <strong><?= htmlspecialchars($perm['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars($perm['description'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="save_role_permissions" class="btn btn-success">
                                ✅ Enregistrer les permissions
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-info">
                        Sélectionnez un rôle dans la liste de gauche pour gérer ses permissions.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Roles & Permissions Tab Placeholder -->
        <div class="tab-pane fade" id="roles" role="tabpanel">
            <div class="alert alert-info">
                Le système de permissions n'a pas encore été initialisé. 
                <br />Pour l'initialiser, exécutez: <code>php run_migration_010.php</code>
            </div>
        </div>
        <?php endif; ?>

        <!-- Messages Tab -->
        <div class="tab-pane fade" id="messages" role="tabpanel">
            <h4>Gestion des Messages</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Message</th>
                        <th>Image</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($msg['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(substr($msg['message'] ?? '', 0, 50), ENT_QUOTES, 'UTF-8') ?>...</td>
                        <td>
                            <?php if (!empty($msg['image'])): ?>
                                <img src="<?= htmlspecialchars($msg['image'], ENT_QUOTES, 'UTF-8') ?>" class="img-thumbnail" style="max-width: 50px;">
                            <?php else: ?>
                                <small class="text-muted">Aucune</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($msg['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr?');">
                                <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                <button type="submit" name="delete_message" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<?php if (!empty($roles) && count($roles) > 0): ?>
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le rôle de l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p>Utilisateur: <strong id="modalUserName"></strong></p>
                    <input type="hidden" id="modalUserId" name="user_id" value="">
                    <div class="mb-3">
                        <label for="modalRoleSelect" class="form-label">Nouveau Rôle</label>
                        <select id="modalRoleSelect" name="role_id" class="form-select" required>
                            <option value="">-- Sélectionner un rôle --</option>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>">
                                <?= htmlspecialchars($role['label'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="update_user_role" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/_logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setUserRole(userId, userName, roleId) {
    document.getElementById('modalUserId').value = userId;
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalRoleSelect').value = roleId;
}

function selectRole(roleId, roleName) {
    // Submit form to load role permissions via POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="role_id" value="${roleId}">
        <input type="hidden" name="manage_role_permissions" value="1">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>