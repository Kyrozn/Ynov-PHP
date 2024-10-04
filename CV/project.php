<?php
session_start();
require 'db.php'; // Include the database connection

// index.php
$requestUri = $_SERVER['REQUEST_URI'];

// Remove any query string from the URL
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Match routes
if (isset($_GET['id'])) {
    $voucherId = $_GET['id'];
    echo "project details for ID " . htmlspecialchars($voucherId);
} else {
    echo "No voucher ID provided.";
}

$personalInfo;
$cvinfo;
// Check if the user is logged in as admin
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
if (isset($_COOKIE['UserTokenSession'])) {
    $userId = $_COOKIE['UserTokenSession'];

    // Assure-toi que l'ID est un entier pour éviter des injections SQL (par mesure de sécurité)
    if (isset($userId)) {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->execute([$userId]);
        $personalInfo = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM CV WHERE User_ID = ?');
        $stmt->execute([$userId]);
        $cvinfo = $stmt->fetch();
    } else {
        echo "Invalid UserTokenSession";
    }
} else {
    header("Location: index.php");
}


// Handle form submission and update the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = $_POST['F_name'];
    $Lname = $_POST['L_name'];
    $email = $_POST['Email'];

    // Update personal information in the database
    $stmt = $pdo->prepare('UPDATE Users SET First_name = ?, Last_name = ?, Email = ? WHERE Id = ?');
    $stmt->execute([$Fname, $Lname, $email, $personalInfo['Id']]);
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Vérifier si le fichier a été téléchargé sans erreur
    if ($file['error'] == 0) {
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileDestination = 'uploads/' . $fileName; // Dossier où stocker les fichiers

        // Déplacer le fichier vers son emplacement final
        if (move_uploaded_file($fileTmp, $fileDestination)) {
            echo "File uploaded successfully";
        } else {
            echo "Error during file upload";
        }
    } else {
        echo "File upload error: " . $file['error'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Vitae</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <?php if (isset($personalInfo)): ?>
            <header>
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <button type="submit">Enregistrer</button>
                </form>
                <h1><?php echo $personalInfo['First_name'] ?></h1>
                <p><?php echo $personalInfo['Last_name'] ?></p>
                <p>Email: <?php echo $personalInfo['Email']; ?> | Phone: <a href="tel:<? $personalInfo['PhoneNB']; ?>"><?php echo $personalInfo['PhoneNB']; ?></a></p>
                <button id="editBtn">Edit Personal Info</button>
                <a href="logout.php">Logout</a>
            </header>
        <?php else: ?>
            <header>
                <h1>You are not connected</h1>
                <p>You can connect to your account <a href="login.php" style="color:blue; text-decoration: underline">here</a></p>
                <p>You can create an account <a href="register.php" style="color:blue; text-decoration: underline">here</a></p>
            </header>
        <?php endif; ?>


        <!-- Profile Section -->
        <?php if (isset($personalInfo) && !empty($personalInfo)): ?>
            <section class="profile">
                <h2>Profile</h2>
                <p><?php echo $personalInfo['UserText']; ?></p>
                <!--<a href="CV.php">Acceder A Votre CV</a>-->
            </section>
        <?php else: ?>
            <section class="profile">
                <h2>No Profile Found</h2>
            </section>
        <?php endif; ?>
        <!-- Modal for updating personal information (visible only for admin) -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Personal Information</h2>
                <form method="POST" action="">
                    <label for="F_name">First Name:</label>
                    <input type="text" id="F_name" name="F_name" value="<?php echo $personalInfo['First_name']; ?>" required>
                    <label for="L_name">Last Name:</label>
                    <input type="text" id="L_name" name="L_name" value="<?php echo $personalInfo['Last_name']; ?>" required>

                    <!--<label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $personalInfo['title']; ?>" required>-->

                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" value="<?php echo $personalInfo['Email']; ?>" required>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $personalInfo['PhoneNB']; ?>" required>

                    <label for="UserText">Profile Description:</label> 
                    <textarea id="UserText" name="UserText" required><?php echo $personalInfo['UserText']; ?></textarea>

                    <input type="submit" value="Save Changes">
                </form>
            </div>
        </div>
    </div>

    <script>
        // Get modal and elements
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("editBtn");
        var span = document.getElementsByClassName("close")[0];

        // Open the modal when the edit button is clicked
        if (btn) {
            btn.onclick = function() {
                modal.style.display = "block";
            }
        }

        // Close the modal when the 'x' is clicked
        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        // Close the modal if the user clicks outside the modal content
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>