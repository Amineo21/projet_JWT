<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: 50px auto;">
        <h1>Connexion</h1>
        <p class="subtitle">Connectez-vous pour réserver des places</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form action="/login" method="POST">
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Se connecter</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <p style="color: #666;">Pas encore de compte ?</p>
            <a href="/register" class="btn" style="background: #28a745;">S'inscrire</a>
        </div>
        
        <div style="margin-top: 20px; background: #fff3cd; padding: 15px; border-radius: 5px;">
            <strong style="color: #856404;">Comptes de test :</strong><br>
            <code>admin / admin123</code> (Administrateur)<br>
            <code>user / user123</code> (Utilisateur)
        </div>
    </div>
</body>
</html>
