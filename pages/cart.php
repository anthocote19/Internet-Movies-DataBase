<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT movies.* FROM cart JOIN movies ON cart.movie_id = movies.id WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = array_reduce($cart_items, function ($sum, $item) {
    return $sum + $item['price'];
}, 0);
?>
<h2>Panier</h2>
<ul>
    <?php foreach ($cart_items as $item): ?>
        <li><?= $item['title'] ?> - <?= $item['price'] ?>€ <a href="remove_from_cart.php?id=<?= $item['id'] ?>">Supprimer</a></li>
    <?php endforeach; ?>
</ul>
<p>Total : <?= $total ?>€</p>
<a href="checkout.php">Acheter</a>
