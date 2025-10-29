# Site de Spectacles - PHP Pur avec JWT

Application web complète de gestion de spectacles développée en PHP pur avec architecture MVC, système d'authentification JWT, refresh tokens et middleware sous forme d'attributs PHP 8.

## Fonctionnalités

### Pages Publiques
- **Page d'accueil** : Message de bienvenue avec menu de navigation
- **Liste des spectacles** : Affichage de tous les spectacles disponibles
- **Fiche spectacle** : Détails complets d'un spectacle

### Utilisateurs Authentifiés (ROLE_USER)
- **Réservation de places** : Réserver des billets pour les spectacles
- **Page de profil** : Voir toutes ses réservations

### Administrateurs (ROLE_ADMIN)
- **Ajouter des spectacles** : Créer de nouveaux spectacles

## Architecture Technique

### Structure du Projet
\`\`\`
/public
  index.php          # Point d'entrée unique
  .htaccess          # Réécriture d'URL
  /css
    style.css        # Styles globaux
/src
  /Core
    Router.php       # Routeur avec support des paramètres dynamiques
    Request.php      # Encapsulation des requêtes HTTP
  /Controller
    HomeController.php
    AuthController.php
    SpectacleController.php
    BookingController.php
    ProfileController.php
    AdminController.php
  /Service
    JWTService.php        # Gestion des JWT (access + refresh tokens)
    AuthService.php       # Authentification
    SpectacleService.php  # CRUD spectacles
    BookingService.php    # Gestion réservations
  /Middleware
    IsGranted.php    # Attribut PHP 8 pour la sécurité
  /View
    *.php            # Templates HTML
/config
  routes.php         # Définition des routes
/vendor
  autoload.php       # Autoloader PSR-4
\`\`\`

### Technologies Utilisées
- **PHP 8.0+** : Attributs, typage strict, namespaces
- **JWT (JSON Web Tokens)** : Authentification stateless
- **Refresh Tokens** : Prolongation automatique de session
- **Attributs PHP 8** : Middleware déclaratif avec `#[IsGranted]`
- **Architecture MVC** : Séparation des responsabilités
- **PSR-4 Autoloading** : Chargement automatique des classes
- **Routeur personnalisé** : Support des URLs dynamiques

## Système de Sécurité

### JWT (JSON Web Tokens)
- **Access Token** : Durée de vie courte (15 minutes)
- **Refresh Token** : Durée de vie longue (7 jours)
- **Signature HMAC-SHA256** : Garantit l'intégrité du token
- **Cookie HTTP-only** : Protection contre les attaques XSS
- **SameSite Strict** : Protection contre les attaques CSRF

