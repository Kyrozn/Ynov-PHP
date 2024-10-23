<?php
session_start();
require 'db.php'; // Include the database connection
require_once('function.php');

// Initialize error variable
$error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the select statement to check if the email is used
    $select = $pdo->prepare('SELECT COUNT(Email) FROM Users WHERE Email = ?');
    $select->execute([$email]);
    $emailUsed = $select->fetchColumn();

    // If the email is not used, insert the new user
    if ($emailUsed == 0) {
        $uuidGenerated = guidv4();
        $stmt = $pdo->prepare('INSERT INTO Users(Id, First_name, Last_name, Email, Password, UserRole) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$uuidGenerated, $Fname, $Lname, $email, password_hash($password, PASSWORD_DEFAULT), "SimpleUser"]);

        setcookie("UserTokenSession", $uuidGenerated, 0, '/', '', false, true);
        header("Location: index.php");
        exit(); // Ensure no further code is executed after redirect
    } else {
        $error = "An account is already registered with this email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="/static/login.css">
</head>

<body>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <?php if (!empty($error)): ?>
                    <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="POST" action="" class="login">
                    <div class="login__field">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="Fname" name="Fname" placeholder="Prenom" required>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="text" id="Lname" name="Lname" placeholder="Nom" required>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button class="button login__submit">
                        <span class="button__text">Register Now</span>
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