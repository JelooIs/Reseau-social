<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Inscription</h2>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="nom">Nom:</label>
        <input type="text" name="nom" id="nom" required><br>
        <label for="prenoms">Prénoms:</label>
        <input type="text" name="prenoms" id="prenoms" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" name="register" value="S'inscrire">
    </form>
</body>
</html>