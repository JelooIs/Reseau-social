<!DOCTYPE html>
<html>
    <head>
        <title>Mini Réseau Social</title>
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
    <div class="mb-4">
        <a href="index.php?action=subject" class="btn btn-info">📚 Catalogue de Sujets</a>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <a href="index.php?action=admin" class="btn btn-danger">🛡️ Admin</a>
        <?php endif; ?>
    </div>

        <?php if (isset($_SESSION['user'])): ?>
            <!-- Formulaire pour utilisateur connecté -->
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="mb-3">
                    <label for="messages" class="form-label">Message:</label>
                    <textarea name="message" id="messages" rows="3" class="form-control"></textarea>
                    <label for="image" class="form-label mt-2">Image (optionnelle):</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>
                <button type="submit" name="envoyer" class="btn btn-primary">Envoyer</button>
            </form>
        <?php else: ?>
            <!-- Message d'information pour utilisateur non connecté -->
            <div class="alert alert-warning text-center mb-4">
                <strong>Vous devez être connecté pour envoyer un message.</strong><br>
                <a href="index.php?action=login" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</a>
                <a href="index.php?action=register" class="btn btn-success btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#registerModal">Inscription</a>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Messages</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($messages)): ?>
                    <ul class="list-group">
                        <?php foreach ($messages as $msg): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($msg['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>:</strong>
                                <span id="msg-content-<?= $msg['id'] ?>"><?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if (!empty($msg['image'])): ?>
                                  <br>
                                  <img src="<?= htmlspecialchars($msg['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Image" class="pm-image">
                                <?php endif; ?>
                                <span class="text-muted float-end"><em><?= $msg['created_at'] ?></em></span>
                                <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $msg['user_id'] || $_SESSION['user']['role'] == 'admin')): ?>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Supprimer</button>
                                    </form>
                                    <!-- Edit button triggers modal -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMsgModal<?= $msg['id'] ?>">Modifier</button>
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editMsgModal<?= $msg['id'] ?>" tabindex="-1" aria-labelledby="editMsgModalLabel<?= $msg['id'] ?>" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <form method="post" enctype="multipart/form-data">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="editMsgModalLabel<?= $msg['id'] ?>">Modifier le message</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                              <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                              <label for="edit-message-<?= $msg['id'] ?>" class="form-label">Message :</label>
                                              <textarea name="message" id="edit-message-<?= $msg['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                              <label for="edit-image-<?= $msg['id'] ?>" class="form-label mt-2">Image (optionnelle) :</label>
                                              <input type="file" name="image" id="edit-image-<?= $msg['id'] ?>" class="form-control">
                                            </div>
                                            <div class="modal-footer">
                                              <button type="submit" name="edit" class="btn btn-primary">Enregistrer</button>
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Aucun message pour l'instant.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center mt-3">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/_auth_modals.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      <?php if (isset($_GET['showLogin'])): ?>
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
      <?php endif; ?>
      <?php if (isset($_GET['showRegister'])): ?>
        var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
      <?php endif; ?>
    });
  </script>
    </body>
</html>
