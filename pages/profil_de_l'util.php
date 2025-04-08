<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, date_joined FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

$stmt = $pdo->prepare("SELECT movies.title, movies.image FROM purchases 
                        JOIN movies ON purchases.movie_id = movies.id 
                        WHERE purchases.user_id = ?");
$stmt->execute([$user_id]);
$purchased_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Anthony & Tiago's Movies</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="./profil_de_l'util.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>

<section class="conteneur-profil">
    <h1>Votre profil : <?= htmlspecialchars($user['username']) ?></h1>
    <div class="infos-profil">
        <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Membre depuis :</strong> <?= date("d/m/Y", strtotime($user['date_joined'])) ?></p>
    </div>

    <h2>Vos films achetés</h2>
    <div class="conteneur-films">
        <?php if (empty($purchased_movies)): ?>
            <p>Vous n'avez acheté aucun film pour le moment.</p>
        <?php else: ?>
            <?php foreach ($purchased_movies as $movie): ?>
                <div class="carte-film">
                    <img src="../assets/images/<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="../index.php" class="bouton retour">Retour à l'accueil</a>
</section>

<?php include '../includes/footer_bas_de_page.php'; ?>

</body>
</html>
