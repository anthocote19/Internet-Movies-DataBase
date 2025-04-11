<?php
session_start();
require_once '../config/database.php';

$utilisateurId = $_SESSION['user_id'] ?? null;


if (!$utilisateurId || (empty($_SESSION['cart']) && !panierExistantEnBase($utilisateurId, $pdo))) {
    http_response_code(400);
    echo "<h1>Erreur</h1><p>Vous devez être connecté et avoir un panier rempli pour finaliser l'achat.</p>";
    exit();
}

try {
    $pdo->beginTransaction();

    $panier = [];


    if (!empty($_POST['movie_id']) && is_numeric($_POST['movie_id'])) {
        $idFilm = (int) $_POST['movie_id'];
        $quantite = (int) ($_POST['quantity'] ?? 1);
        $panier[$idFilm] = $quantite;
    } else {
       
        if (!empty($_SESSION['cart'])) {
            $panier = $_SESSION['cart'];
        } else {
            $sql = "SELECT movie_id, quantity FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$utilisateurId]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
                $panier[$ligne['movie_id']] = $ligne['quantity'];
            }
        }
    }

   
    $majPanier = $pdo->prepare("UPDATE cart SET purchased_at = NOW(), is_active = 0 WHERE user_id = ? AND movie_id = ?");
    $ajoutAchat = $pdo->prepare("INSERT INTO purchases (user_id, movie_id, quantity, purchase_date) VALUES (?, ?, ?, NOW())");

    
    foreach ($panier as $idFilm => $quantite) {
        $majPanier->execute([$utilisateurId, $idFilm]);
        $ajoutAchat->execute([$utilisateurId, $idFilm, $quantite]);
    }

    $_SESSION['cart'] = [];
    $pdo->commit();
    ?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Merci pour votre achat !</title>
        <link rel="stylesheet" href="finalisation_achat.css?v=<?= time(); ?>">
    </head>
    <body>
        <div class="container">
            <h1>Merci pour votre achat !</h1>
            <p>Votre commande a bien été prise en compte.</p>
            <div class="buttons">
                <a href="../index.php" class="btn">Retour à l'accueil</a>
                <a href="./categories.php" class="btn">Poursuivre mes achats</a>
            </div>
        </div>
    </body>
    </html>

    <?php
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo "<h1>Erreur</h1><p>Une erreur est survenue : " . htmlspecialchars($e->getMessage()) . "</p>";
}


function panierExistantEnBase($utilisateurId, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
    $stmt->execute([$utilisateurId]);
    return $stmt->fetchColumn() > 0;
}
?>
