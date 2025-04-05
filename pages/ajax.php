<?php
session_start();
require_once '../config/database.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "Vous devez être connecté pour ajouter un film au panier.";
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['movie_id']) && is_numeric($_POST['movie_id'])) {
    $movie_id = intval($_POST['movie_id']);

    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND movie_id = ?");
        $stmt->execute([$user_id, $movie_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, movie_id, quantity, added_at) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$user_id, $movie_id]);
    }

    
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $totalItems = $stmt->fetchColumn();

    $response = [
        'success' => true,
        'message' => "Film ajouté au panier !",
        'total' => $totalItems
    ];
} else {
    $response['message'] = "ID de film invalide.";
}

echo json_encode($response);
exit;
