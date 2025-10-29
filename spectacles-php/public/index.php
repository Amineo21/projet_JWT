<?php
/**
 * POINT D'ENTRÉE DE L'APPLICATION
 * 
 * Ce fichier est le seul accessible publiquement.
 * Toutes les requêtes passent par ici grâce au fichier .htaccess
 */

// Active l'affichage des erreurs en développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarre la session PHP pour stocker les refresh tokens
session_start();

// Charge l'autoloader pour charger automatiquement les classes
require_once __DIR__ . '/../vendor/autoload.php';

// Charge les routes de l'application
require_once __DIR__ . '/../config/routes.php';

use App\Core\Router;
use App\Core\Request;

// Crée une instance du routeur
$router = new Router();

// Enregistre toutes les routes définies dans config/routes.php
registerRoutes($router);

// Récupère la requête HTTP actuelle
$request = Request::createFromGlobals();

try {
    // Dispatch la requête vers le bon contrôleur
    $router->dispatch($request);
} catch (Exception $e) {
    // Gestion des erreurs globales
    http_response_code(500);
    echo "Erreur : " . $e->getMessage();
}
