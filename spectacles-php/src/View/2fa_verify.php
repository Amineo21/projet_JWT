<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA - Spectacles</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Vérification en deux étapes</h1>
            
            <?php if (isset($message)): ?>
                <div class="message <?= htmlspecialchars($messageType) ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($method === 'email'): ?>
                <p class="auth-subtitle">
                    Un code de vérification a été envoyé à votre adresse email :<br>
                    <strong><?= htmlspecialchars($user['email']) ?></strong>
                </p>
                <div class="info-box">
                    <strong>Mode développement :</strong> Le code n'est pas réellement envoyé par email.
                    Consultez les logs de session pour voir le code généré.
                </div>
            <?php elseif ($method === 'sms'): ?>
                <p class="auth-subtitle">
                    Un code de vérification a été envoyé par SMS à votre numéro de téléphone.
                </p>
                <div class="info-box">
                    <strong>Mode développement :</strong> Le code n'est pas réellement envoyé par SMS.
                    Consultez les logs de session pour voir le code généré.
                </div>
            <?php elseif ($method === 'totp'): ?>
                <p class="auth-subtitle">
                    Entrez le code à 6 chiffres généré par votre application d'authentification
                    (Google Authenticator, Microsoft Authenticator, etc.)
                </p>
            <?php endif; ?>
            
            <form method="POST" action="/2fa/verify" class="auth-form">
                <input type="hidden" name="method" value="<?= htmlspecialchars($method) ?>">
                
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
                    <small>Entrez le code à 6 chiffres</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Vérifier</button>
            </form>
            
            <div class="auth-links">
                <a href="/login">Annuler et retourner à la connexion</a>
            </div>
            
            <?php if ($method === 'email' || $method === 'sms'): ?>
                <div class="debug-info">
                    <h3>Informations de débogage (développement uniquement)</h3>
                    <p>Code généré : 
                        <strong>
                            <?php 
                            if ($method === 'email' && isset($_SESSION['2fa_email_code'])) {
                                echo htmlspecialchars($_SESSION['2fa_email_code']['code']);
                            } elseif ($method === 'sms' && isset($_SESSION['2fa_sms_code'])) {
                                echo htmlspecialchars($_SESSION['2fa_sms_code']['code']);
                            }
                            ?>
                        </strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
