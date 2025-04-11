<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$idFilm = (int) $_GET['id'];

// On récupère le film avec le nom du réalisateur (JOIN)
$stmt = $pdo->prepare("
    SELECT m.*, d.name AS director_name 
    FROM movies m
    LEFT JOIN directors d ON m.director_id = d.id
    WHERE m.id = :id
");
$stmt->execute(['id' => $idFilm]);
$film = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    echo "<p>Film introuvable.</p>";
    exit();
}

$titre = htmlspecialchars($film['title'] ?? 'Titre inconnu');
$realisateur = $film['director_name'] ?? null;
$prix = htmlspecialchars($film['price'] ?? '0.00');
$image = htmlspecialchars($film['image'] ?? 'default.jpg');

// On récupère les acteurs liés au film
$stmtActeurs = $pdo->prepare("
    SELECT actors.id, actors.name 
    FROM actors 
    INNER JOIN movie_actor ON actors.id = movie_actor.actor_id 
    WHERE movie_actor.movie_id = :movie_id
");
$stmtActeurs->execute(['movie_id' => $idFilm]);
$listeActeurs = $stmtActeurs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre; ?> - Détails</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <script defer src="../assets/js/detailsdesfilms.js?v=<?= time(); ?>"></script>
</head>
<body>

<br><br><br><br><br><br><br><br><br><br>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="categories.php">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= htmlspecialchars($_SESSION['initiales'] ?? '?'); ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="profil_de_l'util.php">Mon Profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<?php
                            $nbArticles = 0;
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1");
                                $stmt->execute([$_SESSION['user_id']]);
                                $nbArticles = (int) $stmt->fetchColumn();
                            } else {
                                $nbArticles = array_sum($_SESSION['cart'] ?? []);
                            }
                            ?><span id="cart-count"><?= $nbArticles ?></span>)</a></li>
                        <li><a href="dashboard.php">Changer mot de passe</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle" aria-label="Ouvrir le menu">☰</button>
    </nav>
</header>

<section class="movie-details">
    <div class="container">
        <h1><?= $titre; ?></h1>
        <img src="../assets/images/<?= $image; ?>" alt="<?= $titre; ?>">

        <p><strong>Réalisateur :</strong>
            <?php if ($realisateur): ?>
                <a href="films_realisateurs.php?name=<?= urlencode($realisateur); ?>">
                    <?= htmlspecialchars($realisateur); ?>
                </a>
            <?php else: ?>
                <em>Non renseigné</em>
            <?php endif; ?>
        </p>

        <p><strong>Acteurs :</strong>
            <?php if (!empty($listeActeurs)): ?>
                <?php foreach ($listeActeurs as $i => $acteur): ?>
                    <?= htmlspecialchars($acteur['name']); ?><?= $i < count($listeActeurs) - 1 ? ', ' : ''; ?>
                <?php endforeach; ?>
            <?php else: ?>
                Non renseigné
            <?php endif; ?>
        </p>

        <p><strong>Prix :</strong> <?= number_format((float)$prix, 2); ?> €</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="cart.php?add=<?= $idFilm; ?>" class="btn">Ajouter au panier</a>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">Connectez-vous pour ajouter le film au panier.</p>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer_bas_de_page.php'; ?>
</body>
</html>