### Middleware avec Attributs PHP 8
\`\`\`php
// Protège une méthode - nécessite d'être connecté
#[IsGranted('ROLE_USER')]
public function book() { ... }

// Protège une méthode - nécessite le rôle admin
#[IsGranted('ROLE_ADMIN')]
public function create() { ... }
\`\`\`

### Validation des Tokens
1. Vérification de l'existence du cookie
2. Validation de la signature (non modifié)
3. Vérification de l'expiration
4. Vérification du rôle si spécifié

## Installation et Lancement

### Prérequis
- PHP 8.0 ou supérieur
- Serveur web (Apache, Nginx) ou PHP built-in server
- Module Apache `mod_rewrite` activé (si Apache)

### Installation

1. **Clonez ou téléchargez le projet**
\`\`\`bash
git clone <url-du-projet>
cd spectacles-php
\`\`\`

2. **Vérifiez la version de PHP**
\`\`\`bash
php -v
# Doit afficher PHP 8.0 ou supérieur
\`\`\`

### Lancement avec le serveur PHP intégré

1. **Démarrez le serveur depuis le dossier public**
\`\`\`bash
cd public
php -S localhost:8000
\`\`\`

2. **Ouvrez votre navigateur**
\`\`\`
http://localhost:8000
\`\`\`

### Lancement avec XAMPP/WAMP/MAMP

1. **Copiez le projet dans le dossier web**
   - XAMPP : `C:\xampp\htdocs\spectacles-php`
   - WAMP : `C:\wamp64\www\spectacles-php`
   - MAMP : `/Applications/MAMP/htdocs/spectacles-php`

2. **Accédez au projet**
\`\`\`
http://localhost/spectacles-php/public
\`\`\`

3. **Configuration Apache** (si nécessaire)
   - Assurez-vous que `mod_rewrite` est activé
   - Le fichier `.htaccess` doit être présent dans `/public`

### Lancement avec Docker (optionnel)

\`\`\`bash
docker run -d -p 8000:80 -v $(pwd):/var/www/html php:8.2-apache
\`\`\`

## Comptes de Test

### Administrateur
- **Identifiant** : `admin`
- **Mot de passe** : `admin123`
- **Permissions** : Toutes (créer spectacles, réserver, etc.)

### Utilisateur Standard
- **Identifiant** : `user`
- **Mot de passe** : `user123`
- **Permissions** : Réserver des places, voir son profil

## Utilisation

### 1. Connexion
- Accédez à `/login`
- Utilisez un des comptes de test
- Un JWT est créé et stocké dans un cookie sécurisé

### 2. Parcourir les Spectacles
- Cliquez sur "Spectacles" dans le menu
- Consultez les détails d'un spectacle

### 3. Réserver une Place (Utilisateur connecté)
- Sur la fiche d'un spectacle, cliquez sur "Réserver une place"
- La réservation est ajoutée à votre profil

### 4. Voir ses Réservations
- Cliquez sur "Mon Profil"
- Toutes vos réservations sont listées

### 5. Ajouter un Spectacle (Admin uniquement)
- Connectez-vous avec le compte admin
- Cliquez sur "Ajouter un spectacle"
- Remplissez le formulaire

## Refresh Token - Prolongation de Session

Le système utilise des **refresh tokens** pour prolonger automatiquement la session sans redemander les identifiants.

### Fonctionnement
1. À la connexion, deux tokens sont créés :
   - **Access token** (15 min) : stocké dans un cookie
   - **Refresh token** (7 jours) : stocké en session PHP

2. Quand l'access token expire, le client peut appeler `/refresh-token` avec le refresh token pour obtenir un nouvel access token

3. Cela permet de rester connecté pendant 7 jours sans redemander le mot de passe

### Exemple d'utilisation (JavaScript)
\`\`\`javascript
// Appel automatique avant expiration de l'access token
fetch('/refresh-token', {
  method: 'POST',
  credentials: 'include'
})
.then(response => response.json())
.then(data => {
  console.log('Token renouvelé');
});
\`\`\`

## Points Techniques Importants

### Routeur Dynamique
Le routeur supporte les paramètres dans les URLs :
\`\`\`php
$router->add('GET', '/spectacles/{id}', 'SpectacleController', 'show');
// Correspond à : /spectacles/1, /spectacles/2, etc.
\`\`\`

### Attributs PHP 8 pour la Sécurité
Les attributs permettent une sécurité déclarative :
\`\`\`php
#[IsGranted('ROLE_ADMIN')]
public function create() {
    // Cette méthode n'est accessible qu'aux admins
}
\`\`\`

### Autoloader PSR-4
Les classes sont chargées automatiquement selon leur namespace :
\`\`\`php
namespace App\Controller;
// Fichier : src/Controller/HomeController.php
\`\`\`

### Base de Données Simulée
Les données sont stockées dans des tableaux PHP statiques pour simplifier le déploiement. En production, utilisez une vraie base de données (MySQL, PostgreSQL).

## Sécurité en Production

Pour un déploiement en production, pensez à :

1. **Changer la clé secrète JWT** dans `JWTService.php`
2. **Activer HTTPS** et mettre `secure => true` dans les cookies
3. **Utiliser une vraie base de données** (MySQL, PostgreSQL)
4. **Hasher les mots de passe** avec `password_hash()` et `password_verify()`
5. **Valider et assainir toutes les entrées utilisateur**
6. **Ajouter des logs** pour tracer les actions sensibles
7. **Implémenter un rate limiting** contre les attaques par force brute
8. **Utiliser des variables d'environnement** pour les secrets

## Dépannage

### Erreur 404 sur toutes les pages
- Vérifiez que `mod_rewrite` est activé (Apache)
- Vérifiez que le fichier `.htaccess` est présent dans `/public`

### Les cookies ne sont pas stockés
- Vérifiez que la session PHP est démarrée (`session_start()`)
- Vérifiez les paramètres de cookies dans le navigateur

### Erreur "Class not found"
- Vérifiez que l'autoloader est bien chargé
- Vérifiez les namespaces des classes

### Le refresh token ne fonctionne pas
- Vérifiez que la session PHP est active
- Vérifiez que le refresh token est bien stocké en session

## Licence

Projet éducatif - Libre d'utilisation

## Auteur

Développé comme exercice d'apprentissage PHP avec JWT et architecture MVC
