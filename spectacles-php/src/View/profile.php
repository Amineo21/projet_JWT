<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    
    <div class="container">
        <h1>Mon Profil</h1>
        <p class="subtitle">Gérez vos informations et vos réservations</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="user-info">
            <h2 style="margin-bottom: 15px;">Informations personnelles</h2>
            <div style="color: #004085;">
                <strong>Identifiant :</strong> <?php echo htmlspecialchars($currentUser['username']); ?><br>
                <strong>Email :</strong> <?php echo htmlspecialchars($currentUser['email']); ?><br>
                <strong>Rôle :</strong> <?php echo htmlspecialchars($currentUser['role']); ?>
            </div>
        </div>
        
        <div class="tickets-list">
            <h2>Mes Réservations</h2>
            
            <?php if (empty($bookings)): ?>
                <p style="color: #666; margin-top: 20px;">
                    Vous n'avez pas encore de réservation.
                </p>
                <a href="/spectacles" class="btn" style="margin-top: 15px;">Découvrir les spectacles</a>
            <?php else: ?>
                <?php foreach ($bookings as $item): ?>
                    <?php 
                        $booking = $item['booking'];
                        $spectacle = $item['spectacle'];
                    ?>
                    <div class="ticket-card">
                        <h3><?php echo htmlspecialchars($spectacle['title']); ?></h3>
                        <div class="ticket-info">
                            <strong>Date :</strong> 
                            <?php echo date('d/m/Y', strtotime($spectacle['date'])); ?> 
                            à <?php echo htmlspecialchars($spectacle['time']); ?><br>
                            
                            <strong>Lieu :</strong> 
                            <?php echo htmlspecialchars($spectacle['location']); ?><br>
                            
                            <strong>Prix :</strong> 
                            <?php echo number_format($spectacle['price'], 2); ?> €<br>
                            
                            <strong>Réservé le :</strong> 
                            <?php echo date('d/m/Y à H:i', strtotime($booking['booked_at'])); ?>
                        </div>
                        <a href="/spectacles/<?php echo $spectacle['id']; ?>" class="btn" style="margin-top: 10px;">
                            Voir le spectacle
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
