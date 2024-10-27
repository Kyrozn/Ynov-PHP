<?php
<<<<<<< HEAD
require_once './Router.php';

$router = new Router();

$voucherId = $_GET['id'] ?? null;

$router->dispatch($_SERVER['REQUEST_URI']);
=======
session_start();
require __DIR__ . '/Func/db.php';
$counter = 0;
if (isset($_COOKIE['UserTokenSession'])) {
    $userId = $_COOKIE['UserTokenSession'];
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
    <link rel="stylesheet" href="../static/index.css">
</head>

<body>
    <header class="topnav">
        <div style="display: flex; align-items: center;">
            <div>
                <input id="inputBar" placeholder="Search User, Specific Project if he exists" type="text" oninput="getLiveValue()">
                <div class="resultBox" style="position: absolute;">
                </div>
            </div>
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
                    <a href="/login" style="text-decoration: none; color: white;">
                        <li>Login</li>
                    </a>
                    <a href="/register" style="text-decoration: none; color: white;">
                        <li>Register</li>
                    </a>
                <? else : ?>
                    <a href="/profil" style="text-decoration: none; color: white;">
                        <li>Your Profil</li>
                    </a>
                    <a href="/project" style="text-decoration: none; color: white;">
                        <li>Your Projects</li>
                    </a>
                    <? if ($personalInfo['UserRole'] === "Admin") : ?>
                        <a href="/admin" style="text-decoration: none; color: white;">
                            <li>Admin Panel</li>
                        </a>
                    <? endif; ?>
                    <a href="/logout" style="text-decoration: none; color: white;">
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
                <? if ($counter >= 10) {
                    break;
                } ?>
                <a href="/profil?id=<?= $user['Id']; ?>" style="text-decoration: none; color: black">
                    <div class="main-gallery-project__block" id="<? echo $user['Id']; ?>">
                        <!-- Display project details here -->
                        <? if (!is_null($user['PP_User'])) : ?>
                            <img src="./ImageUpload/UserPP/<?php echo $user['PP_User'] ?>" alt="PP User" class="PPUser">
                        <? else : ?>
                            <img src="/ImageUpload/UserPP/user_Img.png" alt="PP User" class="PPUser">
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
                <? if ($counter >= 10) {
                    break;
                } ?>
                <? if ($project['Validate'] === 1) { ?>
                    <a href="/project?id=<? echo $project['Project_Id']; ?>" style="text-decoration: none; color: black">
                        <div class="main-gallery-project__block" id="<? echo $project['Project_Id']; ?>">
                            <!-- Display project details here -->
                            <div>
                                <div style="display: flex; flex-direction: row; align-items: center;">
                                    <? if (!is_null($project['LinkImage'])) : ?>
                                        <img src="../ImageUpload/OtherImg/<?php echo $project['LinkImage'] ?>" alt="Search icon" class="animated-image">
                                    <? else : ?>
                                        <img src="/ImageUpload/OtherImg/logoYnov.jpg" alt="Search icon" class="animated-image">
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
                <? } ?>
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
        const search = document.querySelector(".Search");
        document.addEventListener("DOMContentLoaded", function() {
            const button = document.querySelector(".hamburger");
            const nav = document.querySelector("nav");
            const inputbar = document.querySelector("#inputBar");


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

        function getLiveValue() {
            const inputValue = document.getElementById('inputBar').value.toLowerCase();
            const resultBox = document.querySelector('.resultBox');
            resultBox.innerHTML = '';

            if (inputValue) {
                <?php foreach ($Users as $user) : ?>
                    if ('<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'.toLowerCase().includes(inputValue)) {
                        const userDiv = document.createElement('div');
                        userDiv.classList.add('search-result');
                        userDiv.innerHTML = `
                            <a href="/profil?id=<?php echo $user['Id']; ?>" style="text-decoration: none; color: white; display: flex; align-items: center;">
                                <img src="./ImageUpload/UserPP/<?php echo $user['PP_User'] ?? 'user_Img.png'; ?>" class="PPUser" style="width:30px; height:30px" alt="PP User">
                                <h4>${'<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'}</h4>
                            </a>
                        `;
                        resultBox.appendChild(userDiv);
                    }
                <?php endforeach; ?>
                <?php foreach ($Projects as $project) : ?>
                    if ('<?php echo htmlspecialchars($project['Title'] . " " . $project['Subjects']); ?>'.toLowerCase().includes(inputValue)) {
                        const userDiv = document.createElement('div');
                        userDiv.classList.add('search-result');
                        userDiv.innerHTML = `
                            <a href="/project?id=<?php echo $project['Project_Id']; ?>" style="text-decoration: none; color: white; display: flex; align-items: center;">
                                <img src="./ImageUpload/OtherImg/<?php echo $project['LinkImage'] ?? 'user_Img.png'; ?>" class="PPUser" style="width:30px; height:30px" alt="PP User">
                                <h4>${'<?php echo htmlspecialchars($project['Title']); ?>'}</h4>
                            </a>
                        `;
                        resultBox.appendChild(userDiv);
                    }
                <?php endforeach; ?>
            }
        }
    </script>
</body>

</html>
>>>>>>> 703c37638d822a9e958750db068cfcd6febf6bfe
