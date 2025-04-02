<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$movie_id = intval($_GET['id']);

$query = "SELECT * FROM movies WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $movie_id]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    echo "<p>Film introuvable.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['title']); ?> - Détails</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include '../includes/header.php'; ?>

<section class="movie-details">
    <div class="container">
        <h1><?= htmlspecialchars($movie['title']); ?></h1>
        <img src="../assets/images/<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
        <p><strong>Réalisateur:</strong> <a href="director_movies.php?director=<?= urlencode($movie['director']); ?>"><?= htmlspecialchars($movie['director']); ?></a></p>
        <p><strong>Acteurs:</strong> <?= htmlspecialchars($movie['actors']); ?></p>
        <p><strong>Prix:</strong> <?= htmlspecialchars($movie['price']); ?> €</p>
        <a href="cart.php?add=<?= $movie['id']; ?>" class="btn">🛒 Ajouter au panier</a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
