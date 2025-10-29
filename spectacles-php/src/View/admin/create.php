<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un spectacle - Admin</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="container">
        <h1>Ajouter un spectacle</h1>
        <p class="subtitle">Créez un nouveau spectacle (Administrateurs uniquement)</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form action="/admin/spectacles/create" method="POST">
            <div class="form-group">
                <label for="title">Titre du spectacle</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            
            <div class="form-group">
                <label for="time">Heure</label>
                <input type="time" id="time" name="time" required>
            </div>
            
            <div class="form-group">
                <label for="location">Lieu</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="price">Prix (€)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="available_seats">Nombre de places</label>
                <input type="number" id="available_seats" name="available_seats" min="1" required>
            </div>
            
            <button type="submit" class="btn btn-success">Créer le spectacle</button>
            <a href="/spectacles" class="btn" style="background: #6c757d; margin-left: 10px;">Annuler</a>
        </form>
    </div>
</body>
</html>
