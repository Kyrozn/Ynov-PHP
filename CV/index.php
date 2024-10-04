<?php
session_start();
require 'db.php';
$counter = 0;
if (isset($_COOKIE['UserTokenSession'])) {
    $userId = $_COOKIE['UserTokenSession'];
    // Assure-toi que l'ID est un entier pour évi!ter des injections SQL (par mesure de sécurité)
    if (isset($userId)) {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->execute([$userId]);
        $personalInfo = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM CV WHERE User_ID = ?');
        $stmt->execute([$userId]);
        $cvinfo = $stmt->fetch();
    }
}
$Projects;
$stmt = $pdo->prepare('SELECT * FROM Projects');
$stmt->execute();
$Projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
$Users;
$stmt = $pdo->prepare('SELECT * FROM Users');
$stmt->execute();
$Users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/ressources/index.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topnav {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px;
            max-height: 70px;
            position: relative;
        }

        .hamburger {
            padding: 15px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            outline: none;
        }

        nav {
            display: none;
            /* Hide by default */
            position: absolute;
            top: 11.3vh;
            right: 0;
            width: 150px;
            background-color: black;
            transition: max-height 0.5s ease;
        }

        nav.show {
            display: block;
            /* Show when toggled */
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            padding: 10px;
            color: white;
            border-bottom: 1px solid white;
            background-color: #333;
            opacity: 0;
            transform: translateY(-20px);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        nav.show ul li {
            opacity: 1;
            transform: translateY(0);
        }

        nav ul li:hover {
            background-color: green;
        }

        .Search {
            background-color: transparent;
            border: none;
            cursor: pointer;
            outline: none;
            height: auto;
            width: auto;
        }

        .animated-image {
            max-width: 40px;
            height: auto;
            display: block;
        }

        .Search.clicked .animated-image {
            transform: rotate(-90deg);
            transition: transform 0.5s ease, opacity 0.5s ease;
        }

        /* Search bar styling */
        #inputBar {
            width: 0;
            opacity: 0;
            border: none;
            padding: 10px;
            transition: width 0.5s ease, opacity 0.5s ease;
            background-color: white;
            border-radius: 5px;
        }

        /* When search is active */
        #inputBar.reveal {
            width: 200px;
            opacity: 1;
        }
    </style>
</head>

<body>
    <header class="topnav">
        <div style="display: flex; align-items: center;">
            <input id="inputBar" placeholder="Search User, Specific Project if he exists" type="text">
            <button class="Search" aria-label="Search">
                <img src="./ressources/loupe.png" alt="Search icon" class="animated-image">
            </button>
        </div>
        <button class="hamburger" type="button" aria-expanded="false" aria-controls="menu">
            <span class="hamburger-box">
                <img src="./ressources/menu.png" alt="Menu icon" class="animated-image">
            </span>
        </button>

        <!-- Menu déroulant -->
        <nav id="menu">
            <ul>
                <? if (!isset($_COOKIE['UserTokenSession'])) : ?>
                    <a href="login.php" style="text-decoration: none; color: white;">
                        <li>Login</li>
                    </a>
                    <a href="register.php" style="text-decoration: none; color: white;">
                        <li>Register</li>
                    </a>
                    <li>Lien n°3</li>
                <? else : ?>
                    <a href="profil.php" style="text-decoration: none; color: white;">
                        <li>Your Profil</li>
                    </a>
                    <a href="project.php" style="text-decoration: none; color: white;">
                        <li>Your Projects</li>
                    </a>
                    <a href="logout.php" style="text-decoration: none; color: white;">
                        <li>Logout</li>
                    </a>
                <? endif; ?>
            </ul>
        </nav>
    </header>

    <div style="display: flex;justify-content: center;flex-direction: column;align-items: center;">
        <h2>Welcome To This WebSite</h2>
        <p>You can found Here The different Project Created by our User, See their CVs, And Contact Them</p>
    </div>

    <div class="main-gallery">
        <div class="main-column">
            <? foreach ($Users as &$user) { ?>
                <? if ($counter >= 10) { ?>
                    break;
                <? } ?>
                <div class="main-gallery-project__block" id="<? echo $user['Id']; ?>">
                    <!-- Display project details here -->
                    <? if (!is_null($user['PP_User'])) : ?>
                        <img src=<?php echo $user['PP_User'] ?> alt="PP User" class="PPUser">
                    <? else : ?>
                        <img src="/ressources/user_Img.png" alt="PP User" class="PPUser">
                    <? endif; ?>
                    <h3><?php echo $user['First_name'], " ", $user['Last_name']; ?></h3>
                    <p style="text-align: center;"><?php echo $user['UserText']; ?></p>
                </div>
                <? $counter++ ?>
            <? } ?>
        </div>
        <div class="main-column">
            <? foreach ($Projects as &$project) { ?>
                <? if ($counter >= 10) { ?>
                    break;
                <? } ?>
                <div class="main-gallery-project__block" id="<? echo $project['Project_Id']; ?>">
                    <!-- Display project details here -->
                    <h6></h6>
                    <h3><?php echo $project['Title']; ?></h3>
                    <h4>by <?php $stmt = $pdo->prepare('SELECT First_name, Last_name FROM Users WHERE Id = ?');
                            $stmt->execute([$project['User_ID']]);
                            $user = $stmt->fetch();
                            echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']) ?></h4>
                    <? if (!is_null($project['LinkImage'])) : ?>
                        <img src=<?php echo $project['LinkImage'] ?> alt="Search icon" class="animated-image">
                    <? else : ?>
                        <img src="/ressources/logoYnov.jpg" alt="Search icon" class="animated-image">
                    <? endif; ?>
                    <p style="text-align: center;"><?php echo $project['Description']; ?></p>
                </div>
            <? $counter++;
            } ?>
        </div>
    </div>

    <footer>
        <p>© 2024 YNOV</p>
        <a href="#" class="logo">
            <img alt="ynov campus" src="/ressources/YnovCampusLogo.png">
        </a>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const button = document.querySelector(".hamburger");
            const nav = document.querySelector("nav");
            const inputbar = document.querySelector("#inputBar");
            const search = document.querySelector(".Search");

            button.addEventListener("click", function() {
                nav.classList.toggle("show");
                const isExpanded = nav.classList.contains("show");
                button.setAttribute("aria-expanded", isExpanded);
            });

            search.addEventListener("click", function() {
                search.classList.toggle("clicked");
                inputbar.classList.toggle("reveal");
            });
        });
    </script>
</body>

</html>