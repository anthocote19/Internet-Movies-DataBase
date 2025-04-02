<?php

$host = 'localhost';
$port = 3307;  // Ajout du port correct
$dbname = 'project 1phpd films';  // Vérifie si le nom est exact (évite les espaces)
$username = 'root';  // Vérifie si c'est bien 'root'
$password = '';  // Laisse vide si aucun mot de passe n'est défini

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
