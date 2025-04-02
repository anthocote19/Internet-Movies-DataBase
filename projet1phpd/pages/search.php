<?php
require '../config/database.php';

$search = htmlspecialchars($_GET['q'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? OR director LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$movies = $stmt->fetchAll();
?>
<h2>Résultats de recherche</h2>
<ul>
    <?php foreach ($movies as $movie): ?>
        <li><a href="movie_details.php?id=<?= $movie['id'] ?>"><?= $movie['title'] ?> - <?= $movie['director'] ?></a></li>
    <?php endforeach; ?>
</ul>
