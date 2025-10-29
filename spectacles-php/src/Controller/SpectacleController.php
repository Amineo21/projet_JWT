<?php
/**
 * CONTRÔLEUR SPECTACLES
 * 
 * Gère l'affichage des spectacles (pages publiques)
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\SpectacleService;
use App\Service\AuthService;

class SpectacleController {
    /**
     * Liste tous les spectacles
     * Page publique
     */
    public function list(Request $request, array $params): void {
        $spectacleService = new SpectacleService();
        $spectacles = $spectacleService->getAll();
        
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        require __DIR__ . '/../View/spectacles/list.php';
    }
    
    /**
     * Affiche le détail d'un spectacle
     * Page publique
     */
    public function show(Request $request, array $params): void {
        $spectacleService = new SpectacleService();
        $spectacle = $spectacleService->getById($params['id']);
        
        if (!$spectacle) {
            header('Location: /spectacles?message=Spectacle introuvable&type=error');
            exit;
        }
        
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/spectacles/show.php';
    }
}
