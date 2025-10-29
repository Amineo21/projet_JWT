<?php
/**
 * SERVICE D'AUTHENTIFICATION
 * 
 * Gère l'authentification des utilisateurs
 * Simule une base de données avec un tableau PHP
 */

namespace App\Service;

class AuthService {
    // Simule une base de données d'utilisateurs
    private array $users = [
        [
            'id' => '1',
            'username' => 'admin',
            'email' => 'admin@spectacles.com',
            'password' => 'admin123', // En production : utiliser password_hash()
            'role' => 'ROLE_ADMIN'
        ],
        [
            'id' => '2',
            'username' => 'user',
            'email' => 'user@spectacles.com',
            'password' => 'user123',
            'role' => 'ROLE_USER'
        ]
    ];
    
    /**
     * Authentifie un utilisateur
     */
    public function authenticate(string $username, string $password): array|false {
        foreach ($this->users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                // Retourne les données utilisateur sans le mot de passe
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function register(string $username, string $email, string $password): array {
        $newUser = [
            'id' => (string)(count($this->users) + 1),
            'username' => $username,
            'email' => $email,
            'password' => $password, // En production : password_hash($password, PASSWORD_BCRYPT)
            'role' => 'ROLE_USER'
        ];
        
        $this->users[] = $newUser;
        
        // Retourne sans le mot de passe
        unset($newUser['password']);
        return $newUser;
    }
    
    /**
     * Récupère un utilisateur par son ID
     */
    public function getUserById(string $userId): array|false {
        foreach ($this->users as $user) {
            if ($user['id'] === $userId) {
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Récupère l'utilisateur actuellement connecté depuis le JWT
     */
    public function getCurrentUser(): array|false {
        if (!isset($_COOKIE['auth_token'])) {
            return false;
        }
        
        $jwtService = new JWTService();
        $payload = $jwtService->verify($_COOKIE['auth_token']);
        
        if (!$payload) {
            return false;
        }
        
        return [
            'id' => $payload['user_id'],
            'username' => $payload['username'],
            'email' => $payload['email'],
            'role' => $payload['role']
        ];
    }
}
