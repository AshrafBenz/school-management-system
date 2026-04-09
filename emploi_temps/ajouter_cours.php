<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Ajouter un cours"; $active_page="emploi_temps"; $errors=[];
$jours=['Lun'=>'Lundi','Mar'=>'Mardi','Mer'=>'Mercredi','Jeu'=>'Jeudi','Ven'=>'Vendredi','Sam'=>'Samedi'];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $jour = $_POST['jour'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $salle = trim($_POST['salle']);
    $id_classe = !empty($_POST['id_classe'])    ? $_POST['id_classe']    : null;
    $id_matiere = !empty($_POST['id_matiere'])   ? $_POST['id_matiere']   : null;
    $id_ens = !empty($_POST['id_enseignant'])? $_POST['id_enseignant']: null;
    if(empty($id_matiere)) $errors[]="Matière obligatoire.";
    if(empty($errors)){
        $pdo->prepare("INSERT INTO emploi_temps(jour,heure_debut,heure_fin,salle,id_classe,id_enseignant,id_matiere) VALUES(?,?,?,?,?,?,?)")
            ->execute([$jour,$heure_debut,$heure_fin,$salle,$id_classe,$id_ens,$id_matiere]);
        $_SESSION['msg']="Cours ajouté."; header("Location: liste_emploi.php"); exit();
    }
}
$classes = $pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();
$matieres = $pdo->query("SELECT id_matiere, libelle FROM matiere ORDER BY libelle")->fetchAll();
$enseignants = $pdo->query("SELECT id_enseignant, CONCAT(prenom,' ',nom) AS fn FROM enseignant ORDER BY nom")->fetchAll();
include("../includes/header.php");
?>
<?php if(!empty($errors)): ?><div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="page-header"><div><h1>Ajouter un cours</h1></div><a href="liste_emploi.php" class="btn btn-secondary">← Retour</a></div>
<div class="card"><div class="card-body"><form method="POST">
<div class="form-row-3">
    <div class="form-group"><label>Jour</label>
        <select name="jour" class="form-control">
            <?php foreach($jours as $code=>$nom): ?><option value="<?php echo $code; ?>"><?php echo $nom; ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Heure début *</label><input type="time" name="heure_debut" class="form-control" value="08:00" required></div>
    <div class="form-group"><label>Heure fin *</label><input type="time" name="heure_fin" class="form-control" value="09:00" required></div>
</div>
<div class="form-row">
    <div class="form-group"><label>Matière *</label>
        <select name="id_matiere" class="form-control" required>
            <option value="">Sélectionner...</option>
            <?php foreach($matieres as $m): ?><option value="<?php echo $m['id_matiere']; ?>"><?php echo htmlspecialchars($m['libelle']); ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Classe</label>
        <select name="id_classe" class="form-control">
            <option value="">—</option>
            <?php foreach($classes as $cl): ?><option value="<?php echo $cl['id_classe']; ?>"><?php echo htmlspecialchars($cl['libelle']); ?></option><?php endforeach; ?>
        </select>
    </div>
</div>
<div class="form-row">
    <div class="form-group"><label>Enseignant</label>
        <select name="id_enseignant" class="form-control">
            <option value="">—</option>
            <?php foreach($enseignants as $ens): ?><option value="<?php echo $ens['id_enseignant']; ?>"><?php echo htmlspecialchars($ens['fn']); ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="form-group"><label>Salle</label><input type="text" name="salle" class="form-control" placeholder="ex: A101"></div>
</div>
<div class="form-actions"><button type="submit" class="btn btn-primary">+ Enregistrer</button><a href="liste_emploi.php" class="btn btn-secondary">Annuler</a></div>
</form></div></div>
<?php include("../includes/footer.php"); ?>