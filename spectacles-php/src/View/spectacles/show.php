<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($spectacle['title']); ?> - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="container">
        <h1><?php echo htmlspecialchars($spectacle['title']); ?></h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="spectacle-details">
            <div class="info-row">
                <div class="info-label">Date :</div>
                <div class="info-value">
                    <?php echo date('d/m/Y', strtotime($spectacle['date'])); ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Heure :</div>
                <div class="info-value"><?php echo htmlspecialchars($spectacle['time']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lieu :</div>
                <div class="info-value"><?php echo htmlspecialchars($spectacle['location']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Prix :</div>
                <div class="info-value" style="font-size: 20px; font-weight: bold; color: #28a745;">
                    <?php echo number_format($spectacle['price'], 2); ?> €
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Places disponibles :</div>
                <div class="info-value"><?php echo $spectacle['available_seats']; ?></div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <h2>Description</h2>
            <p style="color: #555; line-height: 1.6;">
                <?php echo htmlspecialchars($spectacle['description']); ?>
            </p>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <?php if ($currentUser && $spectacle['available_seats'] > 0): ?>
                <form action="/spectacles/<?php echo $spectacle['id']; ?>/book" method="POST">
                    <button type="submit" class="btn btn-success">Réserver une place</button>
                </form>
            <?php elseif (!$currentUser): ?>
                <a href="/login?message=Connectez-vous pour réserver&type=info" class="btn">
                    Connectez-vous pour réserver
                </a>
            <?php else: ?>
                <button class="btn" disabled style="background: #ccc; cursor: not-allowed;">
                    Plus de places disponibles
                </button>
            <?php endif; ?>
            
            <a href="/spectacles" class="btn" style="background: #6c757d;">Retour à la liste</a>
        </div>
    </div>
</body>
</html>
