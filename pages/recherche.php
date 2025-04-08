<?php
require '../config/database.php';

$recherche = htmlspecialchars($_GET['q'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? OR director LIKE ?");
$stmt->execute(["%$recherche%", "%$recherche%"]);
$films = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="recherche.css?v=<?= time(); ?>">
</head>
<body>

<div class="resultats-recherche">
    <h2>Résultats de recherche</h2>
    <ul>
        <?php if (count($films) > 0): ?>
            <?php foreach ($films as $film): ?>
                <li><a href="detailsdes_films.php?id=<?= $film['id'] ?>"><?= $film['title'] ?> - <?= $film['director'] ?></a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun film trouvé pour votre recherche.</li>
            <li><a href="../index.php" class="bouton-retour">Cherchez un autre film ?</a></li>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
