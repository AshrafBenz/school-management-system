<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Ajouter un parent";
$active_page="parents";
$errors=[];

if($_SERVER['REQUEST_METHOD']=='POST'){

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $profession = trim($_POST['profession']);
    $lien_parente = trim($_POST['lien_parente']);

    if(empty($nom)) $errors[]="Nom obligatoire.";
    if(empty($telephone)) $errors[]="Téléphone obligatoire.";
    if(empty($lien_parente)) $errors[]="Lien de parenté obligatoire.";

    if(empty($errors)){

        $stmt = $pdo->prepare("INSERT INTO parent(nom,prenom,telephone,email,profession,lien_parente) VALUES(?,?,?,?,?,?)");
        $stmt->execute([$nom,$prenom,$telephone,$email,$profession,$lien_parente]);

        $id_parent = $pdo->lastInsertId();

        if(isset($_POST['id_eleve'])){
            foreach($_POST['id_eleve'] as $id_eleve){
                $pdo->prepare("INSERT INTO parent_eleve(id_parent,id_eleve) VALUES(?,?)")
                    ->execute([$id_parent,$id_eleve]);
            }
        }

        $_SESSION['msg']="Parent ajouté.";
        header("Location: liste_parents.php");
        exit();
    }
}

$eleves = $pdo->query("SELECT id_eleve, CONCAT(prenom,' ',nom) AS nom FROM eleve ORDER BY nom")->fetchAll();

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
<div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div><h1>Ajouter un parent</h1></div>
    <a href="liste_parents.php" class="btn btn-secondary">← Retour</a>
</div>

<div class="card">
<div class="card-body">

<form method="POST">

<div class="form-row">
    <div class="form-group">
        <label>Nom *</label>
        <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($_POST['nom']??''); ?>" required>
    </div>

    <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($_POST['prenom']??''); ?>">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Téléphone *</label>
        <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($_POST['telephone']??''); ?>" required>
    </div>

    <div class="form-group">
        <label>Lien de parenté *</label>
        <input type="text" name="lien_parente" class="form-control" value="<?php echo htmlspecialchars($_POST['lien_parente']??''); ?>" placeholder="ex: Père, Mère, Tuteur" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email']??''); ?>">
    </div>

    <div class="form-group">
        <label>Profession</label>
        <input type="text" name="profession" class="form-control" value="<?php echo htmlspecialchars($_POST['profession']??''); ?>">
    </div>
</div>

<div class="form-row">

    <div class="form-group">
        <label>Recherche élève</label>
        <input type="text" id="search" class="form-control" placeholder="Nom ou prénom...">
    </div>

    <div class="form-group">
        <label>Classe</label>
        <select id="classe" class="form-control">
            <option value="">Toutes les classes</option>
            <?php
            $classes = $pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();
            foreach($classes as $c){
                echo "<option value='".$c['id_classe']."'>".$c['libelle']."</option>";
            }
            ?>
        </select>
    </div>

</div>

<div class="form-group">
    <label>Élève *</label>
    <select name="id_eleve[]" id="eleve" class="form-control" multiple required></select>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">+ Enregistrer</button>
    <a href="liste_parents.php" class="btn btn-secondary">Annuler</a>
</div>

</form>
</div>
</div>
<script>
function loadEleves(){
    let search = document.getElementById('search').value;
    let classe = document.getElementById('classe').value;

    fetch("../absences/get_eleves.php?search=" + search + "&classe=" + classe)
    .then(res => res.json())
    .then(data => {
        let select = document.getElementById('eleve');
        select.innerHTML = '';

        data.forEach(e => {
            select.innerHTML += `<option value="${e.id_eleve}">${e.nom}</option>`;
        });
    });
}

// events
document.getElementById('search').addEventListener('keyup', loadEleves);
document.getElementById('classe').addEventListener('change', loadEleves);

window.onload = loadEleves;
</script>
<?php include("../includes/footer.php"); ?>