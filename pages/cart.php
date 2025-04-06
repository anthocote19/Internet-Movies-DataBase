<?php
session_start();
require_once '../config/database.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$user_id = $_SESSION['user_id'] ?? null;

// Ajouter un film
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $movie_id = intval($_GET['add']);
    if ($user_id) {
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

// Supprimer un film
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

// Récupérer les films
$movies = [];
$total = 0;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT movies.*, cart.quantity FROM cart 
                           JOIN movies ON cart.movie_id = movies.id 
                           WHERE cart.user_id = ?");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
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

// Total
foreach ($movies as $movie) {
    $total += $movie['price'] * $movie['quantity'];
}
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

<br><br><br>

<section class="cart">
    <h1>Votre Panier</h1>
    <?php if (empty($movies)): ?>
        <p>Votre panier est vide.</p>
        <br>
        <br>
        <br>
        <br>
        <br>
        <a href="../index.php" class="back-btn">Retourner à l'accueil</a>
    <?php else: ?>
        <ul class="cart-items">
            <?php foreach ($movies as $movie): ?>
                <li>
                    <img src="../assets/images/<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
                    <div>
                        <h3><?= htmlspecialchars($movie['title']); ?></h3>
                        <p>Prix: <?= htmlspecialchars($movie['price']); ?> €</p>
                        <p>Quantité: <?= htmlspecialchars($movie['quantity']); ?></p>
                        <a href="cart.php?remove=<?= $movie['id']; ?>" class="btn"> Retirer le film</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Total: <?= number_format($total, 2); ?> €</h2>
        <a href="cart.php?clear=true" class="btn"> Vider le panier</a>
        <form method="POST" action="checkout.php" style="display:inline;">
    <button type="submit" class="btn">Acheter</button>
</form>

        <a href="../index.php" class="back-btn">Retourner à l'accueil</a>
    <?php endif; ?>
</section>

</body>
</html>
