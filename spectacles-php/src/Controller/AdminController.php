<?php
/**
 * CONTRÔLEUR ADMIN
 * 
 * Gère les fonctionnalités d'administration
 * Nécessite le rôle ROLE_ADMIN
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\SpectacleService;
use App\Service\AuthService;
use App\Middleware\IsGranted;

class AdminController {
    /**
     * Affiche le formulaire de création de spectacle
     * Nécessite le rôle ROLE_ADMIN
     */
    #[IsGranted('ROLE_ADMIN')]
    public function createForm(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/admin/create.php';
    }
    
    /**
     * Traite la création d'un spectacle
     * Nécessite le rôle ROLE_ADMIN
     */
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, array $params): void {
        $data = [
            'title' => $request->post('title'),
            'description' => $request->post('description'),
            'date' => $request->post('date'),
            'time' => $request->post('time'),
            'location' => $request->post('location'),
            'price' => $request->post('price'),
            'available_seats' => $request->post('available_seats')
        ];
        
        // Validation basique
        foreach ($data as $key => $value) {
            if (empty($value)) {
                header('Location: /admin/spectacles/create?message=Tous les champs sont requis&type=error');
                exit;
            }
        }
        
        $spectacleService = new SpectacleService();
        $spectacle = $spectacleService->create($data);
        
        header('/spectacles?message=Spectacle créé avec succès&type=success');
        exit;
    }
}
