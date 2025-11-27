<?php // session is started centrally in index.php ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white mb-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">RéseauSocial</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item me-2">
            <span class="nav-link mb-0">Bienvenue, <strong><?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></span>
          </li>
          <li class="nav-item me-2">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
              <a class="btn btn-info btn-sm text-white btn-spacing" href="index.php?action=pm&amp;admin=1">Messages privés</a>
              <a class="btn btn-dark btn-sm text-white btn-spacing" href="index.php?action=admin">Admin</a>
            <?php else: ?>
              <a class="btn btn-outline-primary btn-sm btn-spacing" href="index.php?action=pm">Messages privés</a>
            <?php endif; ?>
            <a class="btn btn-outline-secondary btn-sm btn-spacing" href="index.php?action=settings">⚙️ Paramètres</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-danger btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Déconnexion</a>
          </li>
        <?php else: ?>
          <li class="nav-item me-2">
            <a class="btn btn-primary btn-sm btn-spacing" href="index.php?action=login" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-success btn-sm" href="index.php?action=register" data-bs-toggle="modal" data-bs-target="#registerModal">Inscription</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
