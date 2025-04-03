<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}


$_SESSION['cart'] = [];
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