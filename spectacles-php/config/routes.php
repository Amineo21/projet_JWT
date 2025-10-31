<?php
/**
 * DÉFINITION DES ROUTES
 * 
 * Ce fichier enregistre toutes les routes de l'application
 * Format : $router->add(méthode, chemin, contrôleur, méthode)
 */

use App\Core\Router;

function registerRoutes(Router $router) {
    // Routes publiques
    $router->add('GET', '/', 'HomeController', 'index');
    $router->add('GET', '/login', 'AuthController', 'loginForm');
    $router->add('POST', '/login', 'AuthController', 'login');
    $router->add('GET', '/register', 'AuthController', 'registerForm');
    $router->add('POST', '/register', 'AuthController', 'register');
    $router->add('GET', '/logout', 'AuthController', 'logout');
    
    $router->add('GET', '/2fa/verify', 'AuthController', 'verify2FAForm');
    $router->add('POST', '/2fa/verify', 'AuthController', 'verify2FA');
    
    // Routes spectacles (publiques)
    $router->add('GET', '/spectacles', 'SpectacleController', 'list');
    $router->add('GET', '/spectacles/{id}', 'SpectacleController', 'show');
    
    // Routes réservation (utilisateurs authentifiés)
    $router->add('POST', '/spectacles/{id}/book', 'BookingController', 'book');
    
    // Routes profil (utilisateurs authentifiés)
    $router->add('GET', '/profile', 'ProfileController', 'index');
    
    $router->add('GET', '/2fa/settings', 'TwoFactorController', 'settings');
    $router->add('POST', '/2fa/enable-email', 'TwoFactorController', 'enableEmail');
    $router->add('POST', '/2fa/enable-sms', 'TwoFactorController', 'enableSMS');
    $router->add('GET', '/2fa/setup-totp', 'TwoFactorController', 'setupTOTP');
    $router->add('POST', '/2fa/verify-totp', 'TwoFactorController', 'verifyTOTP');
    $router->add('POST', '/2fa/disable', 'TwoFactorController', 'disable');
    
    // Routes admin (administrateurs uniquement)
    $router->add('GET', '/admin/spectacles/create', 'AdminController', 'createForm');
    $router->add('POST', '/admin/spectacles/create', 'AdminController', 'create');
    
    // Route refresh token
    $router->add('POST', '/refresh-token', 'AuthController', 'refreshToken');
}
