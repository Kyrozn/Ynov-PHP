<?php
session_start();
require __DIR__ . '/../Func/db.php'; // Include the database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        // Rechercher l'utilisateur par Email
        $stmt = $pdo->prepare('SELECT Id, Password FROM Users WHERE Email = ?');
        $stmt->execute([$username]);
    } else {
        // Rechercher l'utilisateur par prénom et nom
        $nameParts = explode(" ", $username);
        if (count($nameParts) === 2) {
            $stmt = $pdo->prepare('SELECT Id, Password FROM Users WHERE First_name = ? AND Last_name = ?');
            $stmt->execute([$nameParts[0], $nameParts[1]]);
        } else {
            $error = "Invalid username format!";
        }
    }
    $admin = $stmt->fetch();

    // If the admin user is found, verify the password
    if ($admin && password_verify($password, $admin['Password'])) {
        // Mot de passe correct, connexion réussie
        setcookie("UserTokenSession", $admin['Id'], 0, '/', '', false, true);
        header("Location: /");
        exit;
    } else {
        // Si l'utilisateur n'existe pas ou que le mot de passe est incorrect
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../static/login.css">
</head>

<body>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <?php if (isset($error)): ?>
                    <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="POST" action="" class="login">
                    <div class="login__field">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="username" name="username" class="login__input" placeholder="User name / Email" required>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="login__input" placeholder="Password" required>
                    </div>
                    <button class="button login__submit">
                        <span class="button__text">Log In Now</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                </form>
            </div>
            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape4"></span>
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span>
            </div>
        </div>
    </div>
</body>

</html>