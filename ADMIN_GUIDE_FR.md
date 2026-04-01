# Guide Administrateur - Gestion des Rôles et Permissions

## 📌 Vue d'Ensemble

L'interface Admin du réseau social permet de gérer:
- **Utilisateurs** - Attribution des rôles
- **Rôles et Permissions** - Configuration des droits d'accès
- **Messages** - Modération du contenu

## 🚀 Accès à l'Admin Panel

1. Connectez-vous avec un compte administrateur
2. Accédez à: `http://votresite.com/index.php?action=admin`

## 👥 Onglet Utilisateurs

### Voir les Utilisateurs

L'onglet "Utilisateurs" affiche la liste complète avec:
- **ID** - Identifiant unique de l'utilisateur
- **Pseudo** - Nom d'affichage
- **Email** - Adresse email
- **Rôle** - Rôle actuel (affiché en badge)

### Changer le Rôle d'un Utilisateur

1. Cliquez sur le bouton **"Changer le rôle"**
2. Une fenêtre modale s'ouvre
3. Sélectionnez le nouveau rôle dans la liste déroulante
4. Cliquez sur **"Mettre à jour"**
5. L'utilisateur recevra immédiatement ses nouvelles permissions

### Supprimer un Utilisateur

1. Cliquez sur le bouton **"Supprimer"** (rouge)
2. Confirmez la suppression
3. L'utilisateur et tous ses contenus seront supprimés

⚠️ **Attention**: Cette action est irréversible!

## 🔐 Onglet Rôles & Permissions

Cet onglet permet de configurer les droits d'accès pour chaque rôle.

### Sélectionner un Rôle

1. Dans la liste de gauche, cliquez sur un rôle
2. Les permissions du rôle s'affichent à droite

### Rôles Disponibles

| Rôle | Description |
|------|------------|
| **Étudiant** | Accès basique (création de sujets, messages) |
| **Professeur** | Accès étudiants + annonces + signalements |
| **BDE** | Accès étudiants + annonces |
| **CA** | Accès étudiants + annonces |
| **Modérateur** | Gestion des signalements et modération |
| **Administrateur** | Accès complet |

### Gérer les Permissions d'un Rôle

Les permissions sont groupées par catégorie:

#### 📝 Création
- **Création de sujets** - Pouvoir créer de nouveaux sujets de discussion
- **Création d'annonces** - Pouvoir créer des annonces publiques

#### ✏️ Modification
- **Modification de sujets** - Pouvoir modifier les sujets
- **Modification de sujets (modération)** - Pouvoir modifier les sujets d'autres

#### 🗑️ Suppression
- **Suppression de sujets** - Pouvoir supprimer les sujets
- **Suppression de sujets (modération)** - Pouvoir supprimer les sujets d'autres

#### 💬 Messagerie
- **Messages avec étudiants** - Pouvoir communiquer avec les étudiants
- **Messages avec profs** - Pouvoir communiquer avec les professeurs
- **Envoi de messages** - Pouvoir envoyer des messages privés

#### 👁️ Consultation
- **Voir les signalements** - Pouvoir consulter les signalements

#### ⚙️ Gestion
- **Gérer les signalements** - Pouvoir traiter les signalements

### Modifier les Permissions

1. Cochez les permissions à accorder
2. Décochez les permissions à retirer
3. Cliquez sur **"Enregistrer les permissions"**
4. Les changements s'appliqueront immédiatement à tous les utilisateurs ayant ce rôle

### Matrice Rapide des Permissions par Rôle

```
Rôle              → Droits d'accès
─────────────────────────────────────────────────
Étudiant          → Créer sujets, messages
Professeur        → Étudiant + annonces + signalements
BDE/CA            → Étudiant + annonces
Modérateur        → Gérer signalements, éditer/supprimer sujets
Administrateur    → Tous les droits
```

## 💬 Onglet Messages

### Voir les Messages

L'onglet "Messages" affiche la liste de tous les messages avec:
- **ID** - Identifiant unique
- **Utilisateur** - Auteur du message
- **Message** - Aperçu du contenu (50 premiers caractères)
- **Image** - Vignette si le message contient une image
- **Date** - Date et heure du message
- **Actions** - Bouton de suppression

### Supprimer un Message

1. Cliquez sur le bouton **"Supprimer"** (rouge)
2. Confirmez la suppression
3. Le message sera immédiatement supprimé

⚠️ **Attention**: Cette action est irréversible!

## ⏱️ Conseils et Bonnes Pratiques

### Attribution des Rôles

#### Pour un Étudiant Normal
- Rôle: **Étudiant**
- Permissions: Création de sujets, messages, envoi de messages privés

