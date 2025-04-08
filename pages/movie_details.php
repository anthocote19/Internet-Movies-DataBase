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
$price = htmlspecialchars($movie['price'] ?? '0.00');
$image = htmlspecialchars($movie['image'] ?? 'default.jpg');


$query_actors = "SELECT actors.id, actors.name 
                 FROM actors 
                 INNER JOIN movie_actor ON actors.id = movie_actor.actor_id 
                 WHERE movie_actor.movie_id = :movie_id";

$stmt_actors = $pdo->prepare($query_actors);
$stmt_actors->execute(['movie_id' => $movie_id]);
$actors = $stmt_actors->fetchAll(PDO::FETCH_ASSOC);
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
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
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
                        <li><a href="profile.php">Mon Profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<?php
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = (int)$stmt->fetchColumn();
} else {
    $cart_count = array_sum($_SESSION['cart'] ?? []);
}
?>
<span id="cart-count"><?= $cart_count ?></span>)
</a></li>
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
        <h1><?= $title; ?></h1>
        <img src="../assets/images/<?= $image; ?>" alt="<?= $title; ?>">
        <p><strong>Réalisateur :</strong> 
        <?php foreach ($actors as $index => $actor): ?>
            <?= htmlspecialchars($actor['name']); ?><?= $index < count($actors) - 1 ? ', ' : ''; ?>
        <?php endforeach; ?>
        
        <p><strong>Acteurs :</strong>
            <?php if (!empty($actors)): ?>
                <?php foreach ($actors as $index => $actor): ?>
                    <?= htmlspecialchars($actor['name']); ?><?= $index < count($actors) - 1 ? ', ' : ''; ?>
                <?php endforeach; ?>

            <?php else: ?>
                Non renseigné
            <?php endif; ?>
        </p>

        <p><strong>Prix :</strong> <?= number_format((float)$price, 2); ?> €</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="cart.php?add=<?= $movie_id; ?>" class="btn">Ajouter au panier</a>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">Connectez-vous pour ajouter le film au panier.</p>
        <?php endif; ?>
    </div>
</section>

<script>
    const toggleBtn = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    toggleBtn.addEventListener('click', () => {
        navLinks.classList.toggle('menu-active');
    });

    
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('menu-active');
        });
    });
</script>



<?php include '../includes/footer.php'; ?>

</body>
</html>
