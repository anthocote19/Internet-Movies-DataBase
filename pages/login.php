<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format d'email invalide.";
        } else {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = htmlspecialchars($user['username']);

                // Initiales de l'utilisateur
                $name_parts = explode(" ", trim($user['username']));
                $initiales = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
                $_SESSION['initiales'] = $initiales;

                // Charger le panier depuis la base de données
                $_SESSION['cart'] = [];
                $stmt = $pdo->prepare("SELECT movie_id, quantity FROM cart WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cart_items as $item) {
                    $_SESSION['cart'][$item['movie_id']] = $item['quantity'];
                }

                header("Location: ../index.php");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="login-wrapper">
    <div class="login-container">
        <h2>Connexion</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <a href="register.php">Pas encore inscrit ? Inscris-toi</a>
    </div>
</div>

</body>
</html>
