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
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $query = "SELECT * FROM movies WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($_SESSION['cart']);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($movies as $movie) {
        $total += $movie['price'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include '../includes/header.php'; ?>

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

<?php include '../includes/footer.php'; ?>

</body>
</html>
