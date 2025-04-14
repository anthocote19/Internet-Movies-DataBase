<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$info = "";
$modifie = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ancien = trim($_POST['current_password']);
    $nouveau = trim($_POST['new_password']);
    $confirme = trim($_POST['confirm_password']);
    $id = $_SESSION['user_id'];

    if (strlen($nouveau) < 8) {
        $info = "Mot de passe trop court (8 caractères min).";
    } elseif ($nouveau !== $confirme) {
        $info = "Les mots de passe ne sont pas les mêmes.";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $utilisateur = $stmt->fetch();

        if (!$utilisateur || !password_verify($ancien, $utilisateur['password'])) {
            $info = "Mot de passe actuel incorrect.";
        } else {
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hash, $id])) {
                $info = "Mot de passe changé avec succès.";
                $modifie = true;
            } else {
                $info = "Erreur. Réessayez.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="dashboard.css?v=<?= time(); ?>">
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <h2>Changer le mot de passe</h2>

            <?php if (!empty($info)): ?>
                <p><?= htmlspecialchars($info); ?></p>
            <?php endif; ?>

            <?php if ($modifie): ?>
                <p>Vous allez être redirigé vers l'accueil dans 3 secondes...</p>
                <script src="../assets/js/mdp.js?v=<?= time(); ?>"></script>
            <?php else: ?>
                <form method="POST">
                    <label>Mot de passe actuel :</label>
                    <input type="password" name="current_password" required><br>

                    <label>Nouveau mot de passe :</label>
                    <input type="password" name="new_password" required><br>

                    <label>Confirme le nouveau :</label>
                    <input type="password" name="confirm_password" required><br>

                    <button type="submit">Changer</button>
                </form>
            <?php endif; ?>

            <a href="../index.php">Annuler et revenir</a><br>
            <a href="logout.php">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
