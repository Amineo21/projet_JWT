<?php
/**
 * CONTRÔLEUR PROFIL
 * 
 * Gère le profil utilisateur et l'affichage des réservations
 * Nécessite une authentification
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\AuthService;
use App\Service\BookingService;
use App\Middleware\IsGranted;

class ProfileController {
    /**
     * Affiche le profil et les réservations de l'utilisateur
     * Nécessite d'être connecté
     */
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        // Récupère les réservations avec les détails des spectacles
        $bookingService = new BookingService();
        $bookings = $bookingService->getBookingsWithSpectacles($currentUser['id']);
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/profile.php';
    }
}
