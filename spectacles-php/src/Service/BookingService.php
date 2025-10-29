<?php
/**
 * SERVICE RÉSERVATIONS
 * 
 * Gère les réservations de billets
 * Simule une base de données avec un tableau PHP
 */

namespace App\Service;

class BookingService {
    // Simule une base de données de réservations
    private static array $bookings = [];
    
    /**
     * Crée une réservation
     */
    public function create(string $userId, string $spectacleId, int $quantity = 1): array {
        $booking = [
            'id' => (string)(count(self::$bookings) + 1),
            'user_id' => $userId,
            'spectacle_id' => $spectacleId,
            'quantity' => $quantity,
            'booked_at' => date('Y-m-d H:i:s')
        ];
        
        self::$bookings[] = $booking;
        
        return $booking;
    }
    
    /**
     * Récupère toutes les réservations d'un utilisateur
     */
    public function getByUserId(string $userId): array {
        $userBookings = [];
        
        foreach (self::$bookings as $booking) {
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
