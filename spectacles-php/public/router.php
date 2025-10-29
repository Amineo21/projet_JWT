<?php
/**
 * ROUTEUR POUR LE SERVEUR PHP INTÉGRÉ
 * 
 * Le serveur PHP intégré (php -S) ne lit pas le .htaccess
 * Ce fichier redirige toutes les requêtes vers index.php
 * sauf pour les fichiers statiques (CSS, images, etc.)
 */

// Si le fichier demandé existe et n'est pas un dossier, on le sert directement
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    
    // Si c'est un fichier statique qui existe (CSS, JS, images, etc.)
    if (is_file($file)) {
        return false; // Le serveur PHP sert le fichier directement
    }
}

// Sinon, on passe par index.php pour le routing
require_once __DIR__ . '/index.php';
