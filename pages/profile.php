<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT movies.* FROM purchases JOIN movies ON purchases.movie_id = movies.id WHERE purchases.user_id = ?");
$stmt->execute([$user_id]);
$purchased_movies = $stmt->fetchAll();
?>
<h2>Vos films achetés</h2>
<ul>
    <?php foreach ($purchased_movies as $movie): ?>
        <li><?= $movie['title'] ?></li>
    <?php endforeach; ?>
</ul>
<a href="logout.php">Déconnexion</a>
