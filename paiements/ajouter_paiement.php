<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Ajouter un paiement";
$active_page="paiements";
$errors=[];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $montant= $_POST['montant'];
    $date_paiement= $_POST['date_paiement'];
    $mode= $_POST['mode_paiement'];
    $motif= trim($_POST['motif']);
    $statut= $_POST['statut'];
    $id_eleve= $_POST['id_eleve'];

    if(empty($id_eleve))$errors[]="Élève obligatoire.";
    if(empty($montant)) $errors[]="Montant obligatoire.";

    if(empty($errors)){
        $pdo->prepare("INSERT INTO paiement(montant,date_paiement,mode_paiement,motif,statut,id_eleve) VALUES(?,?,?,?,?,?)")
            ->execute([$montant,$date_paiement,$mode,$motif,$statut,$id_eleve]);

        $_SESSION['msg']="Paiement enregistré.";
        header("Location: liste_paiements.php");
        exit();
    }
}

$classes = $pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
<div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div><h1>Nouveau paiement</h1></div>
    <a href="liste_paiements.php" class="btn btn-secondary">← Retour</a>
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

<div class="form-group">
    <label>Élève *</label>
    <select name="id_eleve" id="eleve" class="form-control" required>
        <option value="">Sélectionner...</option>
    </select>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Montant (MAD) *</label>
        <input type="number" name="montant" class="form-control" value="<?php echo $_POST['montant']??''; ?>" min="0" step="0.01" required>
    </div>

    <div class="form-group">
        <label>Date *</label>
        <input type="date" name="date_paiement" class="form-control" value="<?php echo $_POST['date_paiement']??date('Y-m-d'); ?>" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Mode de paiement</label>
        <select name="mode_paiement" class="form-control">
            <option value="Especes">Espèces</option>
            <option value="Carte">Carte bancaire</option>
            <option value="Virement">Virement</option>
        </select>
    </div>

    <div class="form-group">
        <label>Statut</label>
        <select name="statut" class="form-control">
            <option value="Paye">Payé</option>
            <option value="Non_paye">Non payé</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Motif *</label>
    <input type="text" name="motif" class="form-control" value="<?php echo htmlspecialchars($_POST['motif']??''); ?>" required>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">+ Enregistrer</button>
    <a href="liste_paiements.php" class="btn btn-secondary">Annuler</a>
</div>

</form>
</div>
</div>

<script>
function loadEleves(){
    let search = document.getElementById('search').value;
    let classe = document.getElementById('classe').value;

    fetch("gets_eleves1.php?search=" + search + "&classe=" + classe)
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
window.onload = loadEleves;
</script>

<?php include("../includes/footer.php"); ?>