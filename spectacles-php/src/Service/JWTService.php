<?php
/**
 * SERVICE JWT
 * 
 * Gère la création et la vérification des JSON Web Tokens
 * Supporte les access tokens (courte durée) et refresh tokens (longue durée)
 */

namespace App\Service;

class JWTService {
    // Clé secrète pour signer les jetons (À CHANGER EN PRODUCTION)
    private const SECRET_KEY = 'votre_cle_secrete_super_complexe_123456';
    
    // Durée de vie de l'access token (15 minutes)
    private const ACCESS_TOKEN_EXPIRATION = 900; // 15 minutes
    
    // Durée de vie du refresh token (7 jours)
    private const REFRESH_TOKEN_EXPIRATION = 604800; // 7 jours
    
    /**
     * Crée un access token (JWT standard)
     */
    public function createAccessToken(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + self::ACCESS_TOKEN_EXPIRATION;
        $payload['type'] = 'access';
        
        return $this->createToken($payload);
    }
    
    /**
     * Crée un refresh token (pour renouveler l'access token)
     */
    public function createRefreshToken(string $userId): string {
        $payload = [
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + self::REFRESH_TOKEN_EXPIRATION,
            'type' => 'refresh'
        ];
        
        return $this->createToken($payload);
    }
    
    /**
     * Crée un JWT
     */
    private function createToken(array $payload): string {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            self::SECRET_KEY,
            true
        );
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Vérifie et décode un JWT
     * 
     * Retourne le payload si valide, false sinon
     */
    public function verify(string $jwt): array|false {
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Vérifie la signature
        $signature = $this->base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            self::SECRET_KEY,
            true
        );
        
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }
        
        // Décode le payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        
        // Vérifie l'expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Stocke le JWT dans un cookie sécurisé
     */
    public function setAccessTokenCookie(string $jwt): void {
        setcookie(
            'auth_token',
            $jwt,
            [
                'expires' => time() + self::ACCESS_TOKEN_EXPIRATION,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
    
    /**
     * Supprime le cookie d'authentification
     */
    public function deleteAccessTokenCookie(): void {
        setcookie(
            'auth_token',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
    
    private function base64UrlEncode($data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private function base64UrlDecode($data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
