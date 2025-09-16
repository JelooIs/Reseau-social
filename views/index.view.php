<!DOCTYPE html>
<html>
    <head>
        <title>Mini Réseau Social</title>
        <meta charset="UTF-8">
    </head>
    <body>
    <?php session_start(); ?>
    <?php if (isset($_SESSION['user'])): ?>
        <p>Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom']) ?> !
        <a href="index.php?action=logout">Déconnexion</a></p>
    <?php else: ?>
        <p><a href="index.php?action=login">Connexion</a> | <a href="index.php?action=register">Inscription</a></p>
    <?php endif; ?>
    <form method="post">
        <p>
            <label for="nom">Nom:</label>
            <input type="text" name="nom" id="nom" required="required"/> <br/>

            <label for="prenoms">Prénoms</label>
            <input type="text" name="prenoms" id="prenoms" required="required"/> <br/>
 
            <label for="messages">Message:</label>
            <textarea type="text" name="message" id="messages" rows="5"></textarea> <br/>

            <input type="submit" name="envoyer" value="Envoyer"/>
        </p>
    </form>
    <?php if (!empty($message)): ?>
        <ul>
            <?php foreach ($messages as $msg): ?>
                <li><strong><?= htmlspecialchars($msg['nom'] . ' ' . $msg['prenoms']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?> <em>(<?= $msg['created_at'] ?>)</em></li>
                <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p>Aucun message pour l'instant.</p>
        <?php endif; ?>
    </body>
</html>
