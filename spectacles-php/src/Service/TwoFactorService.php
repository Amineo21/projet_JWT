<?php
/**
 * SERVICE D'AUTHENTIFICATION À DEUX FACTEURS (2FA)
 * 
 * Gère la génération et validation des codes 2FA :
 * - Email : code à 6 chiffres envoyé par email (simulé)
 * - SMS : code à 6 chiffres envoyé par SMS (simulé)
 * - TOTP : Time-based One-Time Password (Google Authenticator, etc.)
 */

namespace App\Service;

class TwoFactorService {
    /**
     * Génère un code aléatoire à 6 chiffres
     * Utilisé pour Email et SMS
     */
    public function generateCode(): string {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Envoie un code par email (simulé)
     * En production : utiliser une vraie API d'envoi d'emails (SendGrid, Mailgun, etc.)
     */
    public function sendEmailCode(string $email, string $code): bool {
        // Stocke le code en session avec un timestamp
        $_SESSION['2fa_email_code'] = [
            'code' => $code,
            'email' => $email,
            'expires_at' => time() + 300 // Expire après 5 minutes
        ];
        
        // En production : envoyer un vrai email
        // mail($email, "Code de vérification", "Votre code : $code");
        
        return true;
    }
    
    /**
     * Envoie un code par SMS (simulé)
     * En production : utiliser une API SMS (Twilio, Vonage, etc.)
     */
    public function sendSMSCode(string $phone, string $code): bool {
        // Stocke le code en session avec un timestamp
        $_SESSION['2fa_sms_code'] = [
            'code' => $code,
            'phone' => $phone,
            'expires_at' => time() + 300 // Expire après 5 minutes
        ];
        
        // En production : envoyer un vrai SMS
        // $twilio->messages->create($phone, ['from' => '+1234567890', 'body' => "Code: $code"]);
        
        return true;
    }
    
    /**
     * Vérifie un code email
     */
    public function verifyEmailCode(string $code): bool {
        if (!isset($_SESSION['2fa_email_code'])) {
            return false;
        }
        
        $stored = $_SESSION['2fa_email_code'];
        
        // Vérifie l'expiration
        if (time() > $stored['expires_at']) {
            unset($_SESSION['2fa_email_code']);
            return false;
        }
        
        // Vérifie le code
        if ($code === $stored['code']) {
            unset($_SESSION['2fa_email_code']);
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie un code SMS
     */
    public function verifySMSCode(string $code): bool {
        if (!isset($_SESSION['2fa_sms_code'])) {
            return false;
        }
        
        $stored = $_SESSION['2fa_sms_code'];
        
        // Vérifie l'expiration
        if (time() > $stored['expires_at']) {
            unset($_SESSION['2fa_sms_code']);
            return false;
        }
        
        // Vérifie le code
        if ($code === $stored['code']) {
            unset($_SESSION['2fa_sms_code']);
            return true;
        }
        
        return false;
    }
    
    /**
     * Génère un secret TOTP aléatoire (base32)
     * Utilisé pour configurer Google Authenticator
     */
    public function generateTOTPSecret(): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32
        $secret = '';
        
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $secret;
    }
    
    /**
     * Génère l'URL pour le QR code TOTP
     * Compatible avec Google Authenticator, Microsoft Authenticator, etc.
     */
    public function getTOTPQRCodeUrl(string $secret, string $username, string $issuer = 'Spectacles App'): string {
        $label = urlencode($issuer . ':' . $username);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30
        ]);
        
        $otpauthUrl = "otpauth://totp/{$label}?{$params}";
        
        // Utilise l'API Google Charts pour générer le QR code
        return 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($otpauthUrl);
    }
    
    /**
     * Vérifie un code TOTP (Time-based One-Time Password)
     * Implémentation de l'algorithme RFC 6238
     */
    public function verifyTOTPCode(string $secret, string $code): bool {
        // Décode le secret base32
        $key = $this->base32Decode($secret);
        
        // Calcule le timestamp actuel (30 secondes par période)
        $timestamp = floor(time() / 30);
        
        // Vérifie le code actuel et les codes adjacents (pour compenser le décalage)
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->generateTOTPCode($key, $timestamp + $i);
            
            if ($code === $calculatedCode) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Génère un code TOTP pour un timestamp donné
     * Algorithme HOTP (RFC 4226) avec timestamp
     */
    private function generateTOTPCode(string $key, int $timestamp): string {
        // Convertit le timestamp en binaire (8 octets, big-endian)
        $time = pack('N*', 0) . pack('N*', $timestamp);
        
        // Calcule le HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $key, true);
        
        // Extraction dynamique (Dynamic Truncation)
        $offset = ord($hash[19]) & 0x0f;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );
        
        // Retourne les 6 derniers chiffres
        return str_pad((string)($code % 1000000), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Décode une chaîne base32 en binaire
     */
    private function base32Decode(string $secret): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $decoded = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];
            $value = strpos($chars, $char);
            
            if ($value === false) {
                continue;
            }
            
            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;
            
            if ($bitsLeft >= 8) {
                $decoded .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }
        
        return $decoded;
    }
    
    /**
     * Sauvegarde la méthode 2FA préférée de l'utilisateur
     */
    public function saveUserPreference(string $userId, string $method, ?string $totpSecret = null): void {
        if (!isset($_SESSION['user_2fa_preferences'])) {
            $_SESSION['user_2fa_preferences'] = [];
        }
        
        $_SESSION['user_2fa_preferences'][$userId] = [
            'method' => $method, // 'email', 'sms', 'totp'
            'totp_secret' => $totpSecret,
            'enabled' => true
        ];
    }
    
    /**
     * Récupère les préférences 2FA d'un utilisateur
     */
    public function getUserPreference(string $userId): ?array {
        if (!isset($_SESSION['user_2fa_preferences'][$userId])) {
            return null;
        }
        
        return $_SESSION['user_2fa_preferences'][$userId];
    }
    
    /**
     * Désactive le 2FA pour un utilisateur
     */
    public function disable2FA(string $userId): void {
        if (isset($_SESSION['user_2fa_preferences'][$userId])) {
            $_SESSION['user_2fa_preferences'][$userId]['enabled'] = false;
        }
    }
}
