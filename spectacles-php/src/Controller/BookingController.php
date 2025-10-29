<?php
/**
 * CONTRÔLEUR RÉSERVATIONS
 * 
 * Gère les réservations de billets
 * Nécessite une authentification
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\BookingService;
use App\Service\SpectacleService;
use App\Service\AuthService;
use App\Middleware\IsGranted;

class BookingController {
    /**
     * Réserve une place pour un spectacle
     * Nécessite d'être connecté (ROLE_USER minimum)
     */
    #[IsGranted('ROLE_USER')]
    public function book(Request $request, array $params): void {
        $spectacleId = $params['id'];
        
        // Vérifie que le spectacle existe et a des places disponibles
        $spectacleService = new SpectacleService();
        $spectacle = $spectacleService->getById($spectacleId);
        
        if (!$spectacle) {
            header('Location: /spectacles?message=Spectacle introuvable&type=error');
            exit;
        }
        
        if ($spectacle['available_seats'] <= 0) {
            header("Location: /spectacles/{$spectacleId}?message=Plus de places disponibles&type=error");
            exit;
        }
        
        // Récupère l'utilisateur connecté
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        // Crée la réservation
        $bookingService = new BookingService();
        $booking = $bookingService->create($currentUser['id'], $spectacleId);
        
        // Décrémente le nombre de places disponibles
        $spectacleService->decrementSeats($spectacleId);
        
        header("Location: /profile?message=Réservation confirmée&type=success");
        exit;
    }
}
