<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM classe WHERE id_classe=?"); $stmt->execute([$id]); $cl=$stmt->fetch();
if(!$cl){ $_SESSION['err']="Introuvable."; header("Location: liste_classes.php"); exit(); }
$page_title="Modifier la classe"; $active_page="classes"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $libelle=$_POST['libelle']; $niveau=$_POST['niveau']; $capacite=(int)$_POST['capacite_max']; $annee=$_POST['annee_scolaire'];
    if(empty($libelle)) $errors[]="Libellé obligatoire.";
    if(empty($errors)){
        $pdo->prepare("UPDATE classe SET libelle=?,niveau=?,capacite_max=?,annee_scolaire=? WHERE id_classe=?")->execute([$libelle,$niveau,$capacite,$annee,$id]);
        $_SESSION['msg']="Classe modifiée."; header("Location: liste_classes.php"); exit();
    }
}else{ $_POST=$cl; }
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?><div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="page-header"><div><h1>Modifier la classe</h1><p><?php echo htmlspecialchars($cl['libelle']); ?></p></div><a href="liste_classes.php" class="btn btn-secondary">← Retour</a></div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row">
    <div class="form-group"><label>Libellé *</label><input type="text" name="libelle" class="form-control" value="<?php echo htmlspecialchars($_POST['libelle']??''); ?>" required></div>
    <div class="form-group"><label>Niveau</label><input type="text" name="niveau" class="form-control" value="<?php echo htmlspecialchars($_POST['niveau']??''); ?>"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Capacité max</label><input type="number" name="capacite_max" class="form-control" value="<?php echo $_POST['capacite_max']??30; ?>"></div>
    <div class="form-group"><label>Année scolaire *</label><input type="text" name="annee_scolaire" class="form-control" value="<?php echo htmlspecialchars($_POST['annee_scolaire']??''); ?>" required></div>
</div>
<div class="form-actions"><button type="submit" class="btn btn-primary">✓ Enregistrer</button><a href="liste_classes.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>