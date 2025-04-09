<?php
session_start();
require_once '../config/database.php'; 

$categorie = $_GET['cat'] ?? null; 

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetchColumn();
} else {
    $cart_count = array_sum($_SESSION['cart'] ?? []);
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - Anthony & Tiago's Movies</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <script defer src="../assets/js/categ.js?v=<?= time(); ?>"></script>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>

        <button class="menu-toggle" aria-label="Menu mobile">☰</button>

        <ul class="nav-links">
            <li><a href="../index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="categories.php" class="<?= $currentPage === 'categories.php' ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= htmlspecialchars($_SESSION['initiales'] ?? '?'); ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="profil_de_l'util.php">Mon Profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<span id="cart-count"><?= $cart_count ?></span>)</a></li>
                        <li><a href="dashboard.php">Changer mot de passe</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<br>
<br>
<main>
<section class="section-categorie">
    <?php if ($categorie): ?>
        <h1>Films de la catégorie : <?= ucfirst($categorie); ?></h1>
    <?php else: ?>
        <h1>Choisissez la catégorie qui vous convient le plus !</h1>
        <ul class="liste-categories">
            <li><a href="categories.php?cat=action">Action</a></li>
            <li><a href="categories.php?cat=drama">Drame</a></li>
        </ul>
    <?php endif; ?>

    <div class="grille-films">
        <?php
        if ($categorie) {
            try {
                $requete = "SELECT id, title, price, image FROM movies WHERE category = :categorie ORDER BY created_at DESC";
                $stmt = $pdo->prepare($requete);
                $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
                $stmt->execute();
                $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$films) {
                    echo "<p class='message-erreur'>Aucun film trouvé dans cette catégorie.</p>";
                } else {
                    foreach ($films as $film) {
                        $id = htmlspecialchars($film['id']);
                        $titre = htmlspecialchars($film['title']);
                        $prix = htmlspecialchars($film['price']);
                        $image = htmlspecialchars($film['image']);

                        echo "
                            <div class='carte-film'>
                                <img src='../assets/images/$image' alt='$titre'>
                                <h3>$titre</h3>
                                <p>$prix €</p>
                                <a href='detailsdes_films.php?id=$id' class='btn-action'>Voir Détails</a>";
                                
                        if (isset($_SESSION['user_id'])) {
                            echo "<button class='btn-action ajout-panier' data-id='$id'>Ajouter au panier</button>";
                        } else {
                            echo "<p class='texte-non-connecte'><a href='login.php'>Connectez-vous</a> pour ajouter ce film au panier</p>";
                        }

                        echo "</div>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p class='message-erreur'>Erreur lors de la récupération des films : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        ?>
    </div>

    <div class="zone-retour">
        <a href="../index.php" class="bouton-retour">Retourner à l'accueil</a>
    </div>
</section>
</main>
<?php include '../includes/footer_bas_de_page.php'; ?>
</body>
</html>
