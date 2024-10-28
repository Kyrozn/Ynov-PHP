<?php
session_start();
require_once __DIR__ . '/../Func/db.php'; // Include the database connection
require_once __DIR__ . '/../Func/function.php';
$personalInfo;
$Cvinfo;

$requestUri = $_SERVER['REQUEST_URI'];

// Remove any query string from the URL
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$ExpExts = [];
$EduExts = [];
$Skills = [];
// Match routes
if (!isset($_GET['id'])) {

    if (isset($_COOKIE['UserTokenSession'])) {
        $userId = $_COOKIE['UserTokenSession'];

        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->execute([$userId]);
        $personalInfo = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM CV WHERE User_ID = ?');
        $stmt->execute([$userId]);
        $Cvinfo = $stmt->fetch();
    } else {
        header("Location: /");
    }

    if (isset($Cvinfo['CV_ID'])) {

        $stmt = $pdo->prepare('SELECT * FROM ExpExternal WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $ExpExts = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $stmt = $pdo->prepare('SELECT * FROM EducationExt WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $EduExts = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $stmt = $pdo->prepare('SELECT * FROM Skills WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $Skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {


    $voucherId = $_GET['id'];
    if (isset($_COOKIE['UserTokenSession']) && $voucherId === $_COOKIE['UserTokenSession']) {
        header("Location: /profil");
    }
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
    $stmt->execute([$voucherId]);
    $personalInfo = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT * FROM CV WHERE User_ID = ?');
    $stmt->execute([$personalInfo['Id']]);
    $Cvinfo = $stmt->fetch();

    if (isset($Cvinfo['CV_ID'])) {

        $stmt = $pdo->prepare('SELECT * FROM ExpExternal WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $ExpExts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT * FROM EducationExt WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $EduExts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT * FROM Skills WHERE CV_ID = ?');
        $stmt->execute([$Cvinfo['CV_ID']]);
        $Skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Handle form submission and update the database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['F_name'])) {
    $Fname = $_POST['F_name'];
    $Lname = $_POST['L_name'];
    $email = $_POST['Email'];
    $phone = $_POST['phone'];
    $UserTxt = $_POST['UserText'];

    // Update personal information in the database
    $stmt = $pdo->prepare('UPDATE Users SET First_name = ?, Last_name = ?, Email = ?, UserText = ?, PhoneNB = ? WHERE Id = ?');
    $stmt->execute([$Fname, $Lname, $email, $UserTxt, $phone, $personalInfo['Id']]);
    header("Location: /profil");
    exit;
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['TitleUser'])) {
    $Title = $_POST['TitleUser'];
    $Description = $_POST['DescriptionUser'];
    $ExperienceTitle = isset($_POST['experienceTitle']) ? $_POST['experienceTitle'] : '';
    $ExperienceDesc = isset($_POST['experienceDesc']) ? $_POST['experienceDesc'] : '';
    $ExperienceStart = isset($_POST['experienceStart']) ? $_POST['experienceStart'] : '';
    $ExperienceEnd = isset($_POST['experienceEnd']) ? $_POST['experienceEnd'] : '';

    $SkillTitle = isset($_POST['skillTitle']) ? $_POST['skillTitle'] : '';
    $SkillDesc = isset($_POST['skillDesc']) ? $_POST['skillDesc'] : '';
    $SkillYear = isset($_POST['skillYear']) ? $_POST['skillYear'] : '';
    $EduSchool = isset($_POST['EducationSchool']) ? $_POST['EducationSchool'] : '';
    $EduStart = isset($_POST['EducationStart']) ? $_POST['EducationStart'] : '';
    $EduEnd = isset($_POST['EducationEnd']) ? $_POST['EducationEnd'] : '';

    if (is_null($Cvinfo['CV_ID'])) {
        $CV_ID = guidv4();

        $stmt = $pdo->prepare('INSERT INTO CV (CV_ID, User_ID, Title, Description) VALUES (?,?,?,?)');
        $stmt->execute([$CV_ID, $_COOKIE['UserTokenSession'], $Title, $Description]);
        if (is_array($ExperienceTitle)) {
            for ($i = 0; $i < count($ExperienceTitle); $i++) {
                $stmt = $pdo->prepare('INSERT INTO ExpExternal (ExpExt_ID, CV_ID, Title, Description, Start_Date, End_Date) VALUES (?,?,?,?,?,?)');
                $stmt->execute([guidv4(), $CV_ID, $ExperienceTitle[$i], $ExperienceDesc[$i], $ExperienceStart[$i], $ExperienceEnd[$i]]);
            }
        }
        if (is_array($SkillTitle)) {
            for ($i = 0; $i < count($SkillTitle); $i++) {
                $stmt = $pdo->prepare('INSERT INTO Skills (Skill_ID, CV_ID, Title, Description, YearsXP) VALUES (?,?,?,?,?)');
                $stmt->execute([guidv4(), $CV_ID, $SkillTitle[$i], $SkillDesc[$i], $SkillYear[$i]]);
            }
        }
        if (is_array($EduSchool)) {
            for ($i = 0; $i < count($EduSchool); $i++) {
                $stmt = $pdo->prepare('INSERT INTO EducationExt (EducationExt_ID, CV_ID, School, Start_Date, End_Date) VALUES (?,?,?,?,?)');
                $stmt->execute([guidv4(), $CV_ID, $EduSchool[$i], $EduStart[$i], $EduEnd[$i]]);
            }
        }
    } else {
        $stmt = $pdo->prepare('UPDATE CV SET  Title = ?, Description = ? Where CV_ID = ?');
        $stmt->execute([$Title, $Description, $Cvinfo['CV_ID']]);

        if (is_array($ExperienceTitle)) {
            for ($i = 0; $i < count($ExperienceTitle); $i++) {
                if (array_key_exists($i, $ExpExts)) {
                    $stmt = $pdo->prepare('UPDATE ExpExternal SET Title = ?, Description = ?, Start_Date = ?, End_Date = ? WHERE ExpExt_ID = ?');
                    $stmt->execute([$ExperienceTitle[$i], $ExperienceDesc[$i], $ExperienceStart[$i], $ExperienceEnd[$i], $ExpExts[$i]['ExpExt_ID']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO ExpExternal (ExpExt_ID, CV_ID, Title, Description, Start_Date, End_Date) VALUES (?,?,?,?,?,?)');
                    $stmt->execute([guidv4(), $Cvinfo['CV_ID'], $ExperienceTitle[$i], $ExperienceDesc[$i], $ExperienceStart[$i], $ExperienceEnd[$i]]);
                }
            }
        }
        if (is_array($SkillTitle)) {
            for ($i = 0; $i < count($SkillTitle); $i++) {
                if (array_key_exists($i, $Skills)) {
                    $stmt = $pdo->prepare('UPDATE Skills SET Title = ?, Description = ?, YearsXP = ? WHERE Skill_ID = ?');
                    $stmt->execute([$SkillTitle[$i], $SkillDesc[$i], $SkillYear[$i], $Skills[$i]['Skill_ID']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO Skills (Skill_ID, CV_ID, Title, Description, YearsXP) VALUES (?,?,?,?,?)');
                    $stmt->execute([guidv4(), $Cvinfo['CV_ID'], $SkillTitle[$i], $SkillDesc[$i], $SkillYear[$i]]);
                }
            }
        }
        if (is_array($EduSchool)) {
            for ($i = 0; $i < count($EduSchool); $i++) {
                if (array_key_exists($i, $EduExts)) {
                    $stmt = $pdo->prepare('UPDATE EducationExt SET School = ?, Start_Date = ?, End_Date = ? WHERE EducationExt_ID = ?');
                    $stmt->execute([$EduSchool[$i], $EduStart[$i], $EduEnd[$i], $EduExts[$i]['EducationExt_ID']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO EducationExt (EducationExt_ID, CV_ID, School, Start_Date, End_Date) VALUES (?,?,?,?,?)');
                    $stmt->execute([guidv4(), $Cvinfo['CV_ID'], $EduSchool[$i], $EduStart[$i], $EduEnd[$i]]);
                }
            }
        }
    }
    header("Location: /profil");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../../static/profil.css">
</head>

<body>
    <div class="logout-container">
        <a href="/" class="logout">Return</a>
    </div>
    <div class="Header-container">
        <!-- Header Section -->
        <header>
            <? if (!is_null($personalInfo['BackgroundUser'])) : ?>
                <img src="/ImageUpload/Background/<?php echo $personalInfo['BackgroundUser'] ?>" alt="BackgroundUser" class="BackgroundImage">
            <? else : ?>
                <img src="/ImageUpload/Background/backgroundDefault.jpg" alt="BackgroundImage" class="BackgroundImage">
            <? endif; ?>
            <?php if (isset($personalInfo) && isset($_COOKIE['UserTokenSession']) && $personalInfo['Id'] === $_COOKIE['UserTokenSession']): ?>
                <form action="index.php" method="POST" enctype="multipart/form-data" style="display:none">
                    <input type="file" name="file">
                    <button type="submit">Enregistrer</button>
                </form>
            <? endif; ?>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div class="infoUser" style="display: flex; align-items: center;">
                    <form id="uploadForm" style="display: flex;" action="" method="POST" enctype="multipart/form-data">
                        <label for="PPupload">
                            <?php if (!is_null($personalInfo['PP_User'])) : ?>
                                <img src="./ImageUpload/UserPP/<?php echo htmlspecialchars($personalInfo['PP_User']); ?>" alt="PP User" class="PPUser" id="PPUser">
                            <?php else : ?>
                                <img src="/ImageUpload/UserPP/user_Img.png" alt="PP User" class="PPUser" id="PPUser">
                            <?php endif; ?>
                        </label>
                        <input type="file" accept=".jpg, .jpeg, .png" name="PPupload" id="PPupload" style="opacity: 0; width: 0;" onchange="document.getElementById('uploadForm').submit();" />
                    </form>

                    <h1 style="margin-left: 10px"><?php echo $personalInfo['First_name'] ?> <?php echo $personalInfo['Last_name'] ?></h1>
                </div>
                <div class="header-controls">
                    <?php if (isset($personalInfo) && isset($_COOKIE['UserTokenSession']) && $personalInfo['Id'] === $_COOKIE['UserTokenSession']): ?>
                        <button id="editBtn" style="background-color: transparent;"><img src="/static/parameters.png" alt="Settings"></button>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <p>Email: <a href="mailto:<? echo $personalInfo['Email'] ?>"><? echo $personalInfo['Email'] ?></a> | Phone:
            <? if (!is_null($personalInfo['PhoneNB'])): ?>
                <a href="tel:<? $personalInfo['PhoneNB']; ?>"><?php echo $personalInfo['PhoneNB']; ?></a>
            <? else : ?> Unknown
            <? endif; ?>
        </p>
        <!-- Profile Section -->
        <?php if (isset($personalInfo) && !empty($personalInfo)): ?>
            <section class="profile">
                <h2>Profile</h2>
                <p><?php echo $personalInfo['UserText']; ?></p>
            </section>
        <?php else: ?>
            <section class="profile">
                <h2>No Profile Found</h2>
            </section>
        <?php endif; ?>
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
                    <input type="text" id="phone" name="phone" value="<?php echo $personalInfo['PhoneNB']; ?>">

                    <label for="UserText">Profile Description:</label>
                    <textarea id="UserText" name="UserText"><?php echo $personalInfo['UserText']; ?></textarea>

                    <input type="submit" value="Save Changes">
                </form>
            </div>
        </div>
    </div>

    <div class="containerCV">
        <? if (isset($_COOKIE['UserTokenSession']) && isset($personalInfo) && $personalInfo['Id'] === $_COOKIE['UserTokenSession']) : ?>
            <h1 style="text-align:center;">Création et édition du CV</h1>
            <div class="container">
                <form action="" method="post" id="formCV">
                    <!-- Informations personnelles -->
                    <h2 style="text-align: center;">Titre - description</h2>
                    <label for="TitleUser">Titre</label>
                    <input type="text" id="Title" name="TitleUser" value="<? echo $Cvinfo['Title'] ?? "" ?>" required>

                    <label for="DescriptionUser">Description</label>
                    <input type="text" id="Description" name="DescriptionUser" value="<? echo $Cvinfo['Description'] ?? "" ?>" required>

                    <h3>Numero Téléphone</h3>
                    <p><? echo $personalInfo['PhoneNB'] ?></p>

                    <!-- Expérience professionnelle -->
                    <h2>Expérience professionnelle</h2>
                    <div id="exp-container">
                        <? foreach ($ExpExts as $exp) { ?>
                            <div>
                                <label for="experience">Résumé de l'expérience</label>
                                <input type="text" id="Title" name="experienceTitle[]" placeholder="Title" value="<? echo $exp['Title'] ?? "" ?>">
                                <input type="text" id="Description" name="experienceDesc[]" placeholder="Description" value="<? echo $exp['Description'] ?? "" ?>">
                                <input type="date" id="Description" name="experienceStart[]" placeholder="DateStart" value="<? echo $exp['Start_Date'] ?? "" ?>">
                                <input type="date" id="Description" name="experienceEnd[]" placeholder="DateEnd" value="<? echo $exp['End_Date'] ?? "" ?>">
                                <br><br>
                            </div>
                        <? } ?>
                        <button type="button" onclick="addExp()" class="AddButton">+</button>
                    </div>
                    <!-- Compétences -->
                    <h2>Compétences</h2>
                    <div id="skill-container">
                        <? foreach ($Skills as $skill) { ?>
                            <div>
                                <label for="skills">Liste des compétences</label>
                                <input type="text" id="Title" name="skillTitle[]" placeholder="Title" value="<? echo $skill['Title'] ?? "" ?>">
                                <input type="text" id="Description" name="skillDesc[]" placeholder="Description" value="<? echo $skill['Description'] ?? "" ?>">
                                <input type="number" id="Description" name="skillYear[]" placeholder="YearsXP" value="<? echo $skill['YearsXP'] ?? "" ?>">
                            </div>
                        <? } ?>
                        <button type="button" onclick="addSkill()" class="AddButton">+</button>
                    </div>
                    <!-- Éducation -->
                    <h2>Éducation</h2>
                    <div id="edu-container">
                        <? foreach ($EduExts as $edu) { ?>
                            <div>
                                <label for="education">Formation académique</label>
                                <input type="text" id="Title" name="EducationSchool[]" placeholder="School Name" value="<? echo $edu['School'] ?? "" ?>">
                                <input type="date" id="Description" name="EducationStart[]" placeholder="DateStart" value="<? echo $edu['Start_Date'] ?? "" ?>">
                                <input type="date" id="Description" name="EducationEnd[]" placeholder="DateEnd" value="<? echo $edu['End_Date'] ?? "" ?>">
                            </div>
                        <? } ?>
                        <button type="button" onclick="addEducation()" class="AddButton">+</button>
                    </div>
                    <!-- Soumettre -->
                    <button type="submit" class="register">Enregistrer le CV</button>
                </form>
                <form id="pdfForm">
                    <button type="submit">Generate CV from this template</button>
                </form>
            </div>
        <? else : ?>
            <? if ($Cvinfo === " ") { ?>
                <h1 style="text-align:center;">Cette personne n'a pas posté de CV</h1>
            <? } else { ?>

                <h1 style="text-align:center;">CV</h1>
                <div class="container" style="width: auto" id="formCV">
                    <h2>Informations personnelles</h2>
                    <h3>Nom complet</h3>
                    <p><? echo $personalInfo['First_name'] ?> <? echo $personalInfo['Last_name'] ?></p>

                    <h3>Adresse Mail</h3>
                    <a href="mailto:<? echo $personalInfo['Email'] ?>"><? echo $personalInfo['Email'] ?></a>

                    <h3>Numero Téléphone</h3>
                    <p><? echo $personalInfo['PhoneNB'] ?></p>

                    <!-- Expérience professionnelle -->
                    <h2>Expérience professionnelle</h2>
                    <? if (!is_null($ExpExts)) { ?>
                        <? foreach ($ExpExts as &$expExt) { ?>
                            <h3><? echo $expExt['Title'] ?></h3>
                            <p><? echo $expExt['Description'] ?></p>
                        <? } ?>
                    <? } ?>

                    <!-- Compétences -->
                    <h2>Compétences</h2>
                    <? foreach ($Skills as &$skill) { ?>
                        <h3><? echo $skill['Title'] ?> <? echo $skill['YearsXP'] ?> ans d'experience</h3>
                        <p><? echo $skill['Description'] ?></p>
                    <? } ?>

                    <!-- Éducation -->
                    <h2>Éducation</h2>
                    <? foreach ($EduExts as &$EduExt) { ?>
                        <h3><? echo $EduExt['School'] ?> </h3>
                        <p><? echo $EduExt['Start_Date'] ?> - <? echo $EduExt['End_Date'] ?></p>
                    <? } ?>
                </div>
                <form id="pdfForm">
                    <button type="submit">Generate CV from this template</button>
                </form>
            <? } ?>
        <? endif; ?>
    </div>
    <script src="../../static/profil.js"></script>
</body>

</html>