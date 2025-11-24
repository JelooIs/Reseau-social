<!DOCTYPE html>
<html>
    <head>
        <title>Mini Réseau Social</title>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
  <div class="container mt-5">
    <div class="mb-4">
      <?php if (isset($_SESSION['user'])): ?>
        <div class="d-flex align-items-center justify-content-between">
          <div class="alert alert-success mb-0 w-100">
            Bienvenue, <strong><?= htmlspecialchars($_SESSION['user']['prenoms']) ?></strong> !
          </div>
          <div class="ms-3">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
              <a href="index.php?action=admin" class="btn btn-dark btn-sm me-2">Admin</a>
            <?php endif; ?>
            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">Déconnexion</a>
          </div>
        </div>
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
                                <strong><?= htmlspecialchars($msg['nom'] . ' ' . $msg['prenoms']) ?>:</strong>
                                <span id="msg-content-<?= $msg['id'] ?>"><?= htmlspecialchars($msg['message']) ?></span>
                                <?php if (!empty($msg['image'])): ?>
                                    <br>
                                    <img src="<?= htmlspecialchars($msg['image']) ?>" alt="Image" style="max-width:150px;">
                                <?php endif; ?>
                                <span class="text-muted float-end"><em><?= $msg['created_at'] ?></em></span>
                                <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $msg['user_id'] || $_SESSION['user']['role'] == 'admin')): ?>
                                    <form method="post" style="display:inline;">
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
                                              <textarea name="message" id="edit-message-<?= $msg['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($msg['message']) ?></textarea>
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

    <!-- Fenêtre pop-up Connexion -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" action="index.php?action=login">
            <div class="modal-header">
              <h5 class="modal-title" id="loginModalLabel">Connexion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <label for="email" class="form-label">Email:</label>
              <input type="email" name="email" id="email" class="form-control" required>
              <label for="password" class="form-label mt-2">Mot de passe:</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="submit" name="login" class="btn btn-primary">Se connecter</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Fenêtre pop-up Déconnexion -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" action="index.php?action=logout">
            <div class="modal-header">
              <h5 class="modal-title" id="logoutModalLabel">Déconnexion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Voulez-vous vraiment vous déconnecter ?</p>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger">Déconnexion</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Fenêtre pop-up Inscription -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post" action="index.php?action=register">
            <div class="modal-header">
              <h5 class="modal-title" id="registerModalLabel">Inscription</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <label for="nom" class="form-label">Nom:</label>
              <input type="text" name="nom" id="nom" class="form-control" required>
              <label for="prenoms" class="form-label mt-2">Prénoms:</label>
              <input type="text" name="prenoms" id="prenoms" class="form-control" required>
              <label for="email" class="form-label mt-2">Email:</label>
              <input type="email" name="email" id="email" class="form-control" required>
              <label for="password" class="form-label mt-2">Mot de passe:</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="submit" name="register" class="btn btn-success">S'inscrire</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if (isset($_GET['showLogin'])): ?>
      document.addEventListener('DOMContentLoaded', function(){
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
      });
    <?php endif; ?>
  </script>
    </body>
</html>
