<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Utilisateurs";
$active_page = "users";

$stmt = $pdo->query("SELECT * FROM utilisateur");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <?php include("../includes/header.php"); ?>

    <!-- MAIN -->
    <div class="main-content">

        <!-- HEADER -->
        <div class="dashboard-header">
            <h1>Utilisateurs</h1>
        </div>

        <!-- TABLE CARD -->
        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between;">
                <h3>Liste des utilisateurs</h3>
                <a href="add_user.php" class="btn btn-primary">+ Ajouter</a>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Login</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id_user']; ?></td>
                            <td><?php echo $u['login']; ?></td>
                            <td>
                                <span class="badge">
                                    <?php echo $u['role']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $u['id_user']; ?>" class="btn btn-sm">✏️</a>
                                <a href="delete_user.php?id=<?php echo $u['id_user']; ?>" class="btn btn-sm btn-danger">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>