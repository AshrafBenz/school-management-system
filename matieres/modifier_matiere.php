<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT * FROM matiere WHERE id_matiere=?"); $stmt->execute([$id]); $mat=$stmt->fetch();
if(!$mat){ $_SESSION['err']="Introuvable."; header("Location: liste_matieres.php"); exit(); }
$page_title="Modifier matière"; $active_page="matieres"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $code=$_POST['code_matiere']; $libelle=$_POST['libelle']; $coef=$_POST['coefficient']; $type=$_POST['type_matiere'];
    if(empty($code)||empty($libelle)) $errors[]="Code et libellé obligatoires.";
    if(empty($errors)){
        try{
            $pdo->prepare("UPDATE matiere SET code_matiere=?,libelle=?,coefficient=?,type_matiere=? WHERE id_matiere=?")->execute([$code,$libelle,$coef,$type,$id]);
            $_SESSION['msg']="Matière modifiée."; header("Location: liste_matieres.php"); exit();
        }catch(PDOException $e){ $errors[]=$e->getMessage(); }
    }
}else{ $_POST=$mat; }
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?><div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="page-header"><div><h1>Modifier la matière</h1></div><a href="liste_matieres.php" class="btn btn-secondary">← Retour</a></div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row">
    <div class="form-group"><label>Code *</label><input type="text" name="code_matiere" class="form-control" value="<?php echo htmlspecialchars($_POST['code_matiere']??''); ?>" required></div>
    <div class="form-group"><label>Libellé *</label><input type="text" name="libelle" class="form-control" value="<?php echo htmlspecialchars($_POST['libelle']??''); ?>" required></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Coefficient</label><input type="number" name="coefficient" class="form-control" value="<?php echo $_POST['coefficient']??1; ?>" step="0.5"></div>
    <div class="form-group"><label>Type</label>
        <select name="type_matiere" class="form-control">
            <option value="Obl" <?php echo ($_POST['type_matiere']??'')=='Obl'?'selected':''; ?>>Obligatoire</option>
            <option value="Opt" <?php echo ($_POST['type_matiere']??'')=='Opt'?'selected':''; ?>>Optionnelle</option>
        </select>
    </div>
</div>
<div class="form-actions"><button type="submit" class="btn btn-primary">✓ Enregistrer</button><a href="liste_matieres.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>