<?php
/**
 * SERVICE SPECTACLES
 * 
 * Gère les spectacles (CRUD)
 * Simule une base de données avec un tableau PHP
 */

namespace App\Service;

class SpectacleService {
    // Simule une base de données de spectacles
    private static array $spectacles = [
        [
            'id' => '1',
            'title' => 'Le Lac des Cygnes',
            'description' => 'Ballet classique en 4 actes sur une musique de Tchaïkovski',
            'date' => '2025-11-15',
            'time' => '20:00',
            'location' => 'Opéra Garnier, Paris',
            'price' => 85.00,
            'available_seats' => 150
        ],
        [
            'id' => '2',
            'title' => 'Roméo et Juliette',
            'description' => 'Pièce de théâtre classique de William Shakespeare',
            'date' => '2025-11-20',
            'time' => '19:30',
            'location' => 'Comédie-Française, Paris',
            'price' => 45.00,
            'available_seats' => 200
        ],
        [
            'id' => '3',
            'title' => 'Concert Symphonique',
            'description' => 'Orchestre Philharmonique - Œuvres de Beethoven et Mozart',
            'date' => '2025-11-25',
            'time' => '20:30',
            'location' => 'Philharmonie de Paris',
            'price' => 65.00,
            'available_seats' => 300
        ]
    ];
    
    /**
     * Récupère tous les spectacles
     */
    public function getAll(): array {
        return self::$spectacles;
    }
    
    /**
     * Récupère un spectacle par son ID
     */
    public function getById(string $id): array|false {
        foreach (self::$spectacles as $spectacle) {
            if ($spectacle['id'] === $id) {
                return $spectacle;
            }
        }
        
        return false;
    }
    
    /**
     * Crée un nouveau spectacle
     */
    public function create(array $data): array {
        $newSpectacle = [
            'id' => (string)(count(self::$spectacles) + 1),
            'title' => $data['title'],
            'description' => $data['description'],
            'date' => $data['date'],
            'time' => $data['time'],
            'location' => $data['location'],
            'price' => (float)$data['price'],
            'available_seats' => (int)$data['available_seats']
        ];
        
        self::$spectacles[] = $newSpectacle;
        
        return $newSpectacle;
    }
    
    /**
     * Réduit le nombre de places disponibles
     */
    public function decrementSeats(string $id): bool {
        foreach (self::$spectacles as &$spectacle) {
            if ($spectacle['id'] === $id) {
                if ($spectacle['available_seats'] > 0) {
                    $spectacle['available_seats']--;
                    return true;
                }
                return false;
            }
        }
        
        return false;
    }
}
