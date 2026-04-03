# Fonctionnalité de Personnalisation d'Arrière-Plan - Résumé d'Implémentation

## Vue d'ensemble
Les utilisateurs peuvent désormais personnaliser l'arrière-plan de l'ensemble du site avec trois options :
1. **Mode Clair** - Arrière-plan blanc classique (par défaut)
2. **Mode Sombre** - Arrière-plan sombre pour une visualisation confortable
3. **Personnalisé** - Image d'arrière-plan propre à l'utilisateur

## Fichiers Créés

### 1. Migration de Base de Données
- **Fichier** : `migrations/006_create_user_preferences_table.sql`
- **Objectif** : Crée la table `user_preferences` pour stocker les préférences de thème des utilisateurs
- **Colonnes** :
  - `user_id` (UNIQUE, FK vers users.id)
  - `background_mode` (ENUM : light/dark/custom)
  - `custom_background_image` (VARCHAR pour le chemin de l'image)

### 2. Modèle
- **Fichier** : `models/UserPreferences.php`
- **Méthodes** :
  - `getPreferences($user_id)` - Obtenir les préférences utilisateur avec valeur par défaut
  - `createDefaultPreferences($user_id)` - Créer des préférences par défaut pour les nouveaux utilisateurs
  - `setBackgroundMode($user_id, $mode)` - Changer de thème
  - `setCustomBackgroundImage($user_id, $image_path)` - Télécharger une image personnalisée
  - `deleteCustomBackgroundImage($user_id)` - Supprimer l'image personnalisée

### 3. Contrôleur
- **Fichier** : `controllers/SettingsController.php`
- **Fonctionnalités** :
  - Gérer la sélection de thème (clair/sombre)
  - Traiter les téléchargements d'images (max 5MB, supporte JPG/PNG/GIF/WebP)
  - Supprimer les arrière-plans personnalisés
  - Nettoyage automatique des anciennes images

### 4. Vue
- **Fichier** : `views/settings.view.php`
- **Éléments d'Interface** :
  - Trois cartes de thème (Clair/Sombre/Personnalisé) pour une sélection facile
  - Formulaire de téléchargement d'image avec support du glisser-déposer
  - Aperçu de l'arrière-plan personnalisé actuel
  - Bouton de suppression pour les images personnalisées

## Améliorations CSS
**Fichier** : `assets/css/style.css`

### Nouvelles Classes de Thème
- `.bg-light` - Thème clair avec contraste de texte approprié
- `.bg-dark` - Thème sombre avec schéma de couleurs personnalisé pour tous les éléments
- `.bg-custom` - Image personnalisée avec superposition semi-transparente pour la lisibilité

### Fonctionnalités du Thème Sombre
- Arrière-plans de cartes sombres (#2d2d2d)
- Texte clair (#e0e0e0)
- Style d'entrée de formulaire personnalisé
- Style de modal et de tableau
- Contraste approprié pour l'accessibilité

### Fonctionnalités d'Image Personnalisée
- Superposition semi-transparente (50% noir) pour la lisibilité du texte
- Effet de flou pour les cartes (backdrop-filter)
- Attachement d'arrière-plan fixe
- Gestion appropriée du z-index

## Points d'Intégration

### 1. Mises à Jour de index.php
- Charger le modèle UserPreferences au démarrage de la session
- Stocker les préférences dans `$_SESSION['user_preferences']`
- Ajouter le gestionnaire de route SettingsController

### 2. Vues Mises à Jour (Application Dynamique d'Arrière-Plan)
- `index.view.php`
- `subject_detail.view.php`
- `subjects.view.php`
- `pm_inbox.view.php`
- `admin.view.php`
- `reports.view.php`

Chaque vue inclut désormais :
```php
<body class="<?= isset($_SESSION['user_preferences']) ? 'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 'bg-light' ?>"
      <?php if (isset($_SESSION['user_preferences']) && $_SESSION['user_preferences']['background_mode'] === 'custom' && !empty($_SESSION['user_preferences']['custom_background_image'])): ?>
      style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); background-size: cover; background-attachment: fixed; background-position: center;"
      <?php endif; ?>>
```

### 3. Barre de Navigation
- Bouton ⚙️ Paramètres ajouté à la barre de navigation
- Lien : `index.php?action=settings`
- Accessible à tous les utilisateurs authentifiés

## Flux Utilisateur

1. L'utilisateur clique sur "⚙️ Paramètres" dans la barre de navigation
2. Redirigé vers la page des paramètres (`index.php?action=settings`)
3. L'utilisateur choisit :
   - Thème Clair/Sombre via boutons radio → soumet → thème appliqué
   - Image personnalisée via téléchargement de fichier → traite → applique le thème
4. Préférences sauvegardées en base de données
5. Chargées automatiquement sur chaque page pour une expérience persistante

## Détails du Téléchargement d'Image
- **Emplacement** : `uploads/backgrounds/` (auto-créé)
- **Nommage** : `bg_{user_id}_{uniqid}.{extension}`
- **Taille Max** : 5MB
- **Types Autorisés** : JPG, PNG, GIF, WebP
- **Anciennes Images** : Supprimées automatiquement lors d'un nouveau téléchargement

## Fonctionnalités Spéciales
- **Création auto-par défaut** : Les nouveaux utilisateurs obtiennent automatiquement le thème clair
- **Persistant** : Paramètres sauvegardés et chargés sur toutes les pages
- **Basé sur la session** : Préférences chargées dans la session pour les performances
- **Nettoyage d'images** : Anciennes images personnalisées supprimées pour éviter l'encombrement du stockage
- **Responsive** : Fonctionne sur toutes les tailles d'écran
- **Accessible** : Ratios de contraste appropriés pour tous les thèmes

## Compatibilité Navigateur
- Navigateurs modernes supportant les variables CSS et backdrop-filter
- Dégradation gracieuse pour les anciens navigateurs (défaut au thème clair)
- Aucun JavaScript requis pour le changement de thème

## Améliorations Futures (Optionnelles)
- Thèmes de couleurs supplémentaires (Sépia, Bleu, etc.)
- Programmation de thème (clair le jour, sombre la nuit)
- Surcharge de thème par page
- Partage de thème social
- Modèles de thème communautaires
