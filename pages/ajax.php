<?php
session_start();
require_once '../config/database.php';

$response = ['success' => false, 'message' => ''];

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    $response['message'] = "Vous devez être connecté pour ajouter un film au panier.";
    echo json_encode($response);
    exit();
}

// Initialiser le panier si non défini
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['movie_id'])) {
    $movie_id = intval($_POST['movie_id']);

    if (!in_array($movie_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $movie_id;
        $response = ["success" => true, "message" => "Film ajouté au panier !"];
    } else {
        $response = ["success" => false, "message" => "Ce film est déjà dans votre panier."];
    }
} else {
    $response = ["success" => false, "message" => "Erreur lors de l'ajout au panier."];
}

echo json_encode($response);
exit;
