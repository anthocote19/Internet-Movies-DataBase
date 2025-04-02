<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT movies.* FROM purchases JOIN movies ON purchases.movie_id = movies.id WHERE purchases.user_id = ?");
$stmt->execute([$user_id]);
$purchased_movies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos films achetés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-top: 20px;
            display: inline-block;
        }

        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div>
        <h2>Vos films achetés</h2>
        <ul>
            <?php foreach ($purchased_movies as $movie): ?>
                <li><?= htmlspecialchars($movie['title']) ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="logout.php">Déconnexion</a>
    </div>
</body>
</html>
