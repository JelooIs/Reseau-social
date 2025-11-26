<?php // session started centrally in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gestion des Signalements - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <?php include __DIR__ . '/_nav.php'; ?>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Gestion des Signalements</h3>
        <a href="index.php?action=admin" class="btn btn-secondary">Retour au Tableau de Bord</a>
    </div>

    <!-- Filter by Status -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="index.php?action=reports" class="btn btn-outline-primary <?= !isset($_GET['status']) ? 'active' : '' ?>">Tous</a>
            <a href="index.php?action=reports&status=pending" class="btn btn-outline-warning <?= (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'active' : '' ?>">En Attente</a>
            <a href="index.php?action=reports&status=resolved" class="btn btn-outline-success <?= (isset($_GET['status']) && $_GET['status'] === 'resolved') ? 'active' : '' ?>">Résolus</a>
            <a href="index.php?action=reports&status=dismissed" class="btn btn-outline-secondary <?= (isset($_GET['status']) && $_GET['status'] === 'dismissed') ? 'active' : '' ?>">Rejetés</a>
        </div>
    </div>

    <!-- Reports Table -->
    <?php if (!empty($reports)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Signalant</th>
                        <th>Raison</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['id']) ?></td>
                            <td>
                                <span class="badge <?= $report['type'] === 'subject' ? 'bg-info' : 'bg-secondary' ?>">
                                    <?= $report['type'] === 'subject' ? 'Sujet' : 'Commentaire' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($report['prenoms'] . ' ' . $report['nom']) ?></td>
                            <td><?= htmlspecialchars(substr($report['reason'], 0, 50)) . (strlen($report['reason']) > 50 ? '...' : '') ?></td>
                            <td>
                                <span class="badge <?= 
                                    $report['status'] === 'pending' ? 'bg-warning' : 
                                    ($report['status'] === 'resolved' ? 'bg-success' : 'bg-secondary')
                                ?>">
                                    <?= $report['status'] === 'pending' ? 'En Attente' : ($report['status'] === 'resolved' ? 'Résolue' : 'Rejetée') ?>
                                </span>
                            </td>
                            <td><?= substr($report['created_at'], 0, 10) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal<?= $report['id'] ?>">Détails</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Report Detail Modals -->
        <?php foreach ($reports as $report): ?>
            <div class="modal fade" id="reportModal<?= $report['id'] ?>" tabindex="-1" aria-labelledby="reportModalLabel<?= $report['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel<?= $report['id'] ?>">
                                Détails du Signalement #<?= htmlspecialchars($report['id']) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h6>Informations du Signalement</h6>
                            <p>
                                <strong>Signalant:</strong> <?= htmlspecialchars($report['prenoms'] . ' ' . $report['nom']) ?><br>
                                <strong>Type:</strong> <?= $report['type'] === 'subject' ? 'Sujet' : 'Commentaire' ?><br>
                                <strong>Date:</strong> <?= htmlspecialchars($report['created_at']) ?><br>
                                <strong>Statut:</strong> 
                                <span class="badge <?= 
                                    $report['status'] === 'pending' ? 'bg-warning' : 
                                    ($report['status'] === 'resolved' ? 'bg-success' : 'bg-secondary')
                                ?>">
                                    <?= $report['status'] === 'pending' ? 'En Attente' : ($report['status'] === 'resolved' ? 'Résolue' : 'Rejetée') ?>
                                </span>
                            </p>

                            <h6>Raison du Signalement</h6>
                            <p class="border p-3 bg-light"><?= nl2br(htmlspecialchars($report['reason'])) ?></p>

                            <?php if (!empty($report['admin_note'])): ?>
                                <h6>Note de l'Admin</h6>
                                <p class="border p-3 bg-light"><?= nl2br(htmlspecialchars($report['admin_note'])) ?></p>
                            <?php endif; ?>

                            <?php if ($report['status'] === 'pending'): ?>
                                <h6>Actions</h6>
                                <form method="post">
                                    <input type="hidden" name="report_id" value="<?= htmlspecialchars($report['id']) ?>">
                                    <div class="mb-3">
                                        <label for="admin_note<?= $report['id'] ?>" class="form-label">Note de l'Administrateur (optionnelle):</label>
                                        <textarea id="admin_note<?= $report['id'] ?>" name="admin_note" class="form-control" rows="3" placeholder="Vos notes ou actions prises..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="resolve_report" class="btn btn-success btn-sm">Marquer Résolu</button>
                                        <button type="submit" name="dismiss_report" class="btn btn-secondary btn-sm">Rejeter le Signalement</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?action=reports<?= isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Aucun signalement à afficher.
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<?php include __DIR__ . '/_logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
