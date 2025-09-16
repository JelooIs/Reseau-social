<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Connexion</h2>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" name="login" value="Se connecter">
    </form>
    <p><a href="index.php?action=register">Créer un compte</a></p>
</body>
</html>