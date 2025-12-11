<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?> - Réseau Social</title>
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
        <!-- Removed 'Retour à l'Accueil' (catalog is main entry). Keep return to catalog only. -->
        <a href="index.php?action=subject" class="btn btn-info btn-small">📚 Retour au Catalogue</a>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <!-- Admin link exists in navbar; avoid duplicate here -->
        <?php endif; ?>
    </div>

    <!-- Subject Header -->
    <div class="card mb-4">
            <div class="card-body">
            <?php if (!empty($subject['image'])): ?>
                <img src="<?= htmlspecialchars($subject['image'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid mb-3" alt="<?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?>" style="max-height: 300px; object-fit: cover;">
            <?php endif; ?>
            <h2><?= htmlspecialchars($subject['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="text-muted">
                Créé par <strong><?= htmlspecialchars($subject['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                <em><?= $subject['created_at'] ?></em>
            </p>
            <div class="action-bar">
                <?php if (isset($_SESSION['user'])): ?>
                    <button class="btn btn-warning btn-small" data-bs-toggle="modal" data-bs-target="#reportSubjectModal">Signaler</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Discussion Thread -->
    <div class="card" id="comments">
        <div class="card-header">
            <h5 class="mb-0">Discussion</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['user'])): ?>
                <form method="post" class="mb-4">
                    <div class="mb-2">
                        <label for="comment_message" class="form-label">Votre message</label>
                        <textarea id="comment_message" name="comment_message" class="form-control" rows="3" required></textarea>
                    </div>
                    <div>
                        <button type="submit" name="post_comment" class="btn btn-primary">Poster</button>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-muted">Connectez-vous pour participer à la discussion.</p>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier !</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($comments as $c): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($c['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="text-muted small"><?= $c['created_at'] ?></div>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['id'] == $c['user_id'])): ?>
                                        <form method="post" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="comment_id" value="<?= intval($c['id']) ?>">
                                            <button type="submit" name="delete_comment" class="btn btn-sm btn-danger">Supprimer</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['user'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportCommentModal<?= $c['id'] ?>">Signaler</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-2"><?= nl2br(htmlspecialchars($c['message'], ENT_QUOTES, 'UTF-8')) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Comments pagination -->
                <?php if ($totalCommentPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination">
                            <?php for ($p = 1; $p <= $totalCommentPages; $p++): ?>
                                <li class="page-item <?= ($p === $cPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?action=subject&id=<?= $subject['id'] ?>&cpage=<?= $p ?>#comments"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Report Subject Modal -->
    <?php if (isset($_SESSION['user'])): ?>
        <div class="modal fade" id="reportSubjectModal" tabindex="-1" aria-labelledby="reportSubjectLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportSubjectLabel">Signaler ce Sujet</h5>
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

        <!-- Report Comment Modals -->
        <?php foreach ($comments as $c): ?>
            <div class="modal fade" id="reportCommentModal<?= $c['id'] ?>" tabindex="-1" aria-labelledby="reportCommentLabel<?= $c['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportCommentLabel<?= $c['id'] ?>">Signaler ce Commentaire</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="index.php?action=report">
                            <div class="modal-body">
                                <p>Pourquoi signalez-vous ce commentaire?</p>
                                <div class="mb-3">
                                    <textarea name="report_reason" class="form-control" rows="4" placeholder="Décrivez le problème (minimum 10 caractères)" required></textarea>
                                </div>
                                <input type="hidden" name="report_type" value="comment">
                                <input type="hidden" name="report_target_id" value="<?= intval($c['id']) ?>">
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
</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<?php include __DIR__ . '/_logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
