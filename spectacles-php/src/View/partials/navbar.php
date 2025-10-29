<?php
// Navbar réutilisable sur toutes les pages
?>
<nav class="navbar">
    <a href="/" class="navbar-brand">Spectacles</a>
    <div class="navbar-menu">
        <a href="/">Accueil</a>
        <a href="/spectacles">Spectacles</a>
        
        <?php if ($currentUser): ?>
            <a href="/profile">Mon Profil</a>
            
            <?php if ($currentUser['role'] === 'ROLE_ADMIN'): ?>
                <a href="/admin/spectacles/create">Ajouter un spectacle</a>
            <?php endif; ?>
            
            <a href="/logout" class="btn btn-danger">Déconnexion</a>
        <?php else: ?>
            <a href="/login" class="btn">Connexion</a>
            <a href="/register" class="btn" style="background: #28a745;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>
