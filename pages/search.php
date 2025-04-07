<?php
require '../config/database.php';

$search = htmlspecialchars($_GET['q'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? OR director LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$movies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="search.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="search-results">
    <h2>Résultats de recherche</h2>
    <ul>
        <?php if (count($movies) > 0): ?>
            <?php foreach ($movies as $movie): ?>
                <li><a href="movie_details.php?id=<?= $movie['id'] ?>"><?= $movie['title'] ?> - <?= $movie['director'] ?></a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun film trouvé pour votre recherche.</li>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
