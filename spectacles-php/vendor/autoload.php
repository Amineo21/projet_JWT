<?php
/**
 * AUTOLOADER PSR-4
 * 
 * Charge automatiquement les classes en fonction de leur namespace
 * Évite d'avoir à faire des require_once partout
 */

spl_autoload_register(function ($class) {
    // Namespace de base de l'application
    $prefix = 'App\\';
    
    // Dossier de base pour le namespace
    $base_dir = __DIR__ . '/../src/';
    
    // Vérifie si la classe utilise notre namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Récupère le nom de classe relatif
    $relative_class = substr($class, $len);
    
    // Remplace le namespace par le chemin de fichier
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Si le fichier existe, on le charge
    if (file_exists($file)) {
        require $file;
    }
});
