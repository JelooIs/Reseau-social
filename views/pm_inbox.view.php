<?php // session started centrally in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Messages Privés</title>
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
        <a href="index.php?action=subject" class="btn btn-info btn-small">📚 Catalogue de Sujets</a>
    </div>
    
    <h3>Messages privés</h3>
    <div class="row">
        <div class="col-md-4">
            <h5>Conversations</h5>
            <ul class="list-group">
                <?php foreach ($threads as $t): ?>
                    <li class="list-group-item">
                        <a href="index.php?action=pm&with=<?= intval($t['partner_id']) ?>">
                            <?= htmlspecialchars($t['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-8">
            <?php if ($partner): ?>
                <h5>Conversation avec <?= htmlspecialchars($partner['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
                <div class="card mb-3">
                    <div class="card-body pm-card-body">
                        <?php foreach ($messages as $m): ?>
                                                        <div class="mb-2">
                                                                <strong><?= htmlspecialchars($m['sender_pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                                                <div id="pm-msg-content-<?= $m['id'] ?>"><?= nl2br(htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8')) ?></div>
                                                                <small class="text-muted"><?= $m['created_at'] ?></small>
                                                                    <div class="action-bar mt-1">
                                                                    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $m['sender_id'] || $_SESSION['user']['role'] == 'admin')): ?>
                                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPmModal<?= $m['id'] ?>">Modifier</button>
                                                                        <!-- Edit Modal -->
                                                                        <div class="modal fade" id="editPmModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="editPmModalLabel<?= $m['id'] ?>" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <form method="post">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="editPmModalLabel<?= $m['id'] ?>">Modifier le message privé</h5>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <input type="hidden" name="pm_id" value="<?= $m['id'] ?>">
                                                                                            <label for="edit-pm-message-<?= $m['id'] ?>" class="form-label">Message :</label>
                                                                                            <textarea name="pm_message_edit" id="edit-pm-message-<?= $m['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="submit" name="edit_pm" class="btn btn-primary">Enregistrer</button>
                                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($_SESSION['user'])): ?>
                                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reportPmModal<?= $m['id'] ?>">🚩 Signaler</button>
                                                                        <!-- Report Modal -->
                                                                        <div class="modal fade" id="reportPmModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="reportPmModalLabel<?= $m['id'] ?>" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <form method="post" action="index.php?action=report">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="reportPmModalLabel<?= $m['id'] ?>">Signaler ce message privé</h5>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <p>Pourquoi signalez-vous ce message?</p>
                                                                                            <div class="mb-3">
                                                                                                <textarea name="report_reason" class="form-control" rows="4" placeholder="Décrivez le problème (minimum 10 caractères)" required></textarea>
                                                                                            </div>
                                                                                            <input type="hidden" name="report_type" value="message">
                                                                                            <input type="hidden" name="report_target_id" value="<?= intval($m['id']) ?>">
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                                            <button type="submit" class="btn btn-danger">Signaler</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form method="post">
                    <input type="hidden" name="to_user" value="<?= $partner['id'] ?>">
                    <div class="mb-2">
                        <textarea name="pm_message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="send_pm" class="btn btn-primary">Envoyer</button>
                </form>
                <?php
                // Pagination
                $pmLimit = 20;
                $pmCount = isset($pmModel) ? $pmModel->countBetween($_SESSION['user']['id'], $partner['id']) : 0;
                $pmPages = ($pmCount > 0) ? ceil($pmCount / $pmLimit) : 1;
                $pmPage = isset($_GET['pm_page']) ? max(1, intval($_GET['pm_page'])) : 1;
                ?>
                <?php if ($pmPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-2">
                            <?php for ($i = 1; $i <= $pmPages; $i++): ?>
                                <li class="page-item <?= ($i == $pmPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?action=pm&with=<?= $partner['id'] ?>&pm_page=<?= $i ?>">Page <?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">Sélectionnez une conversation à gauche ou commencez-en une nouvelle via son profil.</div>
            <?php endif; ?>
    <div class="action-bar mt-4">
        <button class="btn btn-success btn-small" data-bs-toggle="modal" data-bs-target="#newMessageModal">✉️ Nouveau message</button>
    </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageLabel">✉️ Nouveau Message Privé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Cherchez un utilisateur pour lui envoyer un message privé</p>
                <div class="mb-3">
                    <label for="search_user_input" class="form-label">Rechercher un utilisateur</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search_user_input" placeholder="Entrez un pseudo ou un email..." autocomplete="off">
                        <button class="btn btn-primary" type="button" id="search_user_btn">🔍 Chercher</button>
                    </div>
                    <small class="text-muted d-block mt-1">Minimum 2 caractères requis</small>
                </div>
                <div id="search_results" class="border rounded p-2" style="max-height: 350px; overflow-y: auto; display: none;">
                    <ul class="list-group list-group-flush" id="results_list"></ul>
                </div>
                <div id="no_results" class="alert alert-warning mt-2 mb-0" style="display: none;">
                    <strong>Aucun utilisateur trouvé</strong> - Vérifiez l'orthographe du pseudo
                </div>
                <div id="loading_spinner" class="text-center mt-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Recherche en cours...</span>
                    </div>
                    <small class="text-muted d-block mt-1">Recherche en cours...</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Search users via AJAX
    document.getElementById('search_user_btn')?.addEventListener('click', function() {
        const query = document.getElementById('search_user_input').value.trim();
        
        if (query.length < 2) {
            alert('Veuillez entrer au moins 2 caractères');
            return;
        }
        
        // Show loading spinner
        document.getElementById('loading_spinner').style.display = 'block';
        document.getElementById('search_results').style.display = 'none';
        document.getElementById('no_results').style.display = 'none';
        
        // AJAX request to search users
        fetch('index.php?action=pm&search_user=' + encodeURIComponent(query) + '&json=1')
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading_spinner').style.display = 'none';
                
                const resultsList = document.getElementById('results_list');
                const noResults = document.getElementById('no_results');
                const searchResults = document.getElementById('search_results');
                
                resultsList.innerHTML = '';
                
                if (data.length === 0) {
                    searchResults.style.display = 'none';
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                    searchResults.style.display = 'block';
                    
                    data.forEach(user => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action';
                        li.style.cursor = 'pointer';
                        li.style.padding = '12px';
                        li.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <strong>${escapeHtml(user.pseudo)}</strong>
                                    </h6>
                                    <small class="text-muted">${escapeHtml(user.email)}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Envoyer un message</button>
                            </div>
                        `;
                        li.addEventListener('click', function() {
                            window.location.href = 'index.php?action=pm&with=' + user.id;
                        });
                        resultsList.appendChild(li);
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('loading_spinner').style.display = 'none';
                document.getElementById('no_results').style.display = 'block';
                document.getElementById('no_results').textContent = 'Erreur lors de la recherche';
            });
    });
    
    // Allow Enter key to search
    document.getElementById('search_user_input')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('search_user_btn').click();
        }
    });
    
    // Clear search results when input is cleared
    document.getElementById('search_user_input')?.addEventListener('input', function() {
        if (this.value.trim().length === 0) {
            document.getElementById('search_results').style.display = 'none';
            document.getElementById('no_results').style.display = 'none';
            document.getElementById('loading_spinner').style.display = 'none';
        }
    });
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
</body>
</html>
