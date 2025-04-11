<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];


$req = $pdo->prepare("SELECT username, email, date_joined FROM users WHERE id = ?");
$req->execute([$id]);
$user = $req->fetch();

if (!$user) {
    echo "Profil introuvable.";
    exit();
}


$req = $pdo->prepare("SELECT movies.title, movies.image FROM purchases 
                      JOIN movies ON purchases.movie_id = movies.id 
                      WHERE purchases.user_id = ?");
$req->execute([$id]);
$films = $req->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="profil_de_l'util.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>

<section class="conteneur-profil">
    <h1>Bienvenue <?= htmlspecialchars($user['username']) ?></h1>

    <div class="infos-profil">
        <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Inscrit depuis :</strong> <?= date("d/m/Y", strtotime($user['date_joined'])) ?></p>
    </div>

    <h2>Mes films achetés</h2>
    <div class="conteneur-films">
        <?php if (empty($films)): ?>
            <p>Aucun film acheté pour le moment.</p>
        <?php else: ?>
            <?php foreach ($films as $film): ?>
                <div class="carte-film">
                    <img src="../assets/images/<?= htmlspecialchars($film['image']) ?>" alt="<?= htmlspecialchars($film['title']) ?>">
                    <h3><?= htmlspecialchars($film['title']) ?></h3>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="../index.php" class="bouton retour">Accueil</a>
</section>

<?php include '../includes/footer_bas_de_page.php'; ?>

</body>
</html>
