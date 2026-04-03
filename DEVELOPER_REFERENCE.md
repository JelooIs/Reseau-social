# Personnalisation d'Arrière-Plan - Référence Rapide

## 🎯 Démarrage Rapide

### Accéder à la Fonctionnalité
- **URL** : `index.php?action=settings`
- **Barre de navigation** : Cliquez sur le bouton "⚙️ Paramètres"
- **Requis** : L'utilisateur doit être connecté

### Trois Options de Thème
1. **Clair** - Blanc classique (par défaut)
2. **Sombre** - Mode sombre pour le confort
3. **Personnalisé** - Téléchargez votre propre image

---

## 📁 Structure des Fichiers

```
ReseauSocial/
├── migrations/
│   └── 006_create_user_preferences_table.sql
├── models/
│   └── UserPreferences.php
├── controllers/
│   └── SettingsController.php
├── views/
│   └── settings.view.php
├── assets/css/
│   └── style.css (mis à jour)
└── uploads/
    └── backgrounds/
```

---

## 🔧 Classes/Méthodes Clés

### Modèle UserPreferences
```php
// Obtenir les préférences pour l'utilisateur (crée automatiquement les valeurs par défaut)
$prefs = new UserPreferences();
$preferences = $prefs->getPreferences($user_id);

// Changer de thème
$prefs->setBackgroundMode($user_id, 'dark');

// Télécharger un arrière-plan personnalisé
$prefs->setCustomBackgroundImage($user_id, 'path/to/image.jpg');

// Supprimer l'arrière-plan personnalisé
$prefs->deleteCustomBackgroundImage($user_id);
```

### SettingsController
- Gère les requêtes POST pour les changements de thème
- Valide les téléchargements d'images (type, taille)
- Gère les téléchargements/suppressions de fichiers
- Met à jour la session avec les nouvelles préférences

---

## 🎨 Classes CSS

### Classes Body
```php
// Thème clair
<body class="bg-light">

// Thème sombre
<body class="bg-dark">

// Arrière-plan personnalisé
<body class="bg-custom" style="background-image: url(...)">
```

### Couleurs des Composants

| Élément | Clair | Sombre |
|---------|-------|--------|
| Arrière-plan | #f8f9fa | #1a1a1a |
| Texte | #212529 | #e0e0e0 |
| Cartes | #ffffff | #2d2d2d |
| Bordures | #dee2e6 | #444444 |

---

## 🌐 Variables de Session

```php
// Préférences chargées dans la session
$_SESSION['user_preferences'] = [
    'id' => 1,
    'user_id' => 5,
    'background_mode' => 'dark',
    'custom_background_image' => 'uploads/backgrounds/bg_5_123abc.jpg',
    'created_at' => '2025-11-27 10:00:00',
    'updated_at' => '2025-11-27 12:00:00'
];
```

---

## 📤 Détails du Téléchargement d'Images

### Processus de Téléchargement
1. L'utilisateur sélectionne le fichier
2. Validé par SettingsController
3. Sauvegardé dans `uploads/backgrounds/`
4. Chemin stocké en base de données
5. Ancienne image supprimée

### Règles de Validation
- **Types** : JPG, PNG, GIF, WebP
- **Taille** : Max 5MB
- **Nommage** : `bg_{user_id}_{uniqid}.{ext}`

---

## 🔌 Points d'Intégration

### Dans index.php
```php
// Charger les préférences au démarrage de la session
if (isset($_SESSION['user']) && !isset($_SESSION['user_preferences'])) {
    require_once 'models/UserPreferences.php';
    $prefsModel = new UserPreferences();
    $_SESSION['user_preferences'] = $prefsModel->getPreferences($_SESSION['user']['id']);
}

// Ajouter la route
if (isset($_GET['action']) && $_GET['action'] === 'settings') {
    $controller = new SettingsController();
    $controller->settings();
    exit();
}
```

### Dans Chaque Vue
```php
<body class="<?= isset($_SESSION['user_preferences']) ? 
    'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 
    'bg-light' ?>"
    <?php if (isset($_SESSION['user_preferences']) && 
        $_SESSION['user_preferences']['background_mode'] === 'custom' && 
        !empty($_SESSION['user_preferences']['custom_background_image'])): ?>
    style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); 
           background-size: cover; 
           background-attachment: fixed; 
           background-position: center;"
    <?php endif; ?>>
```

### Dans la Navigation
```php
<a class="btn btn-outline-secondary btn-sm btn-spacing" 
   href="index.php?action=settings">⚙️ Paramètres</a>
```

---

## 🐛 Débogage

### Vérifier la Base de Données
```sql
SELECT * FROM user_preferences WHERE user_id = 5;
```

### Vérifier la Session
```php
echo '<pre>' . print_r($_SESSION['user_preferences'], true) . '</pre>';
```

