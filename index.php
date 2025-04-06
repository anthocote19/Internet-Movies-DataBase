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
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">Anthony's & Tiago's Movies</a>
        </div>
        <button class="menu-toggle" aria-label="Menu mobile">☰</button>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="pages/categories.php" class="<?= ($current_page == 'categories.php') ? 'active' : '' ?>">Catégories</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown">
                    <span class="user-initials"><?= $_SESSION['initiales']; ?></span>
                    <ul class="dropdown-menu">
                        <li><a href="pages/profile.php">Mon Profil</a></li>
                        <li><a href="pages/cart.php">Voir mon panier (<span id="cart-count">
                            <?php
                            $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            echo $stmt->fetchColumn() ?? 0;
                            ?>
                        </span>)</a></li>
                        <li><a href="pages/dashboard.php">Changer mot de passe</a></li>
                        <li><a href="pages/logout.php">Déconnexion</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="pages/login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Connexion</a></li>
                <li><a href="pages/register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="hero">
    <h1>Bienvenue sur Anthony's and Tiago's Movies</h1>
    <p>Découvrez, recherchez et achetez vos films préférés en quelques clics.</p>
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
        $query = "SELECT id, title, price, image, trailer_url FROM movies ORDER BY RAND() LIMIT 5";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = htmlspecialchars($row['id']);
            $title = htmlspecialchars($row['title']);
            $price = htmlspecialchars($row['price']);
            $image = htmlspecialchars($row['image']);
            $trailer_url = htmlspecialchars($row['trailer_url'] ?? '');

            echo "
                <div class='movie-card'>
                    <img src='assets/images/$image' alt='$title'>
                    <h3>$title</h3>
                    <p>$price €</p>
                    <a href='pages/movie_details.php?id=$id' class='btn'>Voir Détails</a>
                    <button class='btn add-to-cart' data-id='$id'>Ajouter au panier</button>";
            
            if (!empty($trailer_url)) {
                echo "<a href='$trailer_url' target='_blank' class='btn'>Voir le trailer</a>";
            } else {
                echo "<button class='btn' disabled>Aucun trailer</button>";
            }

            echo "</div>";
        }
        ?>
    </div>
</section>

<section class="categories">
    <h2>Nos Catégories</h2>
    <div class="category-list">
        <a href="pages/categories.php?cat=action" class="category-card action">Action</a>
        <a href="pages/categories.php?cat=drama" class="category-card drama">Drame</a>
    </div>
</section>

<div id="cart-message" class="hidden"></div>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('menu-active');
    });

    $(document).ready(function() {
        $(".add-to-cart").click(function() {
            var movieId = $(this).data("id");

            $.ajax({
                url: "pages/ajax.php",
                type: "POST",
                data: { movie_id: movieId },
                dataType: "json",
                success: function(response) {
                    $("#cart-message").text(response.message).fadeIn().delay(1500).fadeOut();
                    if (response.success && response.total !== undefined) {
                        $("#cart-count").text(response.total);
                    }
                },
                error: function() {
                    $("#cart-message").text("Erreur lors de l'ajout au panier.").fadeIn().delay(1500).fadeOut();
                }
            });
        });

    
        $(".user-initials").on("click", function(e) {
            e.stopPropagation();
            $(this).siblings(".dropdown-menu").slideToggle();
        });

        $(document).on("click", function() {
            $(".dropdown-menu").slideUp();
        });
    });
</script>

<style>
    #cart-message {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        display: none;
        z-index: 1000;
    }
    .hidden { display: none; }
</style>
<br><br>
<?php include 'includes/footer.php'; ?>
<script src="./cart.js?v=<?= time(); ?>"></script>

</body>
</html>
