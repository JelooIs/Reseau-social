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

    <h4>Utilisateurs</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pseudo</th>
                <th>Email</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4 class="mt-5">Messages</h4>
    <table class="table table-bordered">
        <thead>
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
                <td><?= $msg['id'] ?></td>
                <td><?= htmlspecialchars($msg['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if (!empty($msg['image'])): ?>
                        <img src="<?= htmlspecialchars($msg['image'], ENT_QUOTES, 'UTF-8') ?>" class="pm-image-small">
                    <?php endif; ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/_logout_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>