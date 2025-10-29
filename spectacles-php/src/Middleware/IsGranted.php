<?php
/**
 * ATTRIBUT #[IsGranted]
 * 
 * Middleware sous forme d'attribut PHP 8
 * Permet de protéger les méthodes des contrôleurs
 * 
 * Utilisation :
 * #[IsGranted('ROLE_USER')]
 * #[IsGranted('ROLE_ADMIN')]
 */

namespace App\Middleware;

use Attribute;
use App\Service\JWTService;
use App\Service\AuthService;

#[Attribute(Attribute::TARGET_METHOD)]
class IsGranted {
    private ?string $role;
    
    public function __construct(?string $role = null) {
        $this->role = $role;
    }
    
    /**
     * Vérifie si l'utilisateur a les permissions requises
     * 
     * Étapes de validation :
     * 1. Vérifie que le cookie JWT existe
     * 2. Vérifie que le JWT est valide et non expiré
     * 3. Vérifie que le JWT n'a pas été modifié (signature)
     * 4. Vérifie le rôle si spécifié
     */
    public function check(): bool {
        $jwtService = new JWTService();
        $authService = new AuthService();
        
        // Récupère le JWT depuis le cookie
        if (!isset($_COOKIE['auth_token'])) {
            return false;
        }
        
        $jwt = $_COOKIE['auth_token'];
        
        // Vérifie et décode le JWT
        $payload = $jwtService->verify($jwt);
        
        if (!$payload) {
            // JWT invalide ou expiré
            return false;
        }
        
        // Si un rôle spécifique est requis, on le vérifie
        if ($this->role !== null) {
            $userRole = $payload['role'] ?? null;
            
            // Vérifie que l'utilisateur a le bon rôle
            if ($userRole !== $this->role) {
                return false;
            }
        }
        
        // Toutes les vérifications sont passées
        return true;
    }
}
