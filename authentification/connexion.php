<?php
session_start();
require_once("../connexion/connexion.php");

if(isset($_SESSION['user_id']) && $_SESSION['role'] != 'admin'){
    header("Location: ../tableau_de_bord/dashboard.php");
    exit();
}

if(isset($_SESSION['user_id'])){
    header("Location: ../tableau_de_bord/dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $login    = $_POST['login'];
    $password = $_POST['password'];

    if(empty($login) || empty($password)){
        $error = "Veuillez remplir tous les champs.";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = ? AND actif = 1");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['mot_de_passe'])){

            $_SESSION['user_id']   = $user['id_user'];
            $_SESSION['login']     = $user['login'];
            $_SESSION['role']      = $user['role']; // 🔥 مهم
            $_SESSION['entity_id'] = null;

            if($user['role'] == "Prof"){
                $s = $pdo->prepare("SELECT id_enseignant FROM enseignant WHERE id_user = ?");
                $s->execute([$user['id_user']]);
                $r = $s->fetch();
                $_SESSION['entity_id'] = $r['id_enseignant'] ?? null;
            }
            if($user['role'] == "Eleve"){
                $s = $pdo->prepare("SELECT id_eleve FROM eleve WHERE id_user = ?");
                $s->execute([$user['id_user']]);
                $r = $s->fetch();
                $_SESSION['entity_id'] = $r['id_eleve'] ?? null;
            }

            if($user['role'] == "Parent"){
                $s = $pdo->prepare("SELECT id_parent FROM parent WHERE id_user = ?");
                $s->execute([$user['id_user']]);
                $r = $s->fetch();
                $_SESSION['entity_id'] = $r['id_parent'] ?? null;
            }

            header("Location: ../tableau_de_bord/dashboard.php");
            exit();

        } else {
            $error = "Identifiants invalides ou compte désactivé.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — EduManager</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-page">

    <div class="auth-left">
        <div class="auth-brand">
            <div class="logo-wrap">🎓</div>
            <h1>EduManager</h1>
            <p>Plateforme complète de gestion scolaire — élèves, notes, absences, paiements et plus.</p>
        </div>

        <div class="auth-features">
            <div class="auth-feature">
                <div class="icon">📊</div>
                <span>Tableau de bord avec statistiques en temps réel</span>
            </div>
            <div class="auth-feature">
                <div class="icon">📝</div>
                <span>Gestion des notes et bulletins trimestriels</span>
            </div>
            <div class="auth-feature">
                <div class="icon">📅</div>
                <span>Suivi des absences et emploi du temps</span>
            </div>
            <div class="auth-feature">
                <div class="icon">💳</div>
                <span>Gestion des paiements et frais scolaires</span>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-box">
            <h2>Bon retour 👋</h2>
            <p class="sub">Connectez-vous à votre espace de gestion</p>

            <?php if($error != ""): ?>
                <div class="alert alert-error">✕ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group" style="margin-bottom:16px">
                    <label for="login">Identifiant</label>
                    <input type="text" id="login" name="login" class="form-control"
                        placeholder="Votre login" required>
                </div>

                <div class="form-group" style="margin-bottom:24px">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="••••••••" required>
                </div>

                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:11px;">
                        Se connecter
                    </button>
                    <a href="register.php"
                        style="display:block; width:100%; text-align:center; padding:10px;
                        background:#f3f4f6; border-radius:8px; color:#374151;
                        font-size:0.875rem; font-weight:500;">
                        S'inscrire
                    </a>
                </div>
            </form>

            <div style="text-align:center; margin-top:28px; font-size:0.75rem; color:#9ca3af;">
                EduManager &copy; <?php echo date('Y'); ?> — Système de Gestion Scolaire
            </div>
        </div>
    </div>

</div>
</body>
</html>
