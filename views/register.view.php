<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Inscription</h2>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="pseudo">Pseudo:</label>
        <input type="text" name="pseudo" id="pseudo" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" name="register" value="S'inscrire">
    </form>
</body>
</html>