<!DOCTYPE html>
<html>
    <head>
        <title>Mini Réseau Social</title>
        <meta charset="UTF-8">
    </head>
    <body>
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
