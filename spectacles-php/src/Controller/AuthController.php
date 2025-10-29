<?php
/**
 * CONTRÔLEUR AUTHENTIFICATION
 * 
 * Gère la connexion, l'inscription et la déconnexion
 * Gère également le refresh token
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\AuthService;
use App\Service\JWTService;

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
     * Traite la connexion
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
        
        // Crée l'access token (JWT)
        $jwtService = new JWTService();
        $accessToken = $jwtService->createAccessToken($user);
        
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
        
        // Connecte automatiquement l'utilisateur
        $jwtService = new JWTService();
        $accessToken = $jwtService->createAccessToken($user);
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
        
        // Crée un nouvel access token
        $newAccessToken = $jwtService->createAccessToken($user);
        $jwtService->setAccessTokenCookie($newAccessToken);
        
        echo json_encode(['success' => true]);
    }
}
