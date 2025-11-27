<?php // session started centrally in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Messages Privés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
 </head>
<body class="<?= isset($_SESSION['user_preferences']) ? 'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 'bg-light' ?>" <?php if (isset($_SESSION['user_preferences']) && $_SESSION['user_preferences']['background_mode'] === 'custom' && !empty($_SESSION['user_preferences']['custom_background_image'])): ?>style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); background-size: cover; background-attachment: fixed; background-position: center;"<?php endif; ?>>
<div class="container mt-5">
    <?php include __DIR__ . '/_nav.php'; ?>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
    
    <!-- Navigation Buttons -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">🏠 Retour à l'Accueil</a>
        <a href="index.php?action=subject" class="btn btn-info">📚 Catalogue de Sujets</a>
    </div>
    
    <h3>Messages privés</h3>
    <div class="row">
        <div class="col-md-4">
            <h5>Conversations</h5>
            <ul class="list-group">
                <?php foreach ($threads as $t): ?>
                    <li class="list-group-item">
                        <a href="index.php?action=pm&with=<?= intval($t['partner_id']) ?>">
                            <?= htmlspecialchars($t['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-8">
            <?php if ($partner): ?>
                <h5>Conversation avec <?= htmlspecialchars($partner['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
                <div class="card mb-3">
                    <div class="card-body pm-card-body">
                        <?php foreach ($messages as $m): ?>
                                                        <div class="mb-2">
                                                                <strong><?= htmlspecialchars($m['sender_pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                                                <div id="pm-msg-content-<?= $m['id'] ?>"><?= nl2br(htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8')) ?></div>
                                                                <small class="text-muted"><?= $m['created_at'] ?></small>
                                                                <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $m['sender_id'] || $_SESSION['user']['role'] == 'admin')): ?>
                                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPmModal<?= $m['id'] ?>">Modifier</button>
                                                                        <!-- Edit Modal -->
                                                                        <div class="modal fade" id="editPmModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="editPmModalLabel<?= $m['id'] ?>" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <form method="post">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="editPmModalLabel<?= $m['id'] ?>">Modifier le message privé</h5>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <input type="hidden" name="pm_id" value="<?= $m['id'] ?>">
                                                                                            <label for="edit-pm-message-<?= $m['id'] ?>" class="form-label">Message :</label>
                                                                                            <textarea name="pm_message_edit" id="edit-pm-message-<?= $m['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="submit" name="edit_pm" class="btn btn-primary">Enregistrer</button>
                                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                <?php endif; ?>
                                                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form method="post">
                    <input type="hidden" name="to_user" value="<?= $partner['id'] ?>">
                    <div class="mb-2">
                        <textarea name="pm_message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="send_pm" class="btn btn-primary">Envoyer</button>
                </form>
                <?php
                // Pagination
                $pmLimit = 20;
                $pmCount = isset($pmModel) ? $pmModel->countBetween($_SESSION['user']['id'], $partner['id']) : 0;
                $pmPages = ($pmCount > 0) ? ceil($pmCount / $pmLimit) : 1;
                $pmPage = isset($_GET['pm_page']) ? max(1, intval($_GET['pm_page'])) : 1;
                ?>
                <?php if ($pmPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-2">
                            <?php for ($i = 1; $i <= $pmPages; $i++): ?>
                                <li class="page-item <?= ($i == $pmPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?action=pm&with=<?= $partner['id'] ?>&pm_page=<?= $i ?>">Page <?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">Sélectionnez une conversation à gauche ou commencez-en une nouvelle via son profil.</div>
            <?php endif; ?>
    <div class="mt-4">
        <a href="index.php?action=pm&new=1" class="btn btn-success">Nouveau message</a>
    </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/_auth_modals.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
