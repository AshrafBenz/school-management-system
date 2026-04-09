<?php
session_start();
require_once("../connexion/connexion.php");

if(isset($_SESSION['user_id'])){
    header("Location: ../tableau_de_bord/dashboard.php");
    exit();
}

$error   = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $login    = trim($_POST['login']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $role     = $_POST['role'];

    if(empty($login) || empty($password) || empty($confirm)){
        $error = "Veuillez remplir tous les champs.";
    } elseif(strlen($password) < 6){
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif($password !== $confirm){
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        
        $check = $pdo->prepare("SELECT id_user FROM utilisateur WHERE login = ?");
        $check->execute([$login]);
        if($check->fetch()){
            $error = "Ce login est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO utilisateur(login, mot_de_passe, role, actif) VALUES(?,?,?,1)")
                ->execute([$login, $hash, $role]);
            $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — EduManager</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-page">

    <div class="auth-left">
        <div class="auth-brand">
            <div class="logo-wrap">🎓</div>
            <h1>EduManager</h1>
            <p>Créez votre compte pour accéder à la plateforme de gestion scolaire.</p>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-box">
            <h2>Créer un compte</h2>
            <p class="sub">Remplissez le formulaire pour vous inscrire</p>

            <?php if($error != ""): ?>
                <div class="alert alert-error">✕ <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success != ""): ?>
                <div class="alert alert-success">✓ <?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Login</label>
                    <input type="text" name="login" class="form-control" placeholder="Choisir un identifiant" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 caractères" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirm" class="form-control" placeholder="Répéter le mot de passe" required>
                </div>
                <div class="form-group" style="margin-bottom:24px">
                    <label>Rôle</label>
                    <select name="role" class="form-control">
                        <option value="">----Selectionner----</option>
                        <option value="Prof">Professeur</option>
                        <option value="Parent">Parent</option>
                        <option value="Eleve">Élève</option>
                    </select>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:11px;">
                        Créer le compte
                    </button>
                    <a href="connexion.php"
                    style="display:block; width:100%; text-align:center; padding:10px;
                            background:#f3f4f6; border-radius:8px; color:#374151;
                            font-size:0.875rem; font-weight:500;">
                        J'ai déjà un compte
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
</html>
