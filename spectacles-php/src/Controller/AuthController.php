<?php
/**
 * CONTRÔLEUR AUTHENTIFICATION
 * 
 * Gère la connexion, l'inscription et la déconnexion
 * Gère également le refresh token et l'authentification à deux facteurs (2FA)
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\AuthService;
use App\Service\JWTService;
use App\Service\TwoFactorService;

class AuthController {
    /**
     * Affiche le formulaire de connexion
     */
    public function loginForm(Request $request, array $params): void {
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/login.php';
    }
    
    /**
     * Traite la connexion (première étape)
     * Modifié pour gérer le flux 2FA
     */
    public function login(Request $request, array $params): void {
        $username = $request->post('username');
        $password = $request->post('password');
        
        if (empty($username) || empty($password)) {
            header('Location: /login?message=Tous les champs sont requis&type=error');
            exit;
        }
        
        $authService = new AuthService();
        $user = $authService->authenticate($username, $password);
        
        if (!$user) {
            header('Location: /login?message=Identifiants incorrects&type=error');
            exit;
        }
        
        $twoFactorService = new TwoFactorService();
        $twoFactorPrefs = $twoFactorService->getUserPreference($user['id']);
        
        if ($twoFactorPrefs && $twoFactorPrefs['enabled']) {
            // 2FA activé : stocke l'utilisateur en session temporaire et redirige vers la vérification
            $_SESSION['pending_2fa_user'] = $user;
            
            // Envoie le code selon la méthode choisie
            if ($twoFactorPrefs['method'] === 'email') {
                $code = $twoFactorService->generateCode();
                $twoFactorService->sendEmailCode($user['email'], $code);
                header('Location: /2fa/verify?method=email&message=Code envoyé par email&type=info');
            } elseif ($twoFactorPrefs['method'] === 'sms') {
                $code = $twoFactorService->generateCode();
                // En production : récupérer le numéro de téléphone de l'utilisateur
                $twoFactorService->sendSMSCode('0612345678', $code);
                header('Location: /2fa/verify?method=sms&message=Code envoyé par SMS&type=info');
            } elseif ($twoFactorPrefs['method'] === 'totp') {
                header('Location: /2fa/verify?method=totp');
            }
            exit;
        }
        
        $this->completeLogin($user);
    }
    
    /**
     * Affiche la page de vérification 2FA
     * Nouvelle méthode pour la vérification 2FA
     */
    public function verify2FAForm(Request $request, array $params): void {
        // Vérifie qu'il y a bien une authentification en attente
        if (!isset($_SESSION['pending_2fa_user'])) {
            header('Location: /login?message=Session expirée&type=error');
            exit;
        }
        
        $method = $request->get('method', 'email');
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        $user = $_SESSION['pending_2fa_user'];
        
        require __DIR__ . '/../View/2fa_verify.php';
    }
    
    /**
     * Traite la vérification du code 2FA
     * Nouvelle méthode pour valider le code 2FA
     */
    public function verify2FA(Request $request, array $params): void {
        // Vérifie qu'il y a bien une authentification en attente
        if (!isset($_SESSION['pending_2fa_user'])) {
            header('Location: /login?message=Session expirée&type=error');
            exit;
        }
        
        $code = $request->post('code');
        $method = $request->post('method');
        
        if (empty($code)) {
            header("Location: /2fa/verify?method={$method}&message=Code requis&type=error");
            exit;
        }
        
        $twoFactorService = new TwoFactorService();
        $user = $_SESSION['pending_2fa_user'];
        $isValid = false;
        
        // Vérifie le code selon la méthode
        if ($method === 'email') {
            $isValid = $twoFactorService->verifyEmailCode($code);
        } elseif ($method === 'sms') {
            $isValid = $twoFactorService->verifySMSCode($code);
        } elseif ($method === 'totp') {
            $prefs = $twoFactorService->getUserPreference($user['id']);
            $isValid = $twoFactorService->verifyTOTPCode($prefs['totp_secret'], $code);
        }
        
        if (!$isValid) {
            header("Location: /2fa/verify?method={$method}&message=Code invalide ou expiré&type=error");
            exit;
        }
        
        // Code valide : finalise la connexion
        unset($_SESSION['pending_2fa_user']);
        $this->completeLogin($user);
    }
    
    /**
     * Finalise la connexion en créant les tokens JWT
     * Nouvelle méthode pour éviter la duplication de code
     */
    private function completeLogin(array $user): void {
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Crée l'access token (JWT)
        $jwtService = new JWTService();
        $accessToken = $jwtService->createAccessToken($payload);
        
        // Crée le refresh token
        $refreshToken = $jwtService->createRefreshToken($user['id']);
        
        // Stocke l'access token dans un cookie
        $jwtService->setAccessTokenCookie($accessToken);
        
        // Stocke le refresh token en session (plus sécurisé que cookie)
        $_SESSION['refresh_token'] = $refreshToken;
        
        header('Location: /?message=Connexion réussie&type=success');
        exit;
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function registerForm(Request $request, array $params): void {
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/register.php';
    }
    
    /**
     * Traite l'inscription
     */
    public function register(Request $request, array $params): void {
        $username = $request->post('username');
        $email = $request->post('email');
        $password = $request->post('password');
        
        if (empty($username) || empty($email) || empty($password)) {
            header('Location: /register?message=Tous les champs sont requis&type=error');
            exit;
        }
        
        $authService = new AuthService();
        $user = $authService->register($username, $email, $password);
        
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Connecte automatiquement l'utilisateur
        $jwtService = new JWTService();
        $accessToken = $jwtService->createAccessToken($payload);
        $refreshToken = $jwtService->createRefreshToken($user['id']);
        
        $jwtService->setAccessTokenCookie($accessToken);
        $_SESSION['refresh_token'] = $refreshToken;
        
        header('Location: /?message=Inscription réussie&type=success');
        exit;
    }
    
    /**
     * Déconnexion
     */
    public function logout(Request $request, array $params): void {
        $jwtService = new JWTService();
        $jwtService->deleteAccessTokenCookie();
        
        // Supprime le refresh token de la session
        unset($_SESSION['refresh_token']);
        
        header('Location: /?message=Déconnexion réussie&type=success');
        exit;
    }
    
    /**
     * Renouvelle l'access token avec le refresh token
     * Permet de prolonger la session sans redemander les identifiants
     */
    public function refreshToken(Request $request, array $params): void {
        if (!isset($_SESSION['refresh_token'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Refresh token manquant']);
            exit;
        }
        
        $jwtService = new JWTService();
        $refreshToken = $_SESSION['refresh_token'];
        
        // Vérifie le refresh token
        $payload = $jwtService->verify($refreshToken);
        
        if (!$payload || $payload['type'] !== 'refresh') {
            http_response_code(401);
            echo json_encode(['error' => 'Refresh token invalide']);
            exit;
        }
        
        // Récupère les données utilisateur
        $authService = new AuthService();
        $user = $authService->getUserById($payload['user_id']);
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Utilisateur introuvable']);
            exit;
        }
        
        $userPayload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Crée un nouvel access token
        $newAccessToken = $jwtService->createAccessToken($userPayload);
        $jwtService->setAccessTokenCookie($newAccessToken);
        
        echo json_encode(['success' => true]);
    }
}
