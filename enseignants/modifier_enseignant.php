<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM enseignant WHERE id_enseignant=?"); $stmt->execute([$id]); $ens=$stmt->fetch();
if(!$ens){ $_SESSION['err']="Introuvable."; header("Location: liste_enseignants.php"); exit(); }
$page_title="Modifier enseignant"; $active_page="enseignants"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $d=['matricule_ens'=>trim($_POST['matricule_ens']),'nom'=>trim($_POST['nom']),'prenom'=>trim($_POST['prenom']),'specialite'=>trim($_POST['specialite']),'email'=>trim($_POST['email']),'telephone'=>trim($_POST['telephone']),'date_embauche'=>$_POST['date_embauche'],'grade'=>trim($_POST['grade'])];
    if(empty($d['matricule_ens'])) $errors[]="Matricule obligatoire.";
    if(empty($errors)){
        try{
            $pdo->prepare("UPDATE enseignant SET matricule_ens=?,nom=?,prenom=?,specialite=?,email=?,telephone=?,date_embauche=?,grade=? WHERE id_enseignant=?")
                ->execute(array_merge(array_values($d),[$id]));
            $_SESSION['msg']="Enseignant modifié."; header("Location: liste_enseignants.php"); exit();
        }catch(PDOException $e){ $errors[]=$e->getMessage(); }
    }
}else{ $_POST=$ens; }
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?><div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="page-header">
    <div><h1>Modifier l'enseignant</h1><p><?php echo htmlspecialchars($ens['prenom'].' '.$ens['nom']); ?></p></div>
    <a href="liste_enseignants.php" class="btn btn-secondary">← Retour</a>
</div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row-3">
    <div class="form-group"><label>Matricule *</label><input type="text" name="matricule_ens" class="form-control" value="<?php echo htmlspecialchars($_POST['matricule_ens']??''); ?>" required></div>
    <div class="form-group"><label>Nom *</label><input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($_POST['nom']??''); ?>" required></div>
    <div class="form-group"><label>Prénom</label><input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($_POST['prenom']??''); ?>"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Spécialité</label><input type="text" name="specialite" class="form-control" value="<?php echo htmlspecialchars($_POST['specialite']??''); ?>"></div>
    <div class="form-group"><label>Grade</label><input type="text" name="grade" class="form-control" value="<?php echo htmlspecialchars($_POST['grade']??''); ?>"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email']??''); ?>" required></div>
    <div class="form-group"><label>Téléphone</label><input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($_POST['telephone']??''); ?>"></div>
</div>
<div class="form-group"><label>Date embauche</label><input type="date" name="date_embauche" class="form-control" value="<?php echo $_POST['date_embauche']??''; ?>"></div>
<div class="form-actions">
    <button type="submit" class="btn btn-primary">✓ Enregistrer</button>
    <a href="liste_enseignants.php" class="btn btn-secondary">Annuler</a>
</div>
</form></div></div>
<?php include("../includes/footer.php"); ?>