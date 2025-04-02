<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$movie_id = intval($_GET['id']);

$query = "SELECT * FROM movies WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $movie_id]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    echo "<p>Film introuvable.</p>";
    exit();
}


$title = htmlspecialchars($movie['title'] ?? 'Titre inconnu');
$director = htmlspecialchars($movie['director'] ?? 'Non renseigné');
$actors = htmlspecialchars($movie['actors'] ?? 'Non renseigné');
$price = htmlspecialchars($movie['price'] ?? '0.00');
$image = htmlspecialchars($movie['image'] ?? 'default.jpg'); 

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - Détails</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="categories.php">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="user-initials"><?= htmlspecialchars($_SESSION['initiales'] ?? '?'); ?></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle">☰</button>
    </nav>
</header>

<section class="movie-details">
    <div class="container">
        <h1><?= $title; ?></h1>
        <img src="../assets/images/<?= $image; ?>" alt="<?= $title; ?>">
        <p><strong>Réalisateur:</strong> <a href="director_movies.php?director=<?= urlencode($director); ?>"><?= $director; ?></a></p>
        <p><strong>Acteurs:</strong> <?= $actors; ?></p>
        <p><strong>Prix:</strong> <?= number_format((float)$price, 2); ?> €</p>
        <a href="cart.php?add=<?= $movie_id; ?>" class="btn">🛒 Ajouter au panier</a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
