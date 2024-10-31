<?php
session_start();
require_once __DIR__ . '/../Func/db.php'; // Include the database connection
require_once __DIR__ . '/../Func/function.php';
// index.php
$requestUri = $_SERVER['REQUEST_URI'];

// Remove any query string from the URL
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$thisInfo;
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
    $projectInfo = [];


    // Fetch personal information of the user
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$voucherId]);
    $personalInfo = $stmt->fetch();

    // Fetch all projects associated with the user
    $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE User_Id = ?');
    $stmt->execute([$voucherId]);
    $pivot = $stmt->fetchAll();

    function fetchproject($pivot, $pdo)
    {
        // Fetch project details based on the user's projects
        foreach ($pivot as $projectUser) {
            $stmt = $pdo->prepare('SELECT * FROM Projects WHERE Project_Id = ?');
            $stmt->execute([$projectUser['Project_Id']]);
            $project = $stmt->fetch();
            if ($project) {
                $projectInfo[] = $project; // Store project details
            }
        }
        return $projectInfo;
    }
    function fetchcollab($projectInfo, $pdo)
    {

        $TeamInfo = []; // Initialize TeamInfo array
        // Fetch collaborators for each project
        $stmt = $pdo->prepare('SELECT * FROM ProjectsUsers WHERE Project_Id = ?');
        $stmt->execute([$projectInfo['Project_Id']]);
        $collaborators = $stmt->fetchAll();

        foreach ($collaborators as $collab) {
            $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
            $stmt->execute([$collab['User_ID']]);
            $team = $stmt->fetch();
            if ($team) {
                $TeamInfo[] = $team; // Store collaborator details
            }
        }
        return $TeamInfo;
    }
}
function fetchUser($Id, $pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$Id]);
    $thisInfo = $stmt->fetch();
    return $thisInfo;
}
$stmt = $pdo->prepare('SELECT * FROM Users');
$stmt->execute();
$AllUsers = $stmt->fetchAll();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Title'])) {
    $Subject = $_POST['Subject'];
    $Title = $_POST['Title'];
    $Desc = $_POST['desc'];
    $NewId = guidv4();
    // Update personal information in the database
    $stmt = $pdo->prepare('INSERT INTO Projects (Project_Id, Title, Subjects, Description, Validate) VALUES (?,?,?,?,?)');
    $stmt->execute([$NewId, $Title, $Subject, $Desc, 0]);
    $stmt = $pdo->prepare('INSERT INTO ProjectsUsers (Project_Id, User_ID) VALUES (?,?)');
    $stmt->execute([$NewId, $voucherId]);
    header("Location: /project");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données JSON envoyées par fetch
    $data = json_decode(file_get_contents("php://input"), true);
    echo "test";
    $id = $data['CollabId'];
    $projectid = guidv4();
    $stmt = $pdo->prepare('INSERT INTO Projects (Project_Id) values (?)');
    $stmt->execute([$projectid]);

    $stmt = $pdo->prepare('INSERT INTO ProjectsUsers (Project_Id, User_Id) values (?,?)');
    $stmt->execute([$projectid, $voucherId]);
    $stmt = $pdo->prepare('INSERT INTO ProjectsUsers (Project_Id, User_Id) values (?,?)');
    $stmt->execute([$projectid, $id]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['PPupload'])) {
    $file = $_FILES['PPupload'];

    if ($file['error'] !== 0) {
        echo "Erreur lors de l'upload du fichier.";
        exit;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExtensions)) {
        echo "Seules les images de type JPG, JPEG ou PNG sont acceptées.";
        exit;
    }

    $maxFileSize = 20 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxFileSize) {
        echo "La taille du fichier ne doit pas dépasser 20MB.";
        exit;
    }

    $safeFileName = $userId . "." . $fileExt;
    $uploadDir = "./ImageUpload/UserPP/";
    $fileDestination = $uploadDir . $safeFileName;

    if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
        $stmt = $pdo->prepare('UPDATE Users SET PP_User = ? WHERE Id = ?');
        $stmt->execute([$safeFileName, $userId]);
        header("Location: /profil");
    }
}
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
                        <a style="color:black;text-decoration:none;" href="/profil?id=<? echo $team['Id'] ?? "" ?>">
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
            <form method="post">
                <h5>Sujets : </h5>
                <input type="text" name="Subject" placeholder="Sujets">

                <div class="container">
                    <div>
                        <? if (isset($project['LinkImage'])) : ?>
                            <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $project['LinkImage'] ?>">
                        <? else: ?>
                            <img src="./ImageUpload/OtherImg/logoYnov.jpg">
                        <? endif; ?>
                        <h2 style="text-align: center;">Description</h2>
                        <input type="text" name="desc"></input>
                    </div>
                    <input type="text" name="Title" placeholder="Title">
                    <div id="Collaborator-container">
                        <label for="collaborator">Collaborateur</label>
                        <div id="AllCollab">
                            <div style="color:black;text-decoration:none;" id="<? echo $personalInfo['Id']; ?>">
                                <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;"><? echo $personalInfo['First_name'] ?? "" ?> <? echo $personalInfo['Last_name'] ?? "" ?></h4>
                            </div>
                        </div>
                        <button type="button" onclick="addCollab()" class="AddButton">+</button>
                    </div>
                </div>
                <button type="submit" class="register">Creer Ce nouveaux Projet</button>
            </form>
        </div>
        <? foreach (fetchproject($pivot, $pdo) as $projects) : ?>
            <div class="containerProject">
                <? echo htmlspecialchars($projects['Title']) ?>
                <h5>Subjects : <? echo $projects['Subjects'] ?? "" ?></h5>
                <div class="container">
                    <div>
                        <? if (isset($projects['LinkImage'])) : ?>
                            <img style="width: 700px; height: 350px; object-fit: cover; box-sizing: border-box;" src="../ImageUpload/OtherImg/<? echo $projects['LinkImage'] ?>">
                        <? else: ?>
                            <img src="./ImageUpload/OtherImg/logoYnov.jpg">
                        <? endif; ?>
                        <h2 style="text-align: center;">Description</h2>
                        <h3><? echo $projects['Description'] ?? "" ?></h3>
                    </div>

                    <div>
                        <h2>Collaborateur</h2>
                        <? foreach (fetchcollab($projects, $pdo) as $team) { ?>
                            <a style="color:black;text-decoration:none;" href="/profil?id=<? echo $team['Id'] ?? "" ?>">
                                <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;"><? echo $team['First_name'] ?? "" ?> <? echo $team['Last_name'] ?? "" ?></h4>
                            </a>
                        <? } ?>
                    </div>
                </div>
                <?
                if ($projects['Validate'] === 0) {
                    echo htmlspecialchars("Waiting for admin validation");
                }
                ?>
            </div>
        <? endforeach ?>
        <script>
            function AddcollabChoiced(collab) {
                console.log(collab)
                var collabNode = `
                <div style="color:black;text-decoration:none;" "id="` + collab['Id'] + `">
                    <h4 style="border-radius: 25px; border: 2px solid black;padding: 15px;">` + collab['First_name'] + ` ` + collab['Last_name'] + `</h4>
                </div>`
                document.getElementById("AllCollab").insertAdjacentHTML('beforeend', collabNode);
            }

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

            function sendData(selectedResult) {
                // Créer un objet de données à envoyer
                const data = {
                    CollabId: selectedResult
                };
                console.log(JSON.stringify(data))

                // Utiliser fetch pour envoyer les données
                fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)

                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return;
                    })
                    .then(data => {
                        console.log('Success:', data);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });

            }

            function getLiveValue() {
                const inputValue = document.getElementById('addCollab').value.toLowerCase();
                const resultBox = document.querySelector('.resultBox');
                resultBox.innerHTML = '';

                if (inputValue) {
                    <?php foreach ($AllUsers as $user) : ?>
                        users = '<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'.toLowerCase()
                        if (users.includes(inputValue) && '<? echo $user['Id'] ?>' !== '<? echo $voucherId ?>') {
                            const userDiv = document.createElement('div');
                            userDiv.classList.add('search-result');
                            userDiv.innerHTML = `
                            <div class="childButton" id="<?php echo $user['Id']; ?>" style="text-decoration: none; color: white; display: flex; align-items: center;">
                                <img src="./ImageUpload/UserPP/<?php echo $user['PP_User'] ?? 'user_Img.png'; ?>" class="PPUser" style="width:30px; height:30px" alt="PP User">
                                <h4 style="color: black">${'<?php echo htmlspecialchars($user['First_name'] . " " . $user['Last_name']); ?>'}</h4>
                            </div>
                        `;
                            resultBox.appendChild(userDiv);
                            userDiv.addEventListener('click', function() {

                                var parameters = <? echo json_encode(fetchUser($user['Id'], $pdo)) ?>;
                                AddcollabChoiced(parameters)
                            });
                        }
                    <?php endforeach; ?>
                }
            };
        </script>
    </body>
<? endif; ?>

</html>