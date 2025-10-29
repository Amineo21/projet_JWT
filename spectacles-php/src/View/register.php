<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: 50px auto;">
        <h1>Inscription</h1>
        <p class="subtitle">Créez votre compte pour réserver des places</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form action="/register" method="POST">
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">S'inscrire</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <p style="color: #666;">Déjà un compte ?</p>
            <a href="/login" class="btn">Se connecter</a>
        </div>
    </div>
</body>
</html>
