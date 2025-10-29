# Site de Spectacles - PHP Pur avec JWT

### Lancement avec le serveur PHP intégré 

**IMPORTANT : Utilisez cette commande exacte pour éviter l'erreur "Not Found"**

\`\`\`bash
# Depuis la racine du projet dans le dossier spectacles-php
php -S localhost:8000 -t public public/router.php
\`\`\`

2. **Accédez au projet**
\`\`\`
http://localhost/spectacles-php/public
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

