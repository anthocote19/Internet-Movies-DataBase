<?php
require '../config/database.php';
session_start();

$recherche = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';

$sql = "
    SELECT movies.*, directors.name AS director_name
    FROM movies
    LEFT JOIN directors ON movies.director_id = directors.id
    WHERE movies.title LIKE ? OR directors.name LIKE ?
";

$requete = $pdo->prepare($sql);
$requete->execute(["%$recherche%", "%$recherche%"]);
$filmsTrouves = $requete->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="recherche.css?v=<?= time(); ?>">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">Anthony & Tiago's Movies</a>
        </div>
        <button class="menu-toggle" aria-label="Menu mobile">☰</button>
        <ul class="nav-links">
            <li><a href="../index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="./categories.php" class="<?= ($current_page == 'categories.php') ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= $_SESSION['initiales']; ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="./profil_de_l'util.php">Mon Profil</a></li>
                        <li>
                            <a href="./cart.php">
                                Voir mon panier (<span id="cart-count">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    echo $stmt->fetchColumn() ?: 0;
                                    ?>
                                </span>)
                            </a>
                        </li>
                        <li><a href="./dashboard.php">Changer mot de passe</a></li>
                        <li><a href="./logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="./login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Connexion</a></li>
                <li><a href="./register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="contenu-principal">
    <div class="resultats-recherche">
        <h2>Résultats pour : "<?= htmlspecialchars($recherche) ?>"</h2>
        <ul>
            <?php if (!empty($filmsTrouves)): ?>
                <?php foreach ($filmsTrouves as $film): ?>
                    <li>
                        <a href="detailsdes_films.php?id=<?= $film['id'] ?>">
                            <?= $film['title'] ?> - Réalisé par <?= $film['director_name'] ?? 'Inconnu' ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Aucun film trouvé pour votre recherche.</li>
                <li><a href="../index.php" class="bouton-retour">Revenir à l'accueil</a></li>
            <?php endif; ?>
        </ul>
    </div>
</main>

<?php include '../includes/footer_bas_de_page.php'; ?>
<script src="../assets/js/recherche.js?v=<?= time(); ?>"></script>

</body>
</html>
