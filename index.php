<?php
session_start();
require_once 'config/database.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anthony's and Tiago's Movies</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">Anthony & Tiago's Movies</a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="pages/categories.php" class="<?= ($current_page == 'categories.php') ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="pages/profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">Mon Profil</a></li>
                <li><a href="pages/logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="pages/login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Connexion</a></li>
                <li><a href="pages/register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle">☰</button>
    </nav>
</header>

<section class="hero">
    <h1>Bienvenue sur Anthony's and Tiago's Movies</h1>
    <p>Découvrez, recherchez et achetez vos films préférés en quelques clics.</p>
    <video autoplay loop muted class="background-video">
        <source src="assets/videos/trailer.mp4" type="video/mp4">
    </video>
</section>

<section class="search">
    <h2>Rechercher un film</h2>
    <form action="pages/search.php" method="GET">
        <input type="text" name="q" placeholder="Rechercher par titre ou réalisateur..." required>
        <button type="submit">Rechercher</button>
    </form>
</section>

<section class="latest-movies">
    <h2>Derniers Films Ajoutés</h2>
    <div class="movies-container">
        <?php
        $query = "SELECT id, title, price, image FROM movies ORDER BY RAND() LIMIT 5";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = htmlspecialchars($row['id']);
            $title = htmlspecialchars($row['title']);
            $price = htmlspecialchars($row['price']);
            $image = htmlspecialchars($row['image']);
            echo "
                <div class='movie-card'>
                    <img src='assets/images/$image' alt='$title'>
                    <h3>$title</h3>
                    <p>$price €</p>
                    <a href='pages/movie_details.php?id=$id' class='btn'>Voir Détails</a>
                    <a href='pages/cart.php?add=$id' class='btn'>Ajouter au panier</a>
                </div>
            ";
        }
        ?>
    </div>
</section>

<section class="categories">
    <h2>Nos Catégories</h2>
    <div class="category-list">
        <a href="pages/categories.php?cat=comedy" class="category-card comedy">Comédie</a>
        <a href="pages/categories.php?cat=action" class="category-card action">Action</a>
        <a href="pages/categories.php?cat=drama" class="category-card drama">Drame</a>
    </div>
</section>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
<br>
<br>
<?php include 'includes/footer.php'; ?>

</body>
</html>
