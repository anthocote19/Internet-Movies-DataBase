<?php
session_start();
require_once '../config/database.php';

$allowed_categories = ['action', 'drama', 'comedy']; // Catégories autorisées

$category = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : '';

if (!in_array($category, $allowed_categories)) {
    $category = '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - Anthony & Tiago's Movies</title>
    <link rel="stylesheet" href="categories.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="categories.php">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= $_SESSION['initiales']; ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php">Mon Profil</a></li>
                        <li><a href="cart.php">Voir mon panier (<span id="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>)</a></li>
                        <li><a href="dashboard.php">Changer mot de passe</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle">☰</button>
    </nav>
</header>

<br>
<br>
<br>
<section class="category-section">
    <h1>Films de la catégorie : <?php echo ucfirst($category); ?></h1>

    <?php if (!$category): ?>
        <p>Veuillez sélectionner une catégorie valide :</p>
        <ul>
            <li><a href="categories.php?cat=action">Action</a></li>
            <li><a href="categories.php?cat=drama">Drame</a></li>
        </ul>
    <?php else: ?>
        <div class="movies-container">
            <?php
            try {
                $query = "SELECT id, title, price, image FROM movies WHERE category = :category ORDER BY created_at DESC";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->execute();
                $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($movies)) {
                    echo "<p>Aucun film trouvé dans cette catégorie.</p>";
                } else {
                    foreach ($movies as $row) {
                        $id = htmlspecialchars($row['id']);
                        $title = htmlspecialchars($row['title']);
                        $price = htmlspecialchars($row['price']);
                        $image = htmlspecialchars($row['image']);
                        echo "
                            <div class='movie-card'>
                                <img src='../assets/images/$image' alt='$title'>
                                <h3>$title</h3>
                                <p>$price €</p>
                                <a href='movie_details.php?id=$id' class='btn'>Voir Détails</a>
                                <a href='cart.php?add=$id' class='btn'>Ajouter au panier</a>
                            </div>
                        ";
                    }
                }
            } catch (PDOException $e) {
                echo "<p>Erreur de base de données : " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>