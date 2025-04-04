<?php
session_start();
require_once '../config/database.php'; 

if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO purchases (user_id, movie_id, purchase_date) VALUES (:user_id, :movie_id, NOW())");

    foreach ($_SESSION['cart'] as $movie_id) {
        $stmt->execute([
            'user_id' => $user_id,
            'movie_id' => $movie_id
        ]);
    }

    $pdo->commit(); 
    $_SESSION['cart'] = [];

} catch (Exception $e) {
    $pdo->rollBack(); 
    die("Erreur lors de l'achat : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande passée</title>
    <link rel="stylesheet" href="chekout.css">
</head>
<body>

<section class="checkout">
    <h1>Merci pour votre achat !</h1>
    <p>Votre commande a bien été enregistrée et est en cours de traitement.</p>
    <a href="../index.php" class="btn">Retour à l'accueil</a>
</section>

</body>
</html>
