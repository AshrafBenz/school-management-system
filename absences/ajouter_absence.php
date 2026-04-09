<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Enregistrer une absence";
$active_page="absences";
$errors=[];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $date_absence = $_POST['date_absence'];
    $nb_heures    = $_POST['nb_heures'];
    $motif        = trim($_POST['motif']);
    $justifiee    = isset($_POST['justifiee']) ? 1 : 0;
    $id_eleve     = $_POST['id_eleve'];
    $id_matiere   = !empty($_POST['id_matiere']) ? $_POST['id_matiere'] : null;

    if(empty($id_eleve))     $errors[]="Élève obligatoire.";
    if(empty($date_absence)) $errors[]="Date obligatoire.";

    if(empty($errors)){
        $pdo->prepare("INSERT INTO absence(date_absence,nb_heures,motif,justifiee,id_eleve,id_matiere) VALUES(?,?,?,?,?,?)")
            ->execute([$date_absence,$nb_heures,$motif,$justifiee,$id_eleve,$id_matiere]);

        $_SESSION['msg']="Absence enregistrée.";
        header("Location: liste_absences.php");
        exit();
    }
}

$classes = $pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();

$matieres = $pdo->query("SELECT id_matiere, libelle FROM matiere ORDER BY libelle")->fetchAll();

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
<div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div><h1>Enregistrer une absence</h1></div>
    <a href="liste_absences.php" class="btn btn-secondary">← Retour</a>
</div>

<div class="card">
<div class="card-body">

<form method="POST">

<div class="form-row">


    <div class="form-group">
        <label>Recherche élève</label>
        <input type="text" id="search" class="form-control" placeholder="Nom ou prénom...">
    </div>

    
    <div class="form-group">
        <label>Classe</label>
        <select id="classe" class="form-control">
            <option value="">Toutes les classes</option>
            <?php foreach($classes as $c): ?>
                <option value="<?php echo $c['id_classe']; ?>">
                    <?php echo htmlspecialchars($c['libelle']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

</div>

<div class="form-row">

    
    <div class="form-group">
        <label>Élève *</label>
        <select name="id_eleve" id="eleve" class="form-control" required>
            <option value="">Sélectionner...</option>
        </select>
    </div>

    <div class="form-group">
        <label>Matière</label>
        <select name="id_matiere" class="form-control">
            <option value="">— Optionnelle —</option>
            <?php foreach($matieres as $m): ?>
                <option value="<?php echo $m['id_matiere']; ?>">
                    <?php echo htmlspecialchars($m['libelle']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

</div>

<div class="form-row">
    <div class="form-group">
        <label>Date *</label>
        <input type="date" name="date_absence" class="form-control" value="<?php echo $_POST['date_absence']??date('Y-m-d'); ?>" required>
    </div>

    <div class="form-group">
        <label>Nombre d'heures *</label>
        <input type="number" name="nb_heures" class="form-control" value="<?php echo $_POST['nb_heures']??1; ?>" min="0.5" step="0.5" required>
    </div>
</div>

<div class="form-group">
    <label>Motif</label>
    <textarea name="motif" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['motif']??''); ?></textarea>
</div>

<div class="form-group">
    <label><input type="checkbox" name="justifiee"> Absence justifiée</label>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">+ Enregistrer</button>
    <a href="liste_absences.php" class="btn btn-secondary">Annuler</a>
</div>

</form>
</div>
</div>

<script>
function loadEleves(){
    let search = document.getElementById('search').value;
    let classe = document.getElementById('classe').value;

    fetch("get_eleves.php?search=" + search + "&classe=" + classe)
    .then(res => res.json())
    .then(data => {
        let select = document.getElementById('eleve');
        select.innerHTML = '<option value="">Sélectionner...</option>';

        data.forEach(e => {
            select.innerHTML += `<option value="${e.id_eleve}">${e.nom}</option>`;
        });
    });
}

document.getElementById('search').addEventListener('keyup', loadEleves);
document.getElementById('classe').addEventListener('change', loadEleves);
</script>

<?php include("../includes/footer.php"); ?>