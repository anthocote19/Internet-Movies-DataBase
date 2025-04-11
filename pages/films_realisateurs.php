<?php
session_start();
require_once '../config/database.php';

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmtCart = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
    $stmtCart->execute([$user_id]);
    $cart_count = $stmtCart->fetchColumn() ?: 0;
} elseif (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}

if (isset($_GET['name']) && !empty($_GET['name'])) {
    $name = $_GET['name'];
    $sql = "SELECT m.*, d.name AS director_name 
            FROM movies m 
            LEFT JOIN directors d ON m.director_id = d.id
            WHERE d.name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);
} else {
    $sql = "SELECT m.*, d.name AS director_name 
            FROM movies m 
            LEFT JOIN directors d ON m.director_id = d.id";
    $stmt = $pdo->query($sql);
}

$films = $stmt->fetchAll();
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Films par Réalisateur</title>
    <link rel="stylesheet" href="films_realisateurs.css">
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>

        <button class="menu-toggle" aria-label="Menu mobile">☰</button>

        <ul class="nav-links">
            <li><a href="../index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="categories.php" class="<?= $currentPage === 'categories.php' ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= htmlspecialchars($_SESSION['initiales'] ?? '?'); ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="profil_de_l'util.php">Mon Profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<span id="cart-count"><?= $cart_count ?></span>)</a></li>
                        <li><a href="dashboard.php">Changer mot de passe</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<br><br><br><br>

<h1>Liste des Films<?= isset($name) ? " de " . htmlspecialchars($name) : "" ?></h1>

<?php if (isset($name)): ?>
    <a href="./detailsdes_films.php" class="back-btn">Retour à tous les films</a>
<?php endif; ?>

<?php if (count($films) > 0): ?>
    <div class="films-container">
        <?php foreach ($films as $film): ?>
            <div class="film">
                <h2><?= htmlspecialchars($film['title']) ?></h2>
                <p>
                    <strong>Réalisateur :</strong>
                    <?php if (!empty($film['director_name'])): ?>
                        <a href="films_realisateurs.php?name=<?= urlencode($film['director_name']) ?>">
                            <?= htmlspecialchars($film['director_name']) ?>
                        </a>
                    <?php else: ?>
                        <em>Non renseigné</em>
                    <?php endif; ?>
                </p>
                <p><strong>Prix :</strong> <?= number_format($film['price'], 2) ?> €</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="add-to-cart-btn" data-id="<?= $film['id'] ?>">Ajouter au panier</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun film trouvé.</p>
<?php endif; ?>

<?php include '../includes/footer_bas_de_page.php'; ?>
<script src="../assets/js/films_rea.js?v=<?= time(); ?>"></script>
</body>
</html>
