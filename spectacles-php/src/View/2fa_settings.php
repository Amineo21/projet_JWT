<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration 2FA - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php 
    require __DIR__ . '/partials/navbar.php'; 
    ?>
    
    <main class="container">
        <h1>Authentification à deux facteurs (2FA)</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Sécurisez votre compte</h2>
            <p>
                L'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte.
                Après avoir saisi votre mot de passe, vous devrez entrer un code de vérification.
            </p>
            
            <?php if ($preferences && $preferences['enabled']): ?>
                <div class="alert alert-success">
                    <strong>2FA activé</strong><br>
                    Méthode actuelle : 
                    <?php 
                    switch ($preferences['method']) {
                        case 'email':
                            echo 'Email';
                            break;
                        case 'sms':
                            echo 'SMS';
                            break;
                        case 'totp':
                            echo 'Application d\'authentification (TOTP)';
                            break;
                    }
                    ?>
                </div>
                
                <form method="POST" action="/2fa/disable" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-danger">Désactiver le 2FA</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>2FA désactivé</strong><br>
                    Votre compte n'est pas protégé par l'authentification à deux facteurs.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Choisir une méthode d'authentification</h2>
            
            <div class="twofa-methods">
                <div class="twofa-method">
                    <h3>Email</h3>
                    <p>Recevez un code de vérification par email à chaque connexion.</p>
                    <form method="POST" action="/2fa/enable-email">
                        <button type="submit" class="btn btn-primary">Activer par Email</button>
                    </form>
                </div>
                
                <div class="twofa-method">
                    <h3>SMS</h3>
                    <p>Recevez un code de vérification par SMS à chaque connexion.</p>
                    <form method="POST" action="/2fa/enable-sms">
                        <button type="submit" class="btn btn-primary">Activer par SMS</button>
                    </form>
                </div>
                
                <div class="twofa-method">
                    <h3>Application d'authentification (TOTP)</h3>
                    <p>
                        Utilisez une application comme Google Authenticator, Microsoft Authenticator 
                        ou TOTP Authenticator pour générer des codes.
                    </p>
                    <a href="/2fa/setup-totp" class="btn btn-primary">Configurer TOTP</a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Recommandations</h2>
            <ul>
                <li><strong>TOTP (Application)</strong> : La méthode la plus sécurisée, fonctionne sans connexion internet</li>
                <li><strong>Email</strong> : Pratique mais moins sécurisé si votre email est compromis</li>
                <li><strong>SMS</strong> : Pratique mais vulnérable aux attaques de type SIM swapping</li>
            </ul>
        </div>
        
        <div class="actions">
            <a href="/profile" class="btn btn-secondary">Retour au profil</a>
        </div>
    </main>
    
    <!-- Removed non-existent footer.php -->
</body>
</html>
