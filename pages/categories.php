<?php
session_start();
require_once '../config/database.php';

$allowed_categories = ['action', 'drama', 'comedy']; 
$category = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : null;

if (!in_array($category, $allowed_categories)) {
    $category = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - Anthony & Tiago's Movies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo a {
            color: white;
            text-decoration: none;
            font-size: 24px;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 15px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            list-style: none;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .category-section {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .movies-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .movie-card {
            background: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .movie-card img {
            max-width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 5px;
        }
        .movie-card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .movie-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        .movie-card .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .movie-card .btn:hover {
            background-color: #0056b3;
        }
    </style>
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
                    <span class="user-initials"><?= htmlspecialchars($_SESSION['initiales']); ?></span>
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
    </nav>
</header>

<section class="category-section">
    <?php if ($category): ?>
        <h1>Films de la catégorie : <?= ucfirst($category); ?></h1>
    <?php else: ?>
        <h1>Veuillez sélectionner une catégorie valide :</h1>
        <ul>
            <li><a href="categories.php?cat=action">Action</a></li>
            <li><a href="categories.php?cat=drama">Drame</a></li>
          
        </ul>
    <?php endif; ?>

    <div class="movies-container">
        <?php
        if ($category) {
            try {
                $query = "SELECT id, title, price, image FROM movies WHERE category = :category ORDER BY created_at DESC";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->execute();
                $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$movies) {
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
                echo "<p>Erreur de base de données : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        ?>
    </div>
</section>

</body>
</html>
