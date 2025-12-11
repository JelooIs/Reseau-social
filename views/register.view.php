<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
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

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-3">Inscription</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-2">
                        <label for="pseudo" class="form-label">Pseudo</label>
                        <input type="text" name="pseudo" id="pseudo" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="action-bar">
                        <button type="submit" name="register" class="btn btn-small primary-action">S'inscrire</button>
                        <a href="index.php?action=login" class="btn btn-outline-secondary btn-small">Déjà inscrit ? Connexion</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/_auth_modals.php'; ?>
    <?php include __DIR__ . '/_logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>