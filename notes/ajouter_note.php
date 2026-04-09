<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Ajouter une note"; $active_page="notes"; $errors=[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $valeur       = $_POST['valeur'];
    $type_eval    = $_POST['type_eval'];
    $date_eval    = $_POST['date_eval'];
    $trimestre    = $_POST['trimestre'];
    $commentaire  = trim($_POST['commentaire']);
    $id_eleve     = $_POST['id_eleve'];
    $id_matiere   = $_POST['id_matiere'];
    $id_enseignant= !empty($_POST['id_enseignant']) ? $_POST['id_enseignant'] : null;
    if(empty($id_eleve))  $errors[]="Élève obligatoire.";
    if(empty($id_matiere))$errors[]="Matière obligatoire.";
    if($valeur===''||!is_numeric($valeur)) $errors[]="Note invalide.";
    if(empty($errors)){
        $pdo->prepare("INSERT INTO note(valeur,type_eval,date_eval,trimestre,commentaire,id_eleve,id_enseignant,id_matiere) VALUES(?,?,?,?,?,?,?,?)")
            ->execute([$valeur,$type_eval,$date_eval,$trimestre,$commentaire,$id_eleve,$id_enseignant,$id_matiere]);
        $_SESSION['msg']="Note ajoutée."; header("Location: liste_notes.php"); exit();
    }
}
$eleves      = $pdo->query("SELECT id_eleve, CONCAT(prenom,' ',nom) AS fn FROM eleve ORDER BY nom")->fetchAll();
$matieres    = $pdo->query("SELECT id_matiere, libelle FROM matiere ORDER BY libelle")->fetchAll();
$enseignants = $pdo->query("SELECT id_enseignant, CONCAT(prenom,' ',nom) AS fn FROM enseignant ORDER BY nom")->fetchAll();
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?><div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="page-header"><div><h1>Ajouter une note</h1></div><a href="liste_notes.php" class="btn btn-secondary">← Retour</a></div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row">
    <div class="form-group"><label>Élève *</label>
        <select name="id_eleve" class="form-control" required>
            <option value="">Sélectionner...</option>
            <?php foreach($eleves as $el): ?><option value="<?php echo $el['id_eleve']; ?>" <?php echo ($_POST['id_eleve']??'')==$el['id_eleve']?'selected':''; ?>><?php echo htmlspecialchars($el['fn']); ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Matière *</label>
        <select name="id_matiere" class="form-control" required>
            <option value="">Sélectionner...</option>
            <?php foreach($matieres as $m): ?><option value="<?php echo $m['id_matiere']; ?>" <?php echo ($_POST['id_matiere']??'')==$m['id_matiere']?'selected':''; ?>><?php echo htmlspecialchars($m['libelle']); ?></option><?php endforeach; ?>
        </select>
    </div>
</div>
<div class="form-row-3">
    <div class="form-group"><label>Note /20 *</label><input type="number" name="valeur" class="form-control" value="<?php echo $_POST['valeur']??''; ?>" min="0" max="20" step="0.25" required></div>
    <div class="form-group"><label>Type</label>
        <select name="type_eval" class="form-control">
            <option value="Devoir">Devoir</option>
            <option value="Examen">Examen</option>
            <option value="TP">TP</option>
        </select>
    </div>
    <div class="form-group"><label>Trimestre</label>
        <select name="trimestre" class="form-control">
            <option value="T1">T1</option><option value="T2">T2</option><option value="T3">T3</option>
        </select>
    </div>
</div>
<div class="form-row">
    <div class="form-group"><label>Date évaluation</label><input type="date" name="date_eval" class="form-control" value="<?php echo $_POST['date_eval']??date('Y-m-d'); ?>"></div>
    <div class="form-group"><label>Enseignant</label>
        <select name="id_enseignant" class="form-control">
            <option value="">— Optionnel —</option>
            <?php foreach($enseignants as $ens): ?><option value="<?php echo $ens['id_enseignant']; ?>"><?php echo htmlspecialchars($ens['fn']); ?></option><?php endforeach; ?>
        </select>
    </div>
</div>
<div class="form-group"><label>Commentaire</label><textarea name="commentaire" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['commentaire']??''); ?></textarea></div>
<div class="form-actions"><button type="submit" class="btn btn-primary">+ Enregistrer</button><a href="liste_notes.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>