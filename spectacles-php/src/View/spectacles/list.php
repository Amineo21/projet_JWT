<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spectacles - Liste</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="container">
        <h1>Nos Spectacles</h1>
        <p class="subtitle">Découvrez notre sélection de spectacles exceptionnels</p>
        
        <div class="spectacles-grid">
            <?php foreach ($spectacles as $spectacle): ?>
                <div class="spectacle-card">
                    <h3><?php echo htmlspecialchars($spectacle['title']); ?></h3>
                    <div class="date">
                        <?php echo date('d/m/Y', strtotime($spectacle['date'])); ?> 
                        à <?php echo htmlspecialchars($spectacle['time']); ?>
                    </div>
                    <div class="price"><?php echo number_format($spectacle['price'], 2); ?> €</div>
                    <div class="description">
                        <?php echo htmlspecialchars(substr($spectacle['description'], 0, 100)); ?>...
                    </div>
                    <div style="color: #666; font-size: 14px; margin-bottom: 15px;">
                        <?php echo $spectacle['available_seats']; ?> places disponibles
                    </div>
                    <a href="/spectacles/<?php echo $spectacle['id']; ?>" class="btn">Voir les détails</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
