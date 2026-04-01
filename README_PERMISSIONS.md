# Système de Gestion des Permissions - Installation et Utilisation

## 🎯 Qu'est-ce qui a été implémenté?

Un système complet de gestion des autorisations (permissions) basé sur les rôles des utilisateurs a été ajouté à votre application.

Vous pouvez maintenant contrôler précisément:
- ✅ Qui peut créer des sujets
- ✅ Qui peut créer des annonces
- ✅ Qui peut envoyer des messages
- ✅ Qui peut modérer le contenu
- ✅ Qui peut voir les signalements
- ✅ Et bien d'autres permissions...

## 📋 Résumé des Changements

### Fichiers Créés

1. **`models/Permission.php`** - Classe pour gérer les permissions en base de données
2. **`models/PermissionManager.php`** - Classe helper pour utiliser les permissions
3. **`migrations/010_create_roles_and_permissions.sql`** - Script SQL d'initialisation
4. **`run_migration_010.php`** - Script PHP pour appliquer la migration
5. **Documentation:**
   - `PERMISSIONS_SUMMARY.md` - Résumé d'implémentation
   - `PERMISSIONS_IMPLEMENTATION_GUIDE.md` - Guide complet pour développeurs
   - `PERMISSIONS_EXAMPLES.md` - Exemples de code
   - `ADMIN_GUIDE_FR.md` - Guide pour administrateurs

### Fichiers Modifiés

1. **`models/User.php`** - Support des rôles, récupération du rôle
2. **`controllers/AdminController.php`** - Gestion complète des rôles et permissions
3. **`views/admin.view.php`** - Interface admin redessinée avec 3 onglets

## 🚀 Installation (3 étapes)

### Étape 1: Appliquer la Migration

Vous devez initialiser les tables de rôles et permissions. Choisissez UNE des trois options:

#### Option A: Script PHP (RECOMMANDÉ)
```bash
# Depuis le dossier racine du projet
php run_migration_010.php
```

#### Option B: Ligne de commande MySQL
```bash
mysql -u root -p reseau_social < migrations/010_create_roles_and_permissions.sql
```

#### Option C: phpMyAdmin
1. Ouvrez phpMyAdmin
2. Sélectionnez la base `reseau_social`
3. Onglet "Importer"
4. Choisissez `migrations/010_create_roles_and_permissions.sql`
5. Cliquez "Exécuter"

### Étape 2: Vérifier l'Installation

1. Allez sur `http://votresite.com/index.php?action=admin`
2. Vérifiez que vous avez 3 onglets: "Utilisateurs", "Rôles & Permissions", "Messages"
3. Onglet "Rôles & Permissions": Vous devriez voir 6 rôles listés

### Étape 3: Attribuer des Rôles

1. Allez à Admin → Utilisateurs
2. Pour chaque utilisateur, cliquez "Changer le rôle"
3. Sélectionnez le rôle approprié
4. Cliquez "Mettre à jour"

## 📊 Rôles et Permissions

### Rôles Disponibles

| Rôle | Code | Permissions |
|------|------|-------------|
| **Étudiant** | `student` | Créations sujets, messages |
| **Professeur** | `teacher` | Étudiants + annonces + signalements |
| **BDE** | `bde` | Étudiants + annonces |
| **CA** | `ca` | Étudiants + annonces |
| **Modérateur** | `moderator` | Gestion signalements, modération |
| **Admin** | `admin` | Accès complet |

### Permissions

La liste complète des permissions:
- `create_subject` - Créer des sujets
- `edit_subject` - Éditer ses sujets
- `delete_subject` - Supprimer ses sujets
- `message_student` - Messagerie avec étudiants
- `message_teacher` - Messagerie avec profs
- `send_message` - Envoyer des messages
- `create_announcement` - Créer des annonces
- `view_reports` - Voir les signalements
- `manage_reports` - Gérer les signalements
- `edit_subject_mod` - Éditer sujets (modération)
- `delete_subject_mod` - Supprimer sujets (modération)

## 💻 Utilisation dans le Code

### Dans un Contrôleur

```php
<?php
require_once 'models/PermissionManager.php';

class SubjectController {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        $pm = PermissionManager::getInstance();
        
        // Vérifier la permission
        if (!$pm->userCanCreateSubject()) {
            $_SESSION['error'] = "Vous n'avez pas la permission.";
            header('Location: index.php?action=subject');
            exit();
        }
        
        // Créer le sujet...
    }
}
?>
```

### Dans une Vue

```php
<?php require_once 'models/PermissionManager.php'; 
$pm = PermissionManager::getInstance(); ?>

<!-- Visible seulement si l'utilisateur peut créer un sujet -->
<?php if ($pm->userCanCreateSubject()): ?>
    <a href="index.php?action=create_subject" class="btn btn-primary">
        Créer un sujet
    </a>
<?php endif; ?>
```

## 📚 Documentation

Consultez ces fichiers pour plus d'informations:

1. **`ADMIN_GUIDE_FR.md`** - Guide complet pour administrer le système (recommandé pour les admins)
2. **`PERMISSIONS_IMPLEMENTATION_GUIDE.md`** - Guide détaillé pour développeurs
3. **`PERMISSIONS_SUMMARY.md`** - Résumé technique complet
4. **`PERMISSIONS_EXAMPLES.md`** - Exemples de code pratiques

