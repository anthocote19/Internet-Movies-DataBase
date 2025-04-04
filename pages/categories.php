<?php
session_start();
require_once '../config/database.php';

$categories_autorisees = ['action', 'drama'];
$categorie = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : null;

if (!in_array($categorie, $categories_autorisees)) {
    $categorie = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - Anthony & Tiago's Movies</title>
    <link rel="stylesheet" href="categories.css?v=1">
</head>
<body>

<section class="category-section">
    <?php if ($categorie): ?>
        <h1>Films de la catégorie : <?= ucfirst($categorie); ?></h1>
    <?php else: ?>
        <h1>Choisissez la catégorie qui vous convient le plus !</h1>
        <ul>
            <li><a href="categories.php?cat=action">Action</a></li>
            <li><a href="categories.php?cat=drama">Drame</a></li>
        </ul>
    <?php endif; ?>

    <div class="movies-container">
        <?php
        if ($categorie) {
            try {
                $requete = "SELECT id, title, price, image FROM movies WHERE category = :categorie ORDER BY created_at DESC";
                $stmt = $pdo->prepare($requete);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
                $stmt->execute();
                $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$films) {
                    echo "<p>Aucun film trouvé dans cette catégorie.</p>";
                } else {
                    foreach ($films as $film) {
                        $id = htmlspecialchars($film['id']);
                        $titre = htmlspecialchars($film['title']);
                        $prix = htmlspecialchars($film['price']);
                        $image = htmlspecialchars($film['image']);

                        echo "
                            <div class='movie-card'>
                                <img src='../assets/images/$image' alt='$titre'>
                                <h3>$titre</h3>
                                <p>$prix €</p>
                                <a href='movie_details.php?id=$id' class='btn'>Voir Détails</a>";
                        
                        if (isset($_SESSION['user_id'])) {
                            echo "<a href='cart.php?add=$id' class='btn'>Ajouter au panier</a>";
                        } else {
                            echo "<p class='not-logged'><a href='login.php'>Connectez-vous</a> pour ajouter au panier</p>";
                        }

                        echo "</div>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p>Erreur lors de la récupération des films : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        ?>
    </div>

    <br><br><br><br>
    <a href="../index.php" class="back-btn">⬅ Retourner à l'accueil</a>
</section>

</body>
</html>
