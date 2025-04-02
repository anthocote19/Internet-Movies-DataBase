<?php
session_start();
require_once '../config/database.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajouter un film au panier
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $movie_id = intval($_GET['add']);
    if (!in_array($movie_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $movie_id;
    }
    header("Location: cart.php");
    exit();
}

// Supprimer un film du panier
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [intval($_GET['remove'])]);
    header("Location: cart.php");
    exit();
}

// Vider le panier
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

// Récupérer les détails des films dans le panier
$movies = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    // Filtrer les IDs pour éviter tout problème
    $cart_ids = array_filter($_SESSION['cart'], 'is_numeric');
    
    if (!empty($cart_ids)) { // Vérifier si après filtrage il y a bien des IDs
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $query = "SELECT * FROM movies WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array_values($cart_ids)); // S'assurer que c'est bien un tableau indexé
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calcul du total
    foreach ($movies as $movie) {
        $total += $movie['price'];
    }
}


$initiale = isset($_SESSION['username']) ? strtoupper($_SESSION['username'][0]) : '?';
$cart_count = count($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="./cart.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="../pages/categories.php">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"> <?= $initiale; ?> </span>
                    <ul class="dropdown-menu">
                        <li><a href="../pages/profile.php">Consulter mon profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<span id="cart-count"><?= $cart_count; ?></span>)</a></li>
                        <li><a href="../pages/logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="../pages/login.php">Connexion</a></li>
                <li><a href="../pages/register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="cart">
    <h1>Votre Panier</h1>
    <?php if (empty($movies)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <ul class="cart-items">
            <?php foreach ($movies as $movie): ?>
                <li>
                    <img src="../assets/images/<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
                    <div>
                        <h3><?= htmlspecialchars($movie['title']); ?></h3>
                        <p>Prix: <?= htmlspecialchars($movie['price']); ?> €</p>
                        <a href="cart.php?remove=<?= $movie['id']; ?>" class="btn">❌ Retirer</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Total: <?= number_format($total, 2); ?> €</h2>
        <a href="cart.php?clear=true" class="btn">🗑 Vider le panier</a>
    <?php endif; ?>
</section>

<script>
    $(document).ready(function() {
        $('.dropdown').click(function() {
            $(this).find('.dropdown-menu').toggle();
        });
    });
</script>



</body>
</html>