<?php
session_start();
require_once '../config/database.php'; 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = "";
$password_updated = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    if (strlen($new_password) < 8) {
        $message = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password'])) {
            $message = "Mot de passe actuel incorrect.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $user_id])) {
                $message = "Mot de passe mis à jour avec succès.";
                $password_updated = true; 
            } else {
                $message = "Une erreur est survenue, veuillez réessayer.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <h2>Changer le mot de passe</h2>
            <?php if (!empty($message)): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (!$password_updated): ?>
                <form method="POST">
                    <label>Mot de passe actuel :</label>
                    <input type="password" name="current_password" required><br>

                    <label>Nouveau mot de passe :</label>
                    <input type="password" name="new_password" required><br>

                    <label>Confirmer le nouveau mot de passe :</label>
                    <input type="password" name="confirm_password" required><br>

                    <button type="submit">Modifier</button>
                </form>
            <?php else: ?>
                <a href="../index.php" class="btn">Retour à l'accueil</a>
            <?php endif; ?>

            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</body>
</html>