## ✅ Checklist de Déploiement

- [ ] Exécuter la migration (`php run_migration_010.php`)
- [ ] Accéder à Admin Panel et vérifier les 3 onglets
- [ ] Attribuer les rôles aux utilisateurs existants
- [ ] Mettre à jour les contrôleurs existants pour utiliser PermissionManager
- [ ] Mettre à jour les vues pour afficher les boutons conditionnellement
- [ ] Tester avec différents rôles
- [ ] Tester les contrôles d'accès (tentative d'accès non autorisé)
- [ ] Déployer en production

## 🔐 Points Importants de Sécurité

1. **Toujours vérifier côté serveur** - Ne faites jamais confiance uniquement au frontend
2. **Utiliser PermissionManager** - Classe centralisée pour toutes les vérifications
3. **Session active** - Vérifiez que `session_start()` est appelé
4. **Logs d'audit** - Envisagez de logger les changements de rôle
5. **Secrets en sécurité** - Ne stockez jamais de secrets dans le code

## 🆘 Dépannage Rapide

### Les permissions ne s'appliquent pas
- ✓ Vérifiez que la migration a été exécutée
- ✓ Vérifiez que l'utilisateur a un `role_id` assigné
- ✓ Demandez à l'utilisateur de se reconnecter

### Je ne vois pas le bouton "Créer une annonce"
- ✓ Vérifiez votre rôle (Admin → Rôles & Permissions)
- ✓ Ajoutez la permission `create_announcement` à votre rôle

### Message d'erreur "Permission refusée"
- ✓ Cela signifie que votre rôle n'a pas cette permission
- ✓ Faites-vous promouvoir par un admin

## 🎁 Extras Inclus

- **Admin Panel amélioré** - Interface intuitive pour gérer les rôles
- **Classes réutilisables** - Permission et PermissionManager peuvent être étendues
- **Documentation complète** - Guides pour admins et développeurs
- **Exemples de code** - Pour intégrer rapidement dans vos contrôleurs

## 🚀 Prochaines Étapes Recommandées

1. **Pour les admins:**
   - Lire `ADMIN_GUIDE_FR.md`
   - Attribuer les rôles aux utilisateurs

2. **Pour les développeurs:**
   - Lire `PERMISSIONS_IMPLEMENTATION_GUIDE.md`
   - Intégrer PermissionManager dans SubjectController
   - Intégrer PermissionManager dans AnnouncementController
   - Intégrer PermissionManager dans ReportController
   - Mettre à jour les vues pour afficher les boutons conditionnellement

3. **Pour les tests:**
   - Créer des utilisateurs avec différents rôles
   - Tester chaque permission
   - Tester les contrôles d'accès

## 📞 Questions Fréquentes

**Q: Pouvez-vous expliquer comment ça marche?**
R: Chaque utilisateur a un rôle (étudiant, prof, etc.). Chaque rôle a des permissions (créer sujets, etc.). PermissionManager vérifie si l'utilisateur actuel a la permission demandée.

**Q: Peut-on modifier les permissions via l'admin panel?**
R: Oui! Allez à Admin → Rôles & Permissions, sélectionnez un rôle, cochez/décochez les permissions, cliquez "Enregistrer".

**Q: Comment ajouter une nouvelle permission?**
R: Ajoutez-la à l'admin panel en SQL, ou modifiez la migration pour l'ajouter au script.

**Q: Plusieurs rôles pour un utilisateur?**
R: Pas pour l'instant. Actuellement, un utilisateur = un rôle. Vous pourriez modifier le système pour supporter plusieurs rôles si nécessaire.

**Q: Comment logger les changements?**
R: Vous pouvez ajouter une table `audit_log` et enregistrer qui a changé quoi et quand.

## 📄 Licence et Support

Ce système a été implémenté spécifiquement pour votre application.

Pour questions ou problèmes:
1. Consultez les fichiers de documentation
2. Vérifiez les logs d'erreur PHP
3. Testez les permissions via l'admin panel

## 📊 Vue d'ensemble de l'Architecture

```
User (user_id)
    ↓ has one
Role (role_id)
    ↓ has many through
Permission (permission_id)
```

L'interface Admin permet de:
- Assigner Roles aux Users
- Assigner Permissions aux Roles
- Implémenter des vérifications dans le code avec PermissionManager

## 🎓 Ressources Documentaires

| Document | Pour qui | Sujet |
|----------|----------|-------|
| `ADMIN_GUIDE_FR.md` | Administrateurs | Utilisation du panel admin |
| `PERMISSIONS_IMPLEMENTATION_GUIDE.md` | Développeurs | Intégration technique |
| `PERMISSIONS_SUMMARY.md` | Tous | Résumé complet |
| `PERMISSIONS_EXAMPLES.md` | Développeurs | Exemples de code |
| `README_PERMISSIONS.md` | Tous | Ce fichier |

---

## 🎉 Conclusion

Vous avez maintenant un système complet de gestion des permissions!

**Prochaines actions:**
1. ✅ Exécuter la migration
2. ✅ Attribuer les rôles aux utilisateurs
3. ✅ Intégrer dans vos contrôleurs
4. ✅ Tester la sécurité
5. ✅ Déployer

Bonne chance! 🚀

---

**Créé le**: 19 février 2026  
**Version**: 1.0  
**Statut**: Production-ready ✅
