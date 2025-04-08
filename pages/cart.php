<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $movie_id = intval($_GET['add']);
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND movie_id = ? AND is_active = 1");
        $stmt->execute([$user_id, $movie_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND movie_id = ? AND is_active = 1");
            $stmt->execute([$user_id, $movie_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, movie_id, quantity, added_at, is_active) VALUES (?, ?, 1, NOW(), 1)");
            $stmt->execute([$user_id, $movie_id]);
        }
    } else {
        $_SESSION['cart'][$movie_id] = ($_SESSION['cart'][$movie_id] ?? 0) + 1;
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $movie_id = intval($_GET['remove']);
    if ($user_id) {
        $stmt = $pdo->prepare("UPDATE cart SET is_active = 0 WHERE user_id = ? AND movie_id = ?");
        $stmt->execute([$user_id, $movie_id]);
    } else {
        unset($_SESSION['cart'][$movie_id]);
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['clear'])) {
    if ($user_id) {
        $stmt = $pdo->prepare("UPDATE cart SET is_active = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

$movies = [];
$total = 0.0;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT movies.*, cart.quantity 
                           FROM cart 
                           JOIN movies ON cart.movie_id = movies.id 
                           WHERE cart.user_id = ? AND cart.is_active = 1");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $cart_items = array_filter($_SESSION['cart'], function ($qty, $id) {
        return is_numeric($id) && is_numeric($qty) && $qty > 0;
    }, ARRAY_FILTER_USE_BOTH);

    if (!empty($cart_items)) {
        $movie_ids = array_keys($cart_items);
        $placeholders = implode(',', array_fill(0, count($movie_ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE id IN ($placeholders)");
        $stmt->execute($movie_ids);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($movies as &$movie) {
            $movie['quantity'] = $cart_items[$movie['id']] ?? 1;
        }
    }
}

foreach ($movies as $movie) {
    $total += floatval($movie['price']) * intval($movie['quantity']);
}

$cart_count = 0;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetchColumn();
} else {
    $cart_count = array_sum($_SESSION['cart'] ?? []);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="./cart.css?v=<?= time(); ?>">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>

        <button class="menu-toggle" aria-label="Ouvrir le menu">&#9776;</button>

        <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="categories.php">Catégories</a></li>
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

<main>
    <section class="cart">
        <h1>Votre Panier</h1>

        <?php if (empty($movies)): ?>
            <p>Votre panier est vide.</p>
            <br><br><br><br><br>
            <a href="../index.php" class="back-btn">Retourner à l'accueil</a>
        <?php else: ?>
            <ul class="cart-items">
                <?php foreach ($movies as $movie): ?>
                    <li>
                        <img src="../assets/images/<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
                        <div>
                            <h3><?= htmlspecialchars($movie['title']); ?></h3>
                            <p>Prix: <?= number_format($movie['price'], 2); ?> €</p>
                            <p>Quantité: <?= intval($movie['quantity']); ?></p>
                            <a href="cart.php?remove=<?= $movie['id']; ?>" class="btn">Retirer le film</a>

                            <form method="POST" action="finalisation_achat.php" style="display:inline;">
                                <input type="hidden" name="movie_id" value="<?= $movie['id']; ?>">
                                <input type="hidden" name="quantity" value="<?= intval($movie['quantity']); ?>">
                                <button type="submit" class="btn">Acheter ce film</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h2>Total: <?= number_format($total, 2); ?> €</h2>
            <a href="cart.php?clear=true" class="btn btn-danger">Vider le panier</a>
            <form method="POST" action="finalisation_achat.php" style="display:inline;">
                <button type="submit" class="btn">Acheter</button>
            </form>
            <a href="../index.php" class="back-btn">Retourner à l'accueil</a>
        <?php endif; ?>
    </section>
</main>

<script src="../assets/js/panier.js?v=<?= time(); ?>"></script>
<?php include '../includes/footer_bas_de_page.php'; ?>
</body>
</html>
