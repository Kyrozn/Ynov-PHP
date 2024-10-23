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
    $pivot;
    $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE Projects_Id = ?');
    $stmt->execute([$voucherId]);
    $pivot = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$pivot[0]['Users_Id']]);
    $TeamInfo = $stmt->fetchAll();;
    $stmt = $pdo->prepare('SELECT * FROM Projects WHERE Project_Id = ?');
    $stmt->execute([$pivot[0]['Projects_Id']]);
    $projectInfo = $stmt->fetch();
} else if (isset($_COOKIE['UserTokenSession'])) {
    $voucherId = $_COOKIE['UserTokenSession'];

    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$voucherId]);
    $personalInfo = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE Users_Id = ?');
    $stmt->execute([$voucherId]);
    $pivot = $stmt->fetchAll();

    for ($i = 0; $i < count($pivot); $i++) {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->execute([$pivot[$i]['Users_Id']]);
        $TeamInfo = $stmt->fetchAll();
    }
    for ($i = 0; $i < count($pivot); $i++) {
        $stmt = $pdo->prepare('SELECT * FROM Projects WHERE Project_Id = ?');
        $stmt->execute([$pivot[$i]['Projects_Id']]);
        $projectInfo = $stmt->fetchAll();
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <link rel="stylesheet" href="./static/styles.css">
</head>

<? if (isset($_GET['id'])) : ?>

    <body>
        <header style="justify-content: space-around;" class="topnav">
            <h1 style="text-align:center; color:white;">Project Information : <? echo $projectInfo['Title'] ?? "" ?></h1>
            <a style="text-decoration:none; color:white" href="http://localhost:5050">HOME</a>
        </header>

        <div class="containerProject">
            <h5>Subjects : <? echo $projectInfo['Subjects'] ?? "" ?></h5>
            <div class="container">
                <div>
                    <? if (isset($projectInfo['LinkImage'])) : ?>
                        <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $projectInfo['LinkImage'] ?>">
                    <? else: ?>
                        <img src="./static/logoYnov.jpg">
                    <? endif; ?>
                    <h2 style="text-align: center;">Description</h2>
                    <h3><? echo $projectInfo['Description'] ?? "" ?></h3>
                </div>

                <div>
                    <h2>Collaborateur</h2>
                    <? foreach ($TeamInfo as $team) { ?>
                        <a style="color:black;text-decoration:none;" href="profil.php?id=<? echo $team['Id']; ?>">
                            <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;"><? echo $team['First_name'] ?? "" ?> <? echo $team['Last_name'] ?? "" ?></h4>
                        </a>
                    <? } ?>
                </div>
            </div>
        </div>
    </body>
<? elseif (isset($_COOKIE['UserTokenSession'])): ?>

    <body>
        <header style="justify-content: space-around;" class="topnav">
            <h1 style="text-align:center; color:white;"><? echo htmlspecialchars($personalInfo['First_name'] . " " . $personalInfo['Last_name']) ?> Differents Projet</h1>
            <a style="text-decoration:none; color:white" href="http://localhost:5050">HOME</a>
        </header>
        <? foreach ($projectInfo as $project) : ?>
            <div class="containerProject">
                <h5>Subjects : <? echo $projectInfo['Subjects'] ?? "" ?></h5>
                <div class="container">
                    <div>
                        <? if (isset($project['LinkImage'])) : ?>
                            <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $project['LinkImage'] ?>">
                        <? else: ?>
                            <img src="./static/logoYnov.jpg">
                        <? endif; ?>
                        <h2 style="text-align: center;">Description</h2>
                        <h3><? echo $project['Description'] ?? "" ?></h3>
                    </div>

                    <div>
                        <h2>Collaborateur</h2>
                        <? foreach ($TeamInfo as $team) { ?>
                            <a style="color:black;text-decoration:none;" href="profil.php?id=<? echo $team['Id']; ?>">
                                <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;"><? echo $team['First_name'] ?? "" ?> <? echo $team['Last_name'] ?? "" ?></h4>
                            </a>
                        <? } ?>
                    </div>
                </div>
                <?
                if ($project['Validate'] === 0) {
                    echo htmlspecialchars("Waiting for admin validation");
                }
                ?>
            </div>
        <? endforeach ?>
    </body>
<? endif; ?>

</html>