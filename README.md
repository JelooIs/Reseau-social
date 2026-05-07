# Système de Gestion des Permissions - Installation et Utilisation

## Installation Rapide (WAMP)

### Prérequis
- ✅ WAMP installé et démarré
- ✅ Le projet situé dans `c:\wamp64\www\ReseauSocial`
- ✅ MySQL en cours d'exécution (service WAMP)
- ✅ Base de données `reseau_social` créée

### Étape 1: Appliquer la Migration (Option A - RECOMMANDÉ pour WAMP)

Ouvrez un terminal PowerShell et naviguez vers votre projet:

```powershell
cd c:\wamp64\www\ReseauSocial
php run_migration_010.php
```

✅ Vous devriez voir un message de succès avec un résumé des rôles créés.

**Alternative - Option B: Ligne de commande MySQL**
```bash
mysql -u root -p reseau_social < migrations/010_create_roles_and_permissions.sql
```

**Alternative - Option C: phpMyAdmin (Interface Graphique)**
1. Ouvrez `http://localhost/phpmyadmin/`
2. Sélectionnez la base `reseau_social`
3. Onglet "Importer"
4. Choisissez `migrations/010_create_roles_and_permissions.sql`
5. Cliquez "Exécuter"

### Étape 2: Vérifier l'Installation

Deux façons de vérifier que tout fonctionne:

**Méthode 1: Vérification automatique (recommandée)**
```powershell
php test_permissions.php
```
✅ La commande affichera "TESTS RÉUSSIS" et un résumé des permissions.

**Méthode 2: Via le navigateur**
1. Connectez-vous à votre application: `http://localhost/ReseauSocial/`
2. Allez sur: `http://localhost/ReseauSocial/index.php?action=admin`
3. Vérifiez que vous avez 3 onglets: "Utilisateurs", "Rôles & Permissions", "Messages"
4. Onglet "Rôles & Permissions": Vous devriez voir 6 rôles listés

### Étape 3: Attribuer des Rôles aux Utilisateurs

1. Allez à `http://localhost/ReseauSocial/index.php?action=admin` → Onglet "Utilisateurs"
2. Pour chaque utilisateur, cliquez "Changer le rôle"
3. Sélectionnez le rôle approprié (voir tableau ci-dessous)
4. Cliquez "Mettre à jour"

## Rôles et Permissions

### Rôles Disponibles

| Rôle | Code | Permissions Principales |
|------|------|-------------|
| **Étudiant** | `student` | Créer sujets, envoyer messages |
| **Professeur** | `teacher` | Étudiants + créer annonces + voir signalements |
| **BDE** | `bde` | Étudiants + créer annonces |
| **CA** | `ca` | Étudiants + créer annonces |
| **Modérateur** | `moderator` | Gérer signalements, modérer sujets |
| **Admin** | `admin` | Accès complet |

### Permissions Détaillées

La liste complète des permissions disponibles:
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

## Fichiers Créés/Modifiés

### Fichiers Créés
- `models/Permission.php` - Classe de gestion des permissions en base de données
- `models/PermissionManager.php` - Helper singleton pour utiliser les permissions
- `migrations/010_create_roles_and_permissions.sql` - Script SQL d'initialisation
- `run_migration_010.php` - Script PHP pour appliquer la migration
- `test_permissions.php` - Script de test des permissions
- **Documentation:**
  - `PERMISSIONS_SUMMARY.md` - Résumé technique complet
  - `PERMISSIONS_IMPLEMENTATION_GUIDE.md` - Guide complet pour développeurs
  - `PERMISSIONS_EXAMPLES.md` - Exemples de code pratiques
  - `ADMIN_GUIDE_FR.md` - Guide pour administrateurs

### Fichiers Modifiés
- `models/User.php` - Support des rôles, récupération du rôle utilisateur
- `controllers/AdminController.php` - Gestion complète des rôles et permissions
- `views/admin.view.php` - Interface admin redessinée avec 3 onglet

## Documentation

Consultez ces fichiers pour plus d'informations:

1. **`ADMIN_GUIDE_FR.md`** - Guide complet pour administrer le système (recommandé pour les admins)
2. **`PERMISSIONS_IMPLEMENTATION_GUIDE.md`** - Guide détaillé pour développeurs
3. **`PERMISSIONS_SUMMARY.md`** - Résumé technique complet
4. **`PERMISSIONS_EXAMPLES.md`** - Exemples de code pratiques

## Checklist de Déploiement

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

## Dépannage Rapide

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

## Extras Inclus

- **Admin Panel amélioré** - Interface intuitive pour gérer les rôles
- **Classes réutilisables** - Permission et PermissionManager peuvent être étendues
- **Documentation complète** - Guides pour admins et développeurs
- **Exemples de code** - Pour intégrer rapidement dans vos contrôleurs

## Prochaines Étapes Recommandées

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
