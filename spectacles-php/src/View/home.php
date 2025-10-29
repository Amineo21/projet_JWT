<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    
    <div class="container">
        <h1>Bienvenue sur Spectacles</h1>
        <p class="subtitle">Réservez vos places pour les meilleurs spectacles</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($currentUser): ?>
            <div class="user-info">
                <strong>Connecté en tant que :</strong> <?php echo htmlspecialchars($currentUser['username']); ?>
                (<?php echo htmlspecialchars($currentUser['email']); ?>)
                - Rôle : <?php echo htmlspecialchars($currentUser['role']); ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <h2>Découvrez nos spectacles</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Parcourez notre sélection de spectacles exceptionnels : ballets, théâtre, concerts et bien plus encore.
            </p>
            <a href="/spectacles" class="btn">Voir tous les spectacles</a>
        </div>
        
        <div style="margin-top: 40px; background: #f8f9fa; padding: 20px; border-radius: 10px;">
            <h3 style="margin-bottom: 15px;">Fonctionnalités</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 8px 0; color: #555;">✓ Parcourir les spectacles disponibles</li>
                <li style="padding: 8px 0; color: #555;">✓ Réserver des places (utilisateurs connectés)</li>
                <li style="padding: 8px 0; color: #555;">✓ Gérer vos réservations</li>
                <li style="padding: 8px 0; color: #555;">✓ Ajouter des spectacles (administrateurs)</li>
            </ul>
        </div>
        
        <?php if (!$currentUser): ?>
            <div style="margin-top: 30px; background: #fff3cd; padding: 20px; border-radius: 10px; border: 1px solid #ffeaa7;">
                <h3 style="color: #856404; margin-bottom: 10px;">Comptes de test</h3>
                <p style="color: #856404; margin-bottom: 10px;">
                    <strong>Administrateur :</strong> admin / admin123<br>
                    <strong>Utilisateur :</strong> user / user123
                </p>
                <a href="/login" class="btn">Se connecter</a>
                <a href="/register" class="btn" style="background: #28a745; margin-left: 10px;">S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
