<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM parent WHERE id_parent=?"); $stmt->execute([$id]); $par=$stmt->fetch();
if(!$par){ $_SESSION['err']="Introuvable."; header("Location: liste_parents.php"); exit(); }
$page_title="Modifier parent"; $active_page="parents"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $nom=$_POST['nom']; $prenom=$_POST['prenom']; $tel=$_POST['telephone']; $email=$_POST['email']; $prof=$_POST['profession']; $lien=$_POST['lien_parente'];
    if(empty($nom)) $errors[]="Nom obligatoire.";
    if(empty($errors)){
        $pdo->prepare("UPDATE parent SET nom=?,prenom=?,telephone=?,email=?,profession=?,lien_parente=? WHERE id_parent=?")->execute([$nom,$prenom,$tel,$email,$prof,$lien,$id]);
        $_SESSION['msg']="Parent modifié."; header("Location: liste_parents.php"); exit();
    }
}else{ $_POST=$par; }
include("../includes/header.php");
?>
<div class="page-header"><div><h1>Modifier le parent</h1></div><a href="liste_parents.php" class="btn btn-secondary">← Retour</a></div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row">
    <div class="form-group"><label>Nom *</label><input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($_POST['nom']??''); ?>" required></div>
    <div class="form-group"><label>Prénom</label><input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($_POST['prenom']??''); ?>"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Téléphone *</label><input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($_POST['telephone']??''); ?>" required></div>
    <div class="form-group"><label>Lien de parenté *</label><input type="text" name="lien_parente" class="form-control" value="<?php echo htmlspecialchars($_POST['lien_parente']??''); ?>" required></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email']??''); ?>"></div>
    <div class="form-group"><label>Profession</label><input type="text" name="profession" class="form-control" value="<?php echo htmlspecialchars($_POST['profession']??''); ?>"></div>
</div>
<div class="form-actions"><button type="submit" class="btn btn-primary">✓ Enregistrer</button><a href="liste_parents.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>