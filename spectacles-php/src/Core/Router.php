<?php
/**
 * ROUTEUR DE L'APPLICATION
 * 
 * Gère le routing et le dispatch des requêtes vers les contrôleurs
 * Supporte les paramètres dynamiques dans les URLs ({id})
 */

namespace App\Core;

use App\Middleware\IsGranted;
use ReflectionClass;
use ReflectionMethod;

class Router {
    private array $routes = [];
    
    /**
     * Ajoute une route
     */
    public function add(string $method, string $path, string $controller, string $action): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    /**
     * Dispatch la requête vers le bon contrôleur
     */
    public function dispatch(Request $request): void {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        // Cherche une route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            // Convertit le pattern de route en regex
            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Extrait les paramètres de l'URL
                $params = $this->extractParams($route['path'], $matches);
                
                // Exécute le contrôleur
                $this->executeController($route['controller'], $route['action'], $params, $request);
                return;
            }
        }
        
        // Aucune route trouvée
        http_response_code(404);
        echo "Page non trouvée";
    }
    
    /**
     * Convertit un pattern de route en regex
     * Exemple : /spectacles/{id} => /^\/spectacles\/([^\/]+)$/
     */
    private function convertToRegex(string $path): string {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^\/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Extrait les paramètres de l'URL
     */
    private function extractParams(string $path, array $matches): array {
        $params = [];
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $paramNames);
        
        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $params[$paramNames[1][$i]] = $matches[$i + 1];
        }
        
        return $params;
    }
    
    /**
     * Exécute le contrôleur avec vérification des attributs de sécurité
     */
    private function executeController(string $controllerName, string $action, array $params, Request $request): void {
        $controllerClass = "App\\Controller\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Contrôleur {$controllerClass} introuvable");
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $action)) {
            throw new \Exception("Méthode {$action} introuvable dans {$controllerClass}");
        }
        
        // Utilise la Reflection pour lire les attributs PHP 8
        $reflectionMethod = new ReflectionMethod($controller, $action);
        $attributes = $reflectionMethod->getAttributes(IsGranted::class);
        
        // Si l'attribut #[IsGranted] est présent, on vérifie les permissions
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $isGranted = $attribute->newInstance();
                
                // Vérifie si l'utilisateur a les permissions requises
                if (!$isGranted->check()) {
                    // Redirige vers la page de connexion
                    header('Location: /login?message=Vous devez être connecté&type=error');
                    exit;
                }
            }
        }
        
        // Exécute la méthode du contrôleur
        $controller->$action($request, $params);
    }
}
