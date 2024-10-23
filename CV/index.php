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
$Pivot;
$stmt = $pdo->prepare('SELECT * FROM ProjectsUsers');
$stmt->execute();
$Pivot = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/static/index.css">
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
        .PPUser {
            max-height: 60px;
            max-width: 60px;
            border-radius: 100%;
        }
    </style>
</head>

<body>
    <header class="topnav">
        <div style="display: flex; align-items: center;">
            <input id="inputBar" placeholder="Search User, Specific Project if he exists" type="text">
            <button class="Search" aria-label="Search">
                <img src="./static/loupe.png" alt="Search icon" class="animated-image">
            </button>
        </div>
        <div style="display: flex;justify-content: center;flex-direction: column;align-items: center; color:white;">
            <? if (!isset($_COOKIE['UserTokenSession'])) : ?>
                <h2>Welcome To This WebSite</h2>
            <? else : ?>
                <h2>Welcome <? echo htmlspecialchars($personalInfo['First_name'] . " " . $personalInfo['Last_name']); ?></h2>
            <? endif; ?>

        </div>
        <button class="hamburger" type="button" aria-expanded="false" aria-controls="menu">
            <span class="hamburger-box">
                <img src="./static/menu.png" alt="Menu icon" class="animated-image">
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

    <p style="text-align: center;">You can found Here The different Project Created by our User, See their CVs, And Contact Them</p>

    <div class="main-gallery">
        <div class="main-column">
            <? foreach ($Users as &$user) { ?>
                <? if ($counter >= 10) { ?>
                    break;
                <? } ?>
                <a href="profil.php?id=<? echo $user['Id']; ?>" style="text-decoration: none; color: black">
                    <div class="main-gallery-project__block" id="<? echo $user['Id']; ?>">
                        <!-- Display project details here -->
                        <? if (!is_null($user['PP_User'])) : ?>
                            <img src="./ImageUpload/UserPP/<?php echo $user['PP_User'] ?>" alt="PP User" class="PPUser">
                        <? else : ?>
                            <img src="/static/user_Img.png" alt="PP User" class="PPUser">
                        <? endif; ?>
                        <h3><?php echo $user['First_name'], " ", $user['Last_name']; ?></h3>
                        <p style="text-align: center;"><?php echo $user['UserText']; ?></p>
                    </div>
                </a>
                <? $counter++ ?>
            <? } ?>
        </div>
        <div class="main-column">
            <? foreach ($Projects as &$project) { ?>
                <? if ($counter >= 10) { ?>
                    break;
                <? } ?>
                <a href="project.php?id=<? echo $project['Project_Id']; ?>" style="text-decoration: none; color: black">
                    <div class="main-gallery-project__block" id="<? echo $project['Project_Id']; ?>">
                        <!-- Display project details here -->
                        <div>
                            <div style="display: flex; flex-direction: row; align-items: center;">
                                <? if (!is_null($project['LinkImage'])) : ?>
                                    <img src="../ImageUpload/OtherImg/<?php echo $project['LinkImage'] ?>" alt="Search icon" class="animated-image">
                                <? else : ?>
                                    <img src="/static/logoYnov.jpg" alt="Search icon" class="animated-image">
                                <? endif; ?>
                                <h3 style="margin-left: 5px;"><?php echo $project['Title']; ?></h3>
                            </div>

                            <h6 style="margin-top: 5px;">Subjects: <?php echo $project['Subjects']; ?> by <?php $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE Projects_Id = ?');
                                                                                                            $stmt->execute([$project['Project_Id']]);
                                                                                                            $pivot = $stmt->fetch();
                                                                                                            $stmt = $pdo->prepare('SELECT First_name, Last_name FROM Users WHERE Id = ?');
                                                                                                            $stmt->execute([$pivot['Users_Id']]);
                                                                                                            $user = $stmt->fetch();
                                                                                                            echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']) ?></h6>
                            </h6>
                        </div>
                        <p style="text-align: center;"> Description : <?php echo $project['Description']; ?></p>
                    </div>
                </a>
            <? $counter++;
            } ?>
        </div>
    </div>

    <footer>
        <p>© 2024 YNOV</p>
        <a href="#" class="logo">
            <img alt="ynov campus" src="/static/YnovCampusLogo.png">
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