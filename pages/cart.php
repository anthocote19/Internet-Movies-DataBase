<?php
session_start();
require_once '../config/database.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Vérifier si l'utilisateur est connecté
$user_id = $_SESSION['user_id'] ?? null;

// Ajouter un film au panier
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $movie_id = intval($_GET['add']);

    if ($user_id) {
        // Vérifier si l'article existe déjà dans le panier
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND movie_id = ?");
        $stmt->execute([$user_id, $movie_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND movie_id = ?");
            $stmt->execute([$user_id, $movie_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, movie_id, quantity, added_at) VALUES (?, ?, 1, NOW())");
            $stmt->execute([$user_id, $movie_id]);
        }
    } else {
        $_SESSION['cart'][$movie_id] = ($_SESSION['cart'][$movie_id] ?? 0) + 1;
    }

    header("Location: cart.php");
    exit();
}

// Supprimer un film du panier
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $movie_id = intval($_GET['remove']);

    if ($user_id) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND movie_id = ?");
        $stmt->execute([$user_id, $movie_id]);
    } else {
        unset($_SESSION['cart'][$movie_id]);
    }

    header("Location: cart.php");
    exit();
}

// Vider le panier
if (isset($_GET['clear'])) {
    if ($user_id) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
    $_SESSION['cart'] = [];

    header("Location: cart.php");
    exit();
}

// Récupérer les détails des films dans le panier
$movies = [];
$total = 0;

if ($user_id) {
    // Charger le panier depuis la base de données
    $stmt = $pdo->prepare("SELECT movies.*, cart.quantity FROM cart 
                           JOIN movies ON cart.movie_id = movies.id 
                           WHERE cart.user_id = ?");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si des articles sont également présents dans $_SESSION['cart'], les ajouter
    if (!empty($_SESSION['cart'])) {
        $cart_ids = array_keys($_SESSION['cart']);
        if (!empty($cart_ids)) {
            $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM movies WHERE id IN ($placeholders)");
            $stmt->execute($cart_ids);
            $sessionMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sessionMovies as $sessionMovie) {
                $sessionMovie['quantity'] = $_SESSION['cart'][$sessionMovie['id']] ?? 1;
                $movies[] = $sessionMovie; // Ajouter au panier global
            }
        }
    }
} else {
    // Charger le panier depuis la session uniquement
    if (!empty($_SESSION['cart'])) {
        $cart_ids = array_keys($_SESSION['cart']);
        if (!empty($cart_ids)) {
            $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM movies WHERE id IN ($placeholders)");
            $stmt->execute($cart_ids);
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($movies as &$movie) {
                $movie['quantity'] = $_SESSION['cart'][$movie['id']] ?? 1;
            }
        }
    }
}


// Calcul du total
foreach ($movies as $movie) {
    $total += $movie['price'] * $movie['quantity'];
}

$initiale = isset($_SESSION['username']) ? strtoupper($_SESSION['username'][0]) : '?';
$cart_count = array_sum(array_column($movies, 'quantity'));

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
            <?php if ($user_id): ?>
                <li class="dropdown">
                    <span class="user-initials"> <?= htmlspecialchars($initiale); ?> </span>
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

<br><br><br>

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
                        <p>Quantité: <?= htmlspecialchars($movie['quantity']); ?></p>
                        <a href="cart.php?remove=<?= $movie['id']; ?>" class="btn">❌ Retirer</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Total: <?= number_format($total, 2); ?> €</h2>
        <a href="cart.php?clear=true" class="btn">🗑 Vider le panier</a>
        <a href="../pages/checkout.php" class="btn">Acheter</a>
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
