<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration TOTP - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <main class="container">
        <h1>Configuration TOTP</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Étape 1 : Scannez le QR code</h2>
            <p>
                Ouvrez votre application d'authentification (Google Authenticator, Microsoft Authenticator, etc.)
                et scannez ce QR code :
            </p>
            
            <div class="qr-code-container">
                <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code TOTP">
            </div>
            
            <div class="alert alert-info">
                <strong>Impossible de scanner ?</strong><br>
                Entrez manuellement ce code secret dans votre application :<br>
                <code class="totp-secret"><?= htmlspecialchars($secret) ?></code>
            </div>
        </div>
        
        <div class="card">
            <h2>Étape 2 : Vérifiez le code</h2>
            <p>
                Entrez le code à 6 chiffres généré par votre application pour confirmer la configuration :
            </p>
            
            <form method="POST" action="/2fa/verify-totp" class="auth-form">
                <div class="form-group">
                    <label for="code">Code de vérification</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        required 
                        pattern="[0-9]{6}"
                        maxlength="6"
                        placeholder="000000"
                        autocomplete="one-time-code"
                        autofocus
                    >
                    <small>Le code change toutes les 30 secondes</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Vérifier et activer</button>
            </form>
        </div>
        
        <div class="actions">
            <a href="/2fa/settings" class="btn btn-secondary">Annuler</a>
        </div>
    </main>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
