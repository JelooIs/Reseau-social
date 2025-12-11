<!DOCTYPE html>
<html>
<head>
    <title>Sujets - Réseau Social</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <?php if (!empty($_SESSION['color_styles'])): ?>
        <style><?= $_SESSION['color_styles'] ?></style>
    <?php endif; ?>
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
    <div class="action-bar mb-4">
        <!-- Removed global 'Retour à l'Accueil' to avoid redundancy (subjects is the main catalog) -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <!-- Admin link is provided in the navbar; avoid duplicate here -->
        <?php endif; ?>
    </div>

    <!-- Create Subject Form (logged-in users only) -->
    <?php if (isset($_SESSION['user'])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Créer un Sujet</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="subject_title" class="form-label">Titre du sujet:</label>
                        <input type="text" name="subject_title" id="subject_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_image" class="form-label">Image (optionnelle):</label>
                        <input type="file" name="subject_image" id="subject_image" class="form-control">
                    </div>
                    <button type="submit" name="create_subject" class="btn btn-primary">Créer le Sujet</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center mb-4">
            <strong>Connectez-vous pour créer un sujet.</strong><br>
            <a href="#" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</a>
            <a href="#" class="btn btn-success btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#registerModal">Inscription</a>
        </div>
    <?php endif; ?>

    <!-- Subjects Catalog -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Catalogue de Sujets</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($subjects)): ?>
                <div class="row g-4">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <?php if (!empty($subject['image'])): ?>
                                    <img src="<?= htmlspecialchars($subject['image'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <p class="mb-0">Pas d'image</p>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                                    <p class="card-text text-muted small">
                                        Par <strong><?= htmlspecialchars($subject['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                                        <em><?= $subject['created_at'] ?></em>
                                    </p>
                                </div>
                                <div class="card-footer bg-white action-bar">
                                    <a href="index.php?action=subject&amp;id=<?= $subject['id'] ?>" class="btn btn-primary btn-small">Voir Discussion</a>
                                    <?php if (isset($_SESSION['user'])): ?>
                                        <button class="btn btn-warning btn-small" data-bs-toggle="modal" data-bs-target="#reportSubjectModal<?= $subject['id'] ?>">🚩 Signaler</button>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $subject['user_id'] || $_SESSION['user']['role'] == 'admin')): ?>
                                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                            <button class="btn btn-danger btn-small" data-bs-toggle="modal" data-bs-target="#deleteSubjectModal<?= $subject['id'] ?>">Supprimer</button>
                                        <?php else: ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                                                <button type="submit" name="delete_subject" class="btn btn-danger btn-small" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">Aucun sujet pour l'instant. <a href="#">Créez-en un!</a></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Report Subject Modals -->
    <?php if (isset($_SESSION['user']) && !empty($subjects)): ?>
        <?php foreach ($subjects as $subject): ?>
            <div class="modal fade" id="reportSubjectModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="reportSubjectLabel<?= $subject['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportSubjectLabel<?= $subject['id'] ?>">Signaler ce Sujet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="index.php?action=report">
                            <div class="modal-body">
                                <p>Pourquoi signalez-vous ce sujet?</p>
                                <div class="mb-3">
                                    <textarea name="report_reason" class="form-control" rows="4" placeholder="Décrivez le problème (minimum 10 caractères)" required></textarea>
                                </div>
                                <input type="hidden" name="report_type" value="subject">
                                <input type="hidden" name="report_target_id" value="<?= intval($subject['id']) ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-warning">Signaler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Delete Subject Modals (for admins) -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin' && !empty($subjects)): ?>
        <?php foreach ($subjects as $subject): ?>
            <div class="modal fade" id="deleteSubjectModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="deleteSubjectLabel<?= $subject['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteSubjectLabel<?= $subject['id'] ?>">Supprimer le Sujet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer le sujet <strong><?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?></strong>?</p>
                                <div class="mb-3">
                                    <label for="deletion_reason<?= $subject['id'] ?>" class="form-label">Raison de la suppression (optionnelle):</label>
                                    <textarea id="deletion_reason<?= $subject['id'] ?>" name="deletion_reason" class="form-control" rows="3" placeholder="Expliquez pourquoi ce sujet a été supprimé..."></textarea>
                                </div>
                                <small class="text-muted">L'utilisateur sera notifié par message privé avec la raison.</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                                <button type="submit" name="delete_subject" class="btn btn-danger">Supprimer et Notifier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?action=subject&amp;page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<?php include __DIR__ . '/_logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
