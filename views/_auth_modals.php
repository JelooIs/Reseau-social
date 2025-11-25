<!-- Authentication modals: login + register + logout -->
<?php /* Authentication modals used on public pages */ ?>
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
          <label for="email_reg" class="form-label mt-2">Email:</label>
          <input type="email" name="email" id="email_reg" class="form-control" required>
          <label for="password_reg" class="form-label mt-2">Mot de passe:</label>
          <input type="password" name="password" id="password_reg" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="submit" name="register" class="btn btn-success">S'inscrire</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
