<?php
session_start();
?>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="/index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="/index.php">Accueil</a></li>
            <li><a href="/pages/categories.php">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="/pages/profile.php"> Mon Profil</a></li>
                <li><a href="/pages/logout.php"> Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/pages/login.php"> Connexion</a></li>
                <li><a href="/pages/register.php"> Inscription</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle">☰</button>
    </nav>
</header>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
