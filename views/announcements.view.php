<?php // session started in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Annonces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <?php if (!empty($_SESSION['color_styles'])): ?>
        <style><?= $_SESSION['color_styles'] ?></style>
    <?php endif; ?>
</head>
<body class="<?= isset($_SESSION['user_preferences']) ? 'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 'bg-light' ?>">
<div class="container mt-5">
    <?php include __DIR__ . '/_nav.php'; ?>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <h3>Annonces</h3>

    <div class="mb-4">
        <?php if (isset($_SESSION['user'])): ?>
            <form method="post" action="index.php?action=announcements_create">
                <div class="mb-2">
                    <label class="form-label">Titre</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Contenu</label>
                    <textarea name="body" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Portée</label>
                    <select name="scope" class="form-select">
                        <option value="global">Globale (administration)</option>
                        <option value="subject">Par matière (professeur)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Matière (optionnel pour portée 'subject')</label>
                    <select name="subject_id" class="form-select">
                        <option value="">-- Aucune --</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?= intval($s['id']) ?>"><?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($s['pseudo'], ENT_QUOTES, 'UTF-8') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Publier</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info">Connectez-vous pour voir et publier des annonces.</div>
        <?php endif; ?>
    </div>

    <div>
        <?php if (empty($announcements)): ?>
            <div class="alert alert-secondary">Aucune annonce pour le moment.</div>
        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($a['body'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <p class="text-muted small">Par <?= htmlspecialchars($a['creator_pseudo'], ENT_QUOTES, 'UTF-8') ?> le <?= htmlspecialchars($a['created_at'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($a['scope'], ENT_QUOTES, 'UTF-8') ?><?= isset($a['subject_id']) && $a['subject_id'] ? ' (matière #' . intval($a['subject_id']) . ')' : '' ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
