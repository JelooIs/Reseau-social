<?php // session started centrally in index.php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Paramètres - Réseau Social</title>
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

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Paramètres de Thème</h3>
        </div>
        <div class="card-body">
            <h5 class="mb-4">Sélectionnez votre thème</h5>

            <!-- Theme Selection -->
            <form method="post" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card border <?= isset($preferences) && $preferences['background_mode'] === 'light' ? 'border-primary' : 'border-secondary' ?>" style="cursor: pointer;" onclick="document.getElementById('light_radio').click()">
                            <div class="card-body text-center bg-light p-5">
                                <h6 class="card-title mb-3">☀️ Mode Clair</h6>
                                <input type="radio" name="background_mode" value="light" id="light_radio" class="d-none" <?= isset($preferences) && $preferences['background_mode'] === 'light' ? 'checked' : '' ?>>
                                <p class="text-muted small">Fond blanc classique</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border <?= isset($preferences) && $preferences['background_mode'] === 'dark' ? 'border-primary' : 'border-secondary' ?>" style="cursor: pointer;" onclick="document.getElementById('dark_radio').click()">
                            <div class="card-body text-center bg-dark text-light p-5">
                                <h6 class="card-title mb-3">🌙 Mode Sombre</h6>
                                <input type="radio" name="background_mode" value="dark" id="dark_radio" class="d-none" <?= isset($preferences) && $preferences['background_mode'] === 'dark' ? 'checked' : '' ?>>
                                <p class="text-muted small">Fond sombre pour les yeux</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border <?= isset($preferences) && $preferences['background_mode'] === 'custom' ? 'border-primary' : 'border-secondary' ?>" style="cursor: pointer;" onclick="document.getElementById('custom_radio').click()">
                            <div class="card-body text-center bg-secondary p-5">
                                <h6 class="card-title mb-3">🖼️ Personnalisé</h6>
                                <input type="radio" name="background_mode" value="custom" id="custom_radio" class="d-none" <?= isset($preferences) && $preferences['background_mode'] === 'custom' ? 'checked' : '' ?>>
                                <p class="text-light small">Votre propre image</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Appliquer le thème</button>
            </form>

            <hr>

            <!-- Custom Background Upload -->
            <h5 class="mb-3">Télécharger un fond d'écran personnalisé</h5>
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="mb-3">
                    <label for="custom_bg_image" class="form-label">Sélectionnez une image (JPG, PNG, GIF, WebP - Max 5MB)</label>
                    <input type="file" name="custom_bg_image" id="custom_bg_image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" name="upload_custom_bg" class="btn btn-success">⬆️ Télécharger et appliquer</button>
            </form>

            <!-- Current Custom Background Preview -->
            <?php if (isset($preferences) && $preferences['background_mode'] === 'custom' && !empty($preferences['custom_background_image'])): ?>
                <hr>
                <h5 class="mb-3">Fond d'écran actuel</h5>
                <div class="mb-3">
                    <img src="<?= htmlspecialchars($preferences['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Custom background" class="img-fluid" style="max-height: 300px; object-fit: cover; border-radius: 8px;">
                </div>
                <form method="post" class="d-inline">
                    <button type="submit" name="delete_custom_bg" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fond d\'écran?')">🗑️ Supprimer</button>
                </form>
            <?php endif; ?>

            <hr>

            <!-- Color Customization Section -->
            <h5 class="mb-4">Palettes de Couleurs</h5>
            <form method="post" class="mb-5">
                <div class="row mb-4">
                    <?php if (isset($colorPresets)): ?>
                        <?php foreach ($colorPresets as $presetName => $colors): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border <?= isset($preferences) && $preferences['primary_color'] === $colors['primary'] && $preferences['secondary_color'] === $colors['secondary'] && $preferences['accent_color'] === $colors['accent'] ? 'border-primary' : 'border-secondary' ?>" style="cursor: pointer;" onclick="document.getElementById('preset_<?= htmlspecialchars($presetName, ENT_QUOTES, 'UTF-8') ?>').click()">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title mb-3">
                                            <input type="radio" name="color_preset" value="<?= htmlspecialchars($presetName, ENT_QUOTES, 'UTF-8') ?>" id="preset_<?= htmlspecialchars($presetName, ENT_QUOTES, 'UTF-8') ?>" class="d-none" <?= isset($preferences) && $preferences['primary_color'] === $colors['primary'] && $preferences['secondary_color'] === $colors['secondary'] && $preferences['accent_color'] === $colors['accent'] ? 'checked' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($presetName, ENT_QUOTES, 'UTF-8')) ?>
                                        </h6>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <div style="width: 50px; height: 50px; background-color: <?= htmlspecialchars($colors['primary'], ENT_QUOTES, 'UTF-8') ?>; border-radius: 4px; border: 1px solid #ddd;" title="Primaire"></div>
                                            <div style="width: 50px; height: 50px; background-color: <?= htmlspecialchars($colors['secondary'], ENT_QUOTES, 'UTF-8') ?>; border-radius: 4px; border: 1px solid #ddd;" title="Secondaire"></div>
                                            <div style="width: 50px; height: 50px; background-color: <?= htmlspecialchars($colors['accent'], ENT_QUOTES, 'UTF-8') ?>; border-radius: 4px; border: 1px solid #ddd;" title="Accent"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="submit" name="apply_color_preset" class="btn btn-primary">Appliquer la palette</button>
            </form>

            <hr>

            <!-- Custom Colors Section -->
            <h5 class="mb-4">Couleurs Personnalisées</h5>
            <form method="post" class="mb-5">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="primary_color" class="form-label">Couleur Primaire</label>
                        <div class="input-group">
                            <input type="color" name="primary_color" id="primary_color" class="form-control form-control-color" value="<?= isset($preferences) && !empty($preferences['primary_color']) ? htmlspecialchars($preferences['primary_color'], ENT_QUOTES, 'UTF-8') : '#0d6efd' ?>" style="max-width: 80px;">
                            <input type="text" class="form-control" id="primary_color_hex" placeholder="#0d6efd" value="<?= isset($preferences) && !empty($preferences['primary_color']) ? htmlspecialchars($preferences['primary_color'], ENT_QUOTES, 'UTF-8') : '#0d6efd' ?>" readonly>
                        </div>
                        <small class="text-muted">Utilisée pour les boutons et les liens</small>
                    </div>
                    <div class="col-md-4">
                        <label for="secondary_color" class="form-label">Couleur Secondaire</label>
                        <div class="input-group">
                            <input type="color" name="secondary_color" id="secondary_color" class="form-control form-control-color" value="<?= isset($preferences) && !empty($preferences['secondary_color']) ? htmlspecialchars($preferences['secondary_color'], ENT_QUOTES, 'UTF-8') : '#6c757d' ?>" style="max-width: 80px;">
                            <input type="text" class="form-control" id="secondary_color_hex" placeholder="#6c757d" value="<?= isset($preferences) && !empty($preferences['secondary_color']) ? htmlspecialchars($preferences['secondary_color'], ENT_QUOTES, 'UTF-8') : '#6c757d' ?>" readonly>
                        </div>
                        <small class="text-muted">Utilisée pour les éléments secondaires</small>
                    </div>
                    <div class="col-md-4">
                        <label for="accent_color" class="form-label">Couleur d'Accent</label>
                        <div class="input-group">
                            <input type="color" name="accent_color" id="accent_color" class="form-control form-control-color" value="<?= isset($preferences) && !empty($preferences['accent_color']) ? htmlspecialchars($preferences['accent_color'], ENT_QUOTES, 'UTF-8') : '#198754' ?>" style="max-width: 80px;">
                            <input type="text" class="form-control" id="accent_color_hex" placeholder="#198754" value="<?= isset($preferences) && !empty($preferences['accent_color']) ? htmlspecialchars($preferences['accent_color'], ENT_QUOTES, 'UTF-8') : '#198754' ?>" readonly>
                        </div>
                        <small class="text-muted">Utilisée pour les éléments d'accent</small>
                    </div>
                </div>
                <button type="submit" name="save_custom_colors" class="btn btn-success">💾 Enregistrer les couleurs personnalisées</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/_auth_modals.php'; ?>
<?php include __DIR__ . '/_logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sync color pickers with hex display
    document.getElementById('primary_color')?.addEventListener('input', function() {
        document.getElementById('primary_color_hex').value = this.value;
    });
    document.getElementById('secondary_color')?.addEventListener('input', function() {
        document.getElementById('secondary_color_hex').value = this.value;
    });
    document.getElementById('accent_color')?.addEventListener('input', function() {
        document.getElementById('accent_color_hex').value = this.value;
    });
</script>
</body>
</html>
