<?php
session_start();
require 'db.php'; // Include the database connection

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
        header("Location: index.php");
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
    <link rel="stylesheet" href="/ressources/styles.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Email/UserName:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>