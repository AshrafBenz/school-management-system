<?php
session_start();
require_once("../connexion/connexion.php");

// غير admin يدخل
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentification/connexion.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if(empty($login)){
        $error = "Login obligatoire";
    } else {

        $check = $pdo->prepare("SELECT id_user FROM utilisateur WHERE login=?");
        $check->execute([$login]);

        if($check->fetch()){
            $error = "Login déjà utilisé";
        } else {

            $pdo->prepare("
                INSERT INTO utilisateur(login, mot_de_passe, role, actif)
                VALUES(?,?, 'admin',1)
            ")->execute([$login,$password]);

            $success = "Admin ajouté avec succès";
        }
    }
}
?>

<h2>Ajouter Admin</h2>

<?php if($error) echo $error; ?>
<?php if($success) echo $success; ?>

<form method="POST">
<input type="text" name="login" placeholder="Login"><br>
<input type="password" name="password" placeholder="Password"><br>
<button type="submit">Ajouter Admin</button>
</form>