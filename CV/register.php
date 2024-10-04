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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="Fname">First Name:</label>
            <input type="text" id="Fname" name="Fname" required>

            <label for="Lname">Last Name:</label>
            <input type="text" id="Lname" name="Lname" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
