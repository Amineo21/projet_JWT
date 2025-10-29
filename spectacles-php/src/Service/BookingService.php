<?php
/**
 * SERVICE RÉSERVATIONS
 * 
 * Gère les réservations de billets
 * Utilise les sessions PHP pour persister les données entre les requêtes
 */

namespace App\Service;

class BookingService {
    /**
     * Constructeur - Initialise les réservations en session si nécessaire
     */
    public function __construct() {
        if (!isset($_SESSION['bookings'])) {
            $_SESSION['bookings'] = [];
        }
    }
    
    /**
     * Crée une réservation
     */
    public function create(string $userId, string $spectacleId, int $quantity = 1): array {
        $booking = [
            'id' => (string)(count($_SESSION['bookings']) + 1),
            'user_id' => $userId,
            'spectacle_id' => $spectacleId,
            'quantity' => $quantity,
            'booked_at' => date('Y-m-d H:i:s')
        ];
        
        $_SESSION['bookings'][] = $booking;
        
        return $booking;
    }
    
    /**
     * Récupère toutes les réservations d'un utilisateur
     */
    public function getByUserId(string $userId): array {
        $userBookings = [];
        
        foreach ($_SESSION['bookings'] as $booking) {
            if ($booking['user_id'] === $userId) {
                $userBookings[] = $booking;
            }
        }
        
        return $userBookings;
    }
    
    /**
     * Récupère les réservations avec les détails des spectacles
     */
    public function getBookingsWithSpectacles(string $userId): array {
        $spectacleService = new SpectacleService();
        $bookings = $this->getByUserId($userId);
        $result = [];
        
        foreach ($bookings as $booking) {
            $spectacle = $spectacleService->getById($booking['spectacle_id']);
            
            if ($spectacle) {
                $result[] = [
                    'booking' => $booking,
                    'spectacle' => $spectacle
                ];
            }
        }
        
        return $result;
    }
}