### Problèmes Courants
- Aucune préférence chargée : Videz le cache du navigateur, reconnectez-vous
- Image ne s'affiche pas : Vérifiez le chemin du fichier, assurez-vous que l'image existe
- Thème ne s'applique pas : Vérifiez que la session est définie, vérifiez que le CSS est chargé

---

## ✅ Règles de Validation

### Téléchargement d'Image
```php
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5242880; // 5MB
if (!in_array($file['type'], $allowed_types)) {
    // Erreur : Type invalide
}
if ($file['size'] > $max_size) {
    // Erreur : Trop volumineux
}
```

### Sélection de Thème
```php
$allowed_modes = ['light', 'dark', 'custom'];
if (in_array($_POST['background_mode'], $allowed_modes)) {
    // Thème valide
}
```

---

## 📊 Requêtes de Base de Données

### Obtenir les Préférences Utilisateur
```sql
SELECT * FROM user_preferences WHERE user_id = ?;
```

### Insérer les Préférences par Défaut
```sql
INSERT INTO user_preferences (user_id, background_mode) 
VALUES (?, 'light');
```

### Mettre à Jour le Thème
```sql
UPDATE user_preferences 
SET background_mode = ? 
WHERE user_id = ?;
```

### Mettre à Jour l'Arrière-Plan Personnalisé
```sql
UPDATE user_preferences 
SET custom_background_image = ?, background_mode = 'custom' 
WHERE user_id = ?;
```

---

## 🎬 Flux Utilisateur

```
1. L'utilisateur se connecte
   ↓ Préférences auto-chargées dans $_SESSION
2. L'utilisateur clique sur ⚙️ Paramètres
   ↓ Redirigé vers /views/settings.view.php
3. L'utilisateur sélectionne un thème ou télécharge une image
   ↓ Formulaire soumis à SettingsController
4. Le contrôleur valide et sauvegarde
   ↓ Base de données mise à jour, session rafraîchie
5. L'utilisateur redirigé vers les paramètres
   ↓ Message flash affiche le succès
6. Thème appliqué à toutes les pages
   ↓ Classe body mise à jour au thème sélectionné
```

---

## 🚀 Notes de Performance

- **Aucun accès base de données par page** : Préférences mises en cache dans la session
- **Basé sur CSS** : Aucun traitement à l'exécution
- **Images paresseuses** : Le navigateur gère le chargement d'arrière-plan
- **Clé de session** : `user_preferences` (recherche simple)

---

## 🔐 Notes de Sécurité

- Échappement HTML : Toute sortie avec ENT_QUOTES, UTF-8
- Validation de fichier : Vérifications de type et taille
- Base de données : Requêtes préparées (pas d'injection SQL)
- Isolation utilisateur : Chaque utilisateur a des préférences uniques
- Nettoyage : Anciennes images supprimées pour éviter l'encombrement

---

## 📱 Points de Rupture Responsives

```css
/* Écrans larges */
.col-md-4 { /* 3 colonnes */ }

/* Écrans moyens */
@media (max-width: 768px) { 
    /* Empiler en 2 colonnes */ 
}

/* Écrans petits */
@media (max-width: 576px) { 
    /* Empiler en 1 colonne */ 
}
```

---

## 💾 Emplacements des Fichiers

| Fichier | Emplacement | Objectif |
|---------|-------------|----------|
| Images Téléchargées | `uploads/backgrounds/` | Stocker les images utilisateur |
| Page Paramètres | `views/settings.view.php` | Interface pour la sélection de thème |
| Styles de Thème | `assets/css/style.css` | CSS pour tous les thèmes |
| Modèle | `models/UserPreferences.php` | Couche d'accès aux données |
| Contrôleur | `controllers/SettingsController.php` | Logique métier |
| Migration | `migrations/006_...sql` | Schéma de base de données |

---

## 🔗 Code Connexe

- **Modèle Utilisateur** : `models/User.php` (stocke les données utilisateur)
- **Session** : `index.php` (gère $_SESSION)
- **Navigation** : `views/_nav.php` (bouton Paramètres)
- **CSS** : `assets/css/style.css` (définitions de thème)

---

## 📞 Aide Rapide

**Q : Comment ajouter un nouveau thème ?**
R : Ajoutez une classe CSS à style.css (ex. `.bg-sepia`) et une option à la page des paramètres

**Q : Comment changer la taille de téléchargement de fichier ?**
R : Éditez `SettingsController.php` ligne avec `5242880` (5MB en octets)

**Q : Comment supprimer les anciennes images ?**
R : La fonction `unlink()` gère déjà cela automatiquement

**Q : Comment modifier les couleurs du thème sombre ?**
R : Éditez la section `.bg-dark` dans `assets/css/style.css`

---

**Dernière Mise à Jour** : 27 novembre 2025
**Version** : 1.0
**Statut** : Prêt pour la Production
