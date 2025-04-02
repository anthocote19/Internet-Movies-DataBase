<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user_id = $_SESSION['user_id'];

    // Récupérer le mot de passe actuel
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $message = "Mot de passe actuel incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Mettre à jour le mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $message = "Mot de passe mis à jour avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Changer le mot de passe</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <h2>Changer le mot de passe</h2>
            <?php if ($message): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="POST">
                <label>Mot de passe actuel :</label>
                <input type="password" name="current_password" required><br>

                <label>Nouveau mot de passe :</label>
                <input type="password" name="new_password" required><br>

                <label>Confirmer le nouveau mot de passe :</label>
                <input type="password" name="confirm_password" required><br>

                <button type="submit">Modifier</button>
            </form>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</body>
</html>

