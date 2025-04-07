<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="/index.php">Anthony's & Tiago's Movies</a>
        </div>
        <button class="menu-toggle" aria-label="Menu mobile">☰</button>
        <ul class="nav-links">
            <li><a href="/index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="/pages/categories.php" class="<?= ($current_page == 'categories.php') ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= $_SESSION['initiales']; ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="/pages/profile.php">Mon Profil</a></li>
                        <li>
                            <a href="/pages/cart.php">
                                Voir mon panier (<span id="cart-count">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    echo $stmt->fetchColumn() ?: 0;
                                    ?>
                                </span>)
                            </a>
                        </li>
                        <li><a href="/pages/dashboard.php">Changer mot de passe</a></li>
                        <li><a href="/pages/logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="/pages/login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Connexion</a></li>
                <li><a href="/pages/register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
