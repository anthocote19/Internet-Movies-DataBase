<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $password_hash])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;

            // Générer les initiales de l'utilisateur
            $name_parts = explode(" ", trim($username));
            $initiales = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
            $_SESSION['initiales'] = $initiales;

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="register.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="register-wrapper">
    <div class="register-container">
        <h2>Inscription</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php">Déjà inscrit ? Connecte-toi</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
