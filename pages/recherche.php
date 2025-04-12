<?php
require '../config/database.php';

$recherche = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';


$sql = "
    SELECT movies.*, directors.name AS director_name
    FROM movies
    LEFT JOIN directors ON movies.director_id = directors.id
    WHERE movies.title LIKE ? OR directors.name LIKE ?
";

$requete = $pdo->prepare($sql);
$requete->execute(["%$recherche%", "%$recherche%"]);
$filmsTrouves = $requete->fetchAll();
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
    <h2>Résultats pour : "<?= htmlspecialchars($recherche) ?>"</h2>
    <ul>
        <?php if (!empty($filmsTrouves)): ?>
            <?php foreach ($filmsTrouves as $film): ?>
                <li>
                    <a href="detailsdes_films.php?id=<?= $film['id'] ?>">
                        <?= $film['title'] ?> - Réalisé par <?= $film['director_name'] ?? 'Inconnu' ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun film trouvé pour votre recherche.</li>
            <li><a href="../index.php" class="bouton-retour">Revenir à l'accueil</a></li>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
