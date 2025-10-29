<?php
/**
 * CLASSE REQUEST
 * 
 * Encapsule les données de la requête HTTP
 * Facilite l'accès aux données GET, POST, cookies, etc.
 */

namespace App\Core;

class Request {
    private string $method;
    private string $uri;
    private array $query;
    private array $post;
    private array $cookies;
    
    public function __construct(string $method, string $uri, array $query, array $post, array $cookies) {
        $this->method = $method;
        $this->uri = $uri;
        $this->query = $query;
        $this->post = $post;
        $this->cookies = $cookies;
    }
    
    /**
     * Crée une instance depuis les variables globales PHP
     */
    public static function createFromGlobals(): self {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        return new self(
            $_SERVER['REQUEST_METHOD'],
            $uri,
            $_GET,
            $_POST,
            $_COOKIE
        );
    }
    
    public function getMethod(): string {
        return $this->method;
    }
    
    public function getUri(): string {
        return $this->uri;
    }
    
    public function get(string $key, $default = null) {
        return $this->query[$key] ?? $default;
    }
    
    public function post(string $key, $default = null) {
        return $this->post[$key] ?? $default;
    }
    
    public function cookie(string $key, $default = null) {
        return $this->cookies[$key] ?? $default;
    }
    
    public function all(): array {
        return array_merge($this->query, $this->post);
    }
}
