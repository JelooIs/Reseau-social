<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Gestion du réseau social</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Interface Admin</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Retour à l'accueil</a>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?action=admin" class="btn btn-dark btn-sm">Admin</a>
    <?php endif; ?>

    <h4>Utilisateurs</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénoms</th>
                <th>Email</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['nom']) ?></td>
                <td><?= htmlspecialchars($user['prenoms']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
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
                <td><?= htmlspecialchars($msg['nom'] . ' ' . $msg['prenoms']) ?></td>
                <td><?= htmlspecialchars($msg['message']) ?></td>
                <td>
                    <?php if (!empty($msg['image'])): ?>
                        <img src="<?= htmlspecialchars($msg['image']) ?>" style="max-width:80px;">
                    <?php endif; ?>
                </td>
                <td><?= $msg['created_at'] ?></td>
                <td>
                    <form method="post" action="index.php?action=admin" style="display:inline;">
                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>