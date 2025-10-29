<?php
/**
 * CONTRÔLEUR PAGE D'ACCUEIL
 * 
 * Gère l'affichage de la page d'accueil
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\AuthService;

class HomeController {
    /**
     * Affiche la page d'accueil
     * Page publique avec message de bienvenue
     */
    public function index(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/home.php';
    }
}