#### Pour un Professeur
- Rôle: **Professeur**
- Permissions: Tous les droits des étudiants + annonces + voir signalements

#### Pour un Membre du BDE/CA
- Rôle: **BDE** ou **CA**
- Permissions: Tous les droits des étudiants + annonces

#### Pour un Modérateur
- Rôle: **Modérateur**
- Permissions: Gestion des signalements, édition/suppression de sujets

#### Pour un Admin
- Rôle: **Administrateur**
- Permissions: Tous les droits

### Gestion du Contenu

1. **Signalez d'abord** - Utilisez le système de signalement plutôt que de supprimer directement
2. **Vérifiez au modérateur** - Laissez le modérateur traiter les signalements
3. **Documentez les suppressions** - Gardez un historique des actions

### Sécurité

⚠️ **Points importants:**
- Ne donnez jamais le rôle "Administrateur" à un utilisateur de confiance insuffisante
- Révisez régulièrement les permissions des rôles
- Supprimez rapidement les utilisateurs inappropriés
- Modérez rapidement les contenus signalés

## 🆘 Dépannage

### Problème: Les permissions ne s'appliquent pas immédiatement

**Solution:**
1. Demandez à l'utilisateur de se reconnecter
2. Effacez le cache du navigateur (Ctrl+F5)
3. Vérifiez que le rôle a été bien sauvegardé

### Problème: Je ne vois pas l'option pour créer une annonce

**Solution:**
1. Vérifiez que votre rôle a la permission "create_announcement"
2. Allez à Rôles & Permissions
3. Sélectionnez votre rôle
4. Cochez la case "Création d'annonces"
5. Cliquez "Enregistrer les permissions"
6. Reconnectez-vous

### Problème: Un utilisateur ne peut pas faire quelque chose

**Solution:**
1. Vérifiez son rôle actuel
2. Allez à Rôles & Permissions
3. Vérifiez que le rôle a les permissions nécessaires
4. Si non, ajoutez les permissions
5. Demandez à l'utilisateur de se reconnecter

## 📊 Exemple de Configuration Recommandée

### Rôle: Étudiant
**Permissions:**
- Création de sujets ✓
- Messages avec étudiants ✓
- Messages avec profs ✓
- Envoi de messages ✓

### Rôle: Professeur
**Permissions:**
- Création de sujets ✓
- Messages avec étudiants ✓
- Messages avec profs ✓
- Envoi de messages ✓
- Création d'annonces ✓
- Voir les signalements ✓

### Rôle: Modérateur
**Permissions:**
- Modification de sujets ✓
- Suppression de sujets ✓
- Voir les signalements ✓
- Gérer les signalements ✓

### Rôle: Administrateur
**Permissions:** Toutes ✓

## 🎯 Workflows Typiques

### Workflow 1: Onboarding d'un Nouvel Utilisateur

1. Un nouvel utilisateur s'inscrit
2. Vous le contactez et confirmez son rôle
3. Allez à Admin → Utilisateurs
4. Cliquez "Changer le rôle"
5. Sélectionnez son rôle
6. Cliquez "Mettre à jour"
7. L'utilisateur est automatiquement actif avec ses droits

### Workflow 2: Gestion d'un Contenu Signalé

1. Un utilisateur signale un contenu inapproprié
2. Un modérateur voit le signalement à Admin → Rapports
3. Le modérateur examine le sujet
4. Le modérateur le modifie ou le supprime
5. Le modérateur ferme le signalement

### Workflow 3: Changement de Rôle

1. Un étudiant devient prof
2. Allez à Admin → Utilisateurs
3. Cliquez "Changer le rôle"
4. Sélectionnez "Professeur"
5. Cliquez "Mettre à jour"
6. L'utilisateur a maintenant les droits de professeur

## 💡 Astuces Pratiques

1. **Utilisez le code du rôle** - Les codes (student, teacher, etc.) sont utiles pour les logs
2. **Regroupez les permissions** - Les permissions sont groupées par catégorie pour faciliter la gestion
3. **Testez avant de déployer** - Testez les permissions avec un compte de test avant que les utilisateurs réels les utilisent
4. **Documentez vos changements** - Gardez un historique de qui a changé quoi et pourquoi
5. **Faites des sauvegardesa régulières** - Sauvegardez votre base de données régulièrement

## 📞 Support et Questions

Pour plus d'informations:
- Consultez le fichier `PERMISSIONS_IMPLEMENTATION_GUIDE.md` pour les détails techniques
- Consultez le fichier `PERMISSIONS_SUMMARY.md` pour un résumé complet
- Vérifiez le code source des contrôleurs pour comprendre le flux

---

**Dernière mise à jour**: 19 février 2026
**Version**: 1.0
