<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['movie_id'])) {
    $movie_id = intval($_POST['movie_id']); 

    if (!in_array($movie_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $movie_id; 
        echo json_encode(["success" => true, "message" => "Film ajouté au panier !"]);
    } else {
        echo json_encode(["success" => false, "message" => "Ce film est déjà dans votre panier."]);
    }
    exit;
}


echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout au panier."]);
exit;
?>
