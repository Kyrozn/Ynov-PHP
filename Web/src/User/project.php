<?php
session_start();
require_once __DIR__ . '/../Func/db.php'; // Include the database connection

// index.php
$requestUri = $_SERVER['REQUEST_URI'];

// Remove any query string from the URL
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Match routes
if (isset($_GET['id'])) {
    $voucherId = $_GET['id'];
    $pivot;
    $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE Project_Id = ?');
    $stmt->execute([$voucherId]);
    $pivot = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$pivot[0]['User_ID']]);
    $TeamInfo = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT * FROM Projects WHERE Project_Id = ?');
    $stmt->execute([$pivot[0]['Project_Id']]);
    $projectInfo = $stmt->fetch();
} else if (isset($_COOKIE['UserTokenSession'])) {
    $voucherId = $_COOKIE['UserTokenSession'];

    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$voucherId]);
    $personalInfo = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE User_ID = ?');
    $stmt->execute([$voucherId]);
    $pivot = $stmt->fetchAll();

    for ($i = 0; $i < count($pivot); $i++) {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->execute([$pivot[$i]['User_ID']]);
        $TeamInfo = $stmt->fetchAll();
    }
    for ($i = 0; $i < count($pivot); $i++) {
        $stmt = $pdo->prepare('SELECT * FROM Projects WHERE Project_Id = ?');
        $stmt->execute([$pivot[$i]['Project_Id']]);
        $projectInfo = $stmt->fetchAll();
    }
}
$stmt = $pdo->prepare('SELECT * FROM Users');
$stmt->execute();
$AllUsers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <link rel="stylesheet" href="../../static/project.css">
</head>

<? if (isset($_GET['id'])) : ?>

    <body>
        <header style="justify-content: space-around;" class="topnav">
            <h1 style="text-align:center; color:white;">Project Information : <? echo $projectInfo['Title'] ?? "" ?></h1>
            <a style="text-decoration:none; color:white" href="/">HOME</a>
        </header>

        <div class="containerProject">
            <h5>Subjects : <? echo $projectInfo['Subjects'] ?? "" ?></h5>
            <div class="container">
                <div>
                    <? if (isset($projectInfo['LinkImage'])) : ?>
                        <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $projectInfo['LinkImage'] ?>">
                    <? else: ?>
                        <img src="./ImageUpload/OtherImg/logoYnov.jpg">
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
            <a style="text-decoration:none; color:white" href="/">HOME</a>
        </header>

        <div class="containerProject">
            <form>
                <h5>Subjects : <? echo $projectInfo['Subjects'] ?? "" ?></h5>
                <div class="container">
                    <div>
                        <? if (isset($project['LinkImage'])) : ?>
                            <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $project['LinkImage'] ?>">
                        <? else: ?>
                            <img src="./ImageUpload/OtherImg/logoYnov.jpg">
                        <? endif; ?>
                        <h2 style="text-align: center;">Description</h2>
                        <input type="text"><? echo $project['Description'] ?? "" ?></input>
                    </div>
                    <div id="Collaborator-container">
                        <label for="collaborator">Colaborator</label>
                        <? foreach ($TeamInfo as $team) { ?>
                            <div>
                                <a style="color:black;text-decoration:none;" href="profil.php?id=<? echo $team['Id']; ?>">
                                    <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;"><? echo $team['First_name'] ?? "" ?> <? echo $team['Last_name'] ?? "" ?></h4>
                                </a>
                            </div>
                        <? } ?>
                        <button type="button" onclick="addCollab()" class="AddButton">+</button>
                    </div>
                </div>
            </form>
        </div>
        <? foreach ($projectInfo as $project) : ?>
            <div class="containerProject">
                <h5>Subjects : <? echo $projectInfo['Subjects'] ?? "" ?></h5>
                <div class="container">
                    <div>
                        <? if (isset($project['LinkImage'])) : ?>
                            <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $project['LinkImage'] ?>">
                        <? else: ?>
                            <img src="./ImageUpload/OtherImg/logoYnov.jpg">
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
        <script>
            function addCollab() {
                const collabDiv = document.createElement("div");
                collabDiv.innerHTML = `
                <label for="collaborator">Colaborator</label>
                                <input type="text" id="addCollab" name="collaborator[]" placeholder="User" value="<? echo $collab['School'] ?? "" ?>" oninput="getLiveValue()">
                                <div class="resultBox" style="position: absolute;"></div>
                <button type="button" onclick="remove(this)" class="AddButton">-</button>
                <br><br>
                 `;
                document.getElementById("Collaborator-container").appendChild(collabDiv);
            }

            // Fonction pour supprimer une entrée de formation
            function remove(button) {
                button.parentElement.remove();
            }

            function getLiveValue() {
                const inputValue = document.getElementById('addCollab').value.toLowerCase();
                const resultBox = document.querySelector('.resultBox');
                resultBox.innerHTML = '';

                if (inputValue) {
                    <?php foreach ($AllUsers as $user) : ?>
                        if ('<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'.toLowerCase().includes(inputValue)) {
                            const userDiv = document.createElement('div');
                            userDiv.classList.add('search-result');
                            userDiv.innerHTML = `
                            <a href="profil.php?id=<?php echo $user['Id']; ?>" style="text-decoration: none; color: white; display: flex; align-items: center;">
                                <img src="./ImageUpload/UserPP/<?php echo $user['PP_User'] ?? 'user_Img.png'; ?>" class="PPUser" style="width:30px; height:30px" alt="PP User">
                                <h4 style="color: black">${'<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'}</h4>
                            </a>
                        `;
                            resultBox.appendChild(userDiv);
                        }
                    <?php endforeach; ?>
                }
            }
        </script>
    </body>
<? endif; ?>

</html>