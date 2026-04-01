# 🆘 Solution - Erreur "La table 'roles' n'existe pas"

## Le Problème

Vous avez reçu cette erreur:
```
PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 
La table 'reseau_social.roles' n'existe pas
```

Cela signifie que le **script de migration n'a pas été exécuté**, donc les tables de rôles et permissions n'ont pas été créées en base de données.

## ✅ La Solution (3 étapes simples)

### Étape 1: Exécuter la Migration

Ouvrez PowerShell/Terminal dans le dossier `C:\wamp64\www\ReseauSocial` et tapez:

```bash
php run_migration_010.php
```

Vous devriez voir un message comme:
```
═══════════════════════════════════════════════════════════════
    Migration 010: Initialisation des Rôles et Permissions
═══════════════════════════════════════════════════════════════

Exécution: CREATE TABLE IF NOT EXISTS `roles`...
  ✅ OK
[... plus de résultats ...]
✅ Migration réussie !
```

### Étape 2: Vérifier que c'est OK

Tapez:
```bash
php test_permissions.php
```

Vous devriez voir:
```
✅ TESTS RÉUSSIS
```

### Étape 3: Rafraîchir la Page Admin

1. Allez sur: `http://votresite.com/index.php?action=admin`
2. Rafraîchissez la page (Ctrl+F5)
3. Vous devriez maintenant voir 3 onglets: "Utilisateurs", "Rôles & Permissions", "Messages"

## 🎯 Alternative Si Ça Ne Marche Pas

Si le script PHP ne fonctionne pas, exécutez la migration manuellement via **phpMyAdmin**:

1. Ouvrez phpMyAdmin
2. Allez sur la base de données `reseau_social`
3. Onglet "Importer"
4. Choisissez le fichier: `migrations/010_create_roles_and_permissions.sql`
5. Cliquez "Exécuter"

## 🔧 Corrections Apportées

Pour éviter ce problème à l'avenir, j'ai modifié:

✅ **`models/User.php`** - Gère maintenant le cas où les tables n'existent pas
✅ **`models/PermissionManager.php`** - Gère les opérations si les tables n'existent pas
✅ **`controllers/AdminController.php`** - Affiche un message utile si pas d'initialisation
✅ **`views/admin.view.php`** - Affiche seulement ce qui est nécessaire
✅ **`run_migration_010.php`** - Script plus robuste et informatif

## 🚀 Maintenant Ça Fonctionne!

Après la migration:
1. ✅ Vous pouvez gérer les rôles et permissions via l'Admin Panel
2. ✅ Les utilisateurs peuvent être assignés à des rôles
3. ✅ Le système vérifiera automatiquement les permissions

## 📞 Si Ça Ne Marche Toujours Pas

Vérifiez:
1. Vous travaillez bien dans le bon dossier: `C:\wamp64\www\ReseauSocial`
2. Vous avez PHP installé: `php --version`
3. MySQL/WAMP est démarré
4. Vous avez un USER `root` sans mot de passe en BD (standard WAMP)

Sinon, allez dans `QUICK_START.md` pour plus d'aide.

---

**Créé**: 19 février 2026
**Status**: ✅ Résolvé
