<?php
session_start();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id || (empty($_SESSION['cart']) && !hasCartInDatabase($user_id, $pdo))) {
    http_response_code(400);
    echo "<h1>Erreur</h1><p>Vous devez être connecté et avoir un panier rempli pour finaliser l'achat.</p>";
    exit();
}

try {
    $pdo->beginTransaction();

    $cart = [];

    
    if (!empty($_SESSION['cart'])) {
        $cart = $_SESSION['cart'];
    } else {
        
        $stmt = $pdo->prepare("SELECT movie_id, quantity FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
        $stmt->execute([$user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $cart[$row['movie_id']] = $row['quantity'];
        }
    }

  
    $stmtUpdateCart = $pdo->prepare("
        UPDATE cart 
        SET purchased_at = NOW(), is_active = 0 
        WHERE user_id = ? AND movie_id = ?
    ");


    $stmtInsertPurchase = $pdo->prepare("
        INSERT INTO purchases (user_id, movie_id, quantity, purchase_date)
        VALUES (?, ?, ?, NOW())
    ");

    foreach ($cart as $movie_id => $quantity) {
        $stmtUpdateCart->execute([$user_id, $movie_id]);
        $stmtInsertPurchase->execute([$user_id, $movie_id, $quantity]);
    }

 
    $_SESSION['cart'] = [];

    $pdo->commit();

    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Merci pour votre achat !</title>
        <link rel="stylesheet" href="chekout.css?v=<?php echo time(); ?>">
    </head>
    <body>
        <div class="container">
            <h1>Merci pour votre achat !</h1>
            <p>Votre commande a été traitée avec succès.</p>
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
    echo "<h1>Erreur</h1><p>Une erreur est survenue lors de l'achat : " . htmlspecialchars($e->getMessage()) . "</p>";
}

function hasCartInDatabase($user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ? AND is_active = 1 AND purchased_at IS NULL");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() > 0;
}
?>
