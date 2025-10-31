<?php
/**
 * CONTRÔLEUR AUTHENTIFICATION À DEUX FACTEURS
 * 
 * Gère la configuration du 2FA dans le profil utilisateur
 */

namespace App\Controller;

use App\Core\Request;
use App\Service\AuthService;
use App\Service\TwoFactorService;
use App\Middleware\IsGranted;

class TwoFactorController {
    /**
     * Affiche la page de configuration 2FA
     */
    #[IsGranted('ROLE_USER')]
    public function settings(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $twoFactorService = new TwoFactorService();
        $preferences = $twoFactorService->getUserPreference($currentUser['id']);
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/2fa_settings.php';
    }
    
    /**
     * Active le 2FA par email
     */
    #[IsGranted('ROLE_USER')]
    public function enableEmail(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $twoFactorService = new TwoFactorService();
        $twoFactorService->saveUserPreference($currentUser['id'], 'email');
        
        header('Location: /2fa/settings?message=2FA par email activé&type=success');
        exit;
    }
    
    /**
     * Active le 2FA par SMS
     */
    #[IsGranted('ROLE_USER')]
    public function enableSMS(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $twoFactorService = new TwoFactorService();
        $twoFactorService->saveUserPreference($currentUser['id'], 'sms');
        
        header('Location: /2fa/settings?message=2FA par SMS activé&type=success');
        exit;
    }
    
    /**
     * Affiche la page de configuration TOTP (QR code)
     */
    #[IsGranted('ROLE_USER')]
    public function setupTOTP(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $twoFactorService = new TwoFactorService();
        
        // Génère un nouveau secret TOTP
        $secret = $twoFactorService->generateTOTPSecret();
        
        // Stocke temporairement le secret en session
        $_SESSION['pending_totp_secret'] = $secret;
        
        // Génère l'URL du QR code
        $qrCodeUrl = $twoFactorService->getTOTPQRCodeUrl($secret, $currentUser['username']);
        
        $message = $request->get('message');
        $messageType = $request->get('type', 'info');
        
        require __DIR__ . '/../View/2fa_setup_totp.php';
    }
    
    /**
     * Vérifie et active le TOTP
     */
    #[IsGranted('ROLE_USER')]
    public function verifyTOTP(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $code = $request->post('code');
        
        if (empty($code)) {
            header('Location: /2fa/setup-totp?message=Code requis&type=error');
            exit;
        }
        
        if (!isset($_SESSION['pending_totp_secret'])) {
            header('Location: /2fa/settings?message=Session expirée&type=error');
            exit;
        }
        
        $secret = $_SESSION['pending_totp_secret'];
        $twoFactorService = new TwoFactorService();
        
        // Vérifie le code TOTP
        if (!$twoFactorService->verifyTOTPCode($secret, $code)) {
            header('Location: /2fa/setup-totp?message=Code invalide&type=error');
            exit;
        }
        
        // Code valide : active le TOTP
        $twoFactorService->saveUserPreference($currentUser['id'], 'totp', $secret);
        unset($_SESSION['pending_totp_secret']);
        
        header('Location: /2fa/settings?message=2FA par TOTP activé avec succès&type=success');
        exit;
    }
    
    /**
     * Désactive le 2FA
     */
    #[IsGranted('ROLE_USER')]
    public function disable(Request $request, array $params): void {
        $authService = new AuthService();
        $currentUser = $authService->getCurrentUser();
        
        $twoFactorService = new TwoFactorService();
        $twoFactorService->disable2FA($currentUser['id']);
        
        header('Location: /2fa/settings?message=2FA désactivé&type=success');
        exit;
    }
}
