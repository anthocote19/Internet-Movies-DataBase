<?php
session_start();
require_once '../config/database.php';

$messageErreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['username']);
    $email = trim($_POST['email']);
    $motDePasse = $_POST['password'];

    if (!empty($nom) && !empty($email) && !empty($motDePasse)) {
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (username, email, password) VALUES (:nom, :email, :mdp)";
            $requete = $pdo->prepare($sql);
            $requete->execute([
                'nom' => $nom,
                'email' => $email,
                'mdp' => $motDePasseHash
            ]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $nom;

            
            $morceaux = explode(" ", $nom);
            $lettres = strtoupper(substr($morceaux[0], 0, 1));
            if (isset($morceaux[1])) {
                $lettres .= strtoupper(substr($morceaux[1], 0, 1));
            }
            $_SESSION['initiales'] = $lettres;

            header("Location: ../index.php");
            exit();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $messageErreur = "Ce nom d'utilisateur existe déjà.";
            } else {
                $messageErreur = "Une erreur est survenue : " . $e->getMessage();
            }
        }

    } else {
        $messageErreur = "Merci de remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="register.css?v=<?= time(); ?>">
</head>
<body>

<div class="register-wrapper">
    <div class="register-container">
        <h2>Inscription</h2>

        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Adresse email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>

        <?php if (!empty($messageErreur)) : ?>
            <p class="error"><?= $messageErreur; ?></p>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])) : ?>
            <a href="login.php">Déjà un compte ? Se connecter</a>
            <a href="../index.php">Retour à l'accueil</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
