<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Ajouter une classe"; $active_page="classes"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $libelle=$_POST['libelle']; $niveau=$_POST['niveau']; $capacite=(int)$_POST['capacite_max']; $annee=$_POST['annee_scolaire'];
    if(empty($libelle))  $errors[]="Libellé obligatoire.";
    if(empty($annee))    $errors[]="Année scolaire obligatoire.";
    if(empty($errors)){
        $pdo->prepare("INSERT INTO classe(libelle,niveau,capacite_max,annee_scolaire) VALUES(?,?,?,?)")->execute([$libelle,$niveau,$capacite,$annee]);
        $_SESSION['msg']="Classe créée."; header("Location: liste_classes.php"); exit();
    }
}
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?>
    <div class="alert alert-error">✕ 
        <?php echo implode('<br>',$errors); ?></div>
        <?php endif; ?>
<div class="page-header"><div><h1>Ajouter une classe</h1></div><a href="liste_classes.php" class="btn btn-secondary">← Retour</a></div>
<div class="card">
    <div class="card-body"><form method="POST">
<div class="form-row">
    <div class="form-group"><label>Libellé *</label><input type="text" name="libelle" class="form-control" value="<?php echo htmlspecialchars($_POST['libelle']??''); ?>" placeholder="ex: 6ème A" required></div>
    <div class="form-group"><label>Niveau</label><input type="text" name="niveau" class="form-control" value="<?php echo htmlspecialchars($_POST['niveau']??''); ?>" placeholder="ex: Collège"></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Capacité max</label><input type="number" name="capacite_max" class="form-control" value="<?php echo $_POST['capacite_max']??30; ?>" min="1"></div>
    <div class="form-group"><label>Année scolaire *</label><input type="text" name="annee_scolaire" class="form-control" value="<?php echo htmlspecialchars($_POST['annee_scolaire']??'2025-2026'); ?>" placeholder="2025-2026" required></div>
</div>
<div class="form-actions"><button type="submit" class="btn btn-primary">+ Enregistrer</button><a href="liste_classes.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>