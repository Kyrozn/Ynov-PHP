<?php
session_start();
require __DIR__ . '/../Func/db.php'; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_COOKIE['UserTokenSession'])) {
    header("Location: /");
    exit;
}

// Récupérer les informations de l'utilisateur connecté
$stmt = $pdo->prepare('SELECT * FROM Users WHERE Id = ?');
$stmt->execute([$_COOKIE['UserTokenSession']]);
$user = $stmt->fetch();

// Vérifier si l'utilisateur est un administrateur
if ($user['UserRole'] !== 'Admin') {
    header("Location: /");
    exit;
}

// all user
$stmt = $pdo->prepare('SELECT * FROM Users');
$stmt->execute();
$users = $stmt->fetchAll();

// Récupérer tous les projets en attente d'approbation
$stmt = $pdo->prepare('SELECT * FROM Projects WHERE Validate = 0');
$stmt->execute();
$pending_projects = $stmt->fetchAll();

// ban
if (isset($_POST['ban_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare('DELETE FROM Users WHERE Id = ?');
    $stmt->execute([$userId]);
    header("Location: /admin");
    exit;
}

// Promouvoir un utilisateur en administrateur
if (isset($_POST['promote_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare('UPDATE Users SET UserRole = "Admin" WHERE Id = ?');
    $stmt->execute([$userId]);
    header("Location: /admin");
    exit;
}

// Approuver un projet
if (isset($_POST['approve_project'])) {
    $projectId = $_POST['project_id'];
    $stmt = $pdo->prepare('UPDATE Projects SET Validate = 1 WHERE Project_ID = ?');
    $stmt->execute([$projectId]);
    header("Location: /admin");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="../../static/adminPanel.css">
</head>
<body>

<div class="container">
    <a href="/">Home</a>
    <h2>Tableau de bord Admin</h2>

    <h3>Utilisateurs inscrits</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['First_name'].' '. $user['Last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                <td><?php echo htmlspecialchars($user['UserRole']); ?></td>
                <td>
                    <?php if ($user['UserRole'] !== 'Admin'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="user_id" value="<?php echo $user['Id']; ?>">
                            <button type="submit" name="promote_user">Promouvoir admin</button>
                            <button type="submit" name="ban_user" onclick="return confirm('Êtes-vous sûr de vouloir bannir cet utilisateur ?');">Bannir</button>
                        </form>
                    <?php else: ?>
                        <span>Admin</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Projets en attente d'approbation</h3>
    <table>
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($pending_projects as $project): ?>
            <tr>
                <td><?php echo htmlspecialchars($project['Title']); ?></td>
                <td><?php echo htmlspecialchars($project['Description']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="project_id" value="<?php echo $project['Project_ID']; ?>">
                        <button type="submit" name="approve_project">Approuver</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>