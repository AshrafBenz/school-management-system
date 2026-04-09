<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Modifier paiement";
$active_page="paiements";

$errors=[];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 🔥 récupérer paiement
$stmt = $pdo->prepare("
    SELECT p.*, CONCAT(e.prenom,' ',e.nom) AS eleve_nom
    FROM paiement p
    JOIN eleve e ON p.id_eleve = e.id_eleve
    WHERE p.id_paiement = ?
");
$stmt->execute([$id]);
$paiement = $stmt->fetch();

if(!$paiement){
    header("Location: liste_paiements.php");
    exit();
}

// 🔥 récupérer élèves (pour modification)
$eleves = $pdo->query("SELECT id_eleve, CONCAT(prenom,' ',nom) AS nom FROM eleve ORDER BY nom")->fetchAll();

// 🔥 update
if($_SERVER['REQUEST_METHOD']=='POST'){

    $montant       = $_POST['montant'];
    $date_paiement = $_POST['date_paiement'];
    $mode          = $_POST['mode_paiement'];
    $motif         = trim($_POST['motif']);
    $statut        = $_POST['statut'];
    $id_eleve      = $_POST['id_eleve'];

    if(empty($id_eleve)) $errors[]="Élève obligatoire.";
    if(empty($montant))  $errors[]="Montant obligatoire.";
    if(empty($motif))    $errors[]="Motif obligatoire.";

    if(empty($errors)){
        $pdo->prepare("
            UPDATE paiement 
            SET montant=?, date_paiement=?, mode_paiement=?, motif=?, statut=?, id_eleve=?
            WHERE id_paiement=?
        ")->execute([$montant,$date_paiement,$mode,$motif,$statut,$id_eleve,$id]);

        $_SESSION['msg']="Paiement modifié.";
        header("Location: liste_paiements.php");
        exit();
    }
}

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
<div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div><h1>Modifier paiement</h1></div>
    <a href="liste_paiements.php" class="btn btn-secondary">← Retour</a>
</div>

<div class="card">
<div class="card-body">

<form method="POST">

<!-- 👨‍🎓 ELEVE -->
<div class="form-group">
    <label>Élève *</label>
    <select name="id_eleve" class="form-control" required>
        <?php foreach($eleves as $e): ?>
            <option value="<?php echo $e['id_eleve']; ?>" 
                <?php echo $paiement['id_eleve']==$e['id_eleve']?'selected':''; ?>>
                <?php echo htmlspecialchars($e['nom']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- 💰 INFOS -->
<div class="form-row">
    <div class="form-group">
        <label>Montant (MAD) *</label>
        <input type="number" name="montant" class="form-control"
               value="<?php echo htmlspecialchars($paiement['montant']); ?>"
               min="0" step="0.01" required>
    </div>

    <div class="form-group">
        <label>Date *</label>
        <input type="date" name="date_paiement" class="form-control"
               value="<?php echo htmlspecialchars($paiement['date_paiement']); ?>" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Mode de paiement</label>
        <select name="mode_paiement" class="form-control">
            <option value="Especes" <?php echo $paiement['mode_paiement']=='Especes'?'selected':''; ?>>Espèces</option>
            <option value="Carte" <?php echo $paiement['mode_paiement']=='Carte'?'selected':''; ?>>Carte bancaire</option>
            <option value="Virement" <?php echo $paiement['mode_paiement']=='Virement'?'selected':''; ?>>Virement</option>
        </select>
    </div>

    <div class="form-group">
        <label>Statut</label>
        <select name="statut" class="form-control">
            <option value="Paye" <?php echo $paiement['statut']=='Paye'?'selected':''; ?>>Payé</option>
            <option value="Non_paye" <?php echo $paiement['statut']=='Non_paye'?'selected':''; ?>>Non payé</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Motif *</label>
    <input type="text" name="motif" class="form-control"
           value="<?php echo htmlspecialchars($paiement['motif']); ?>" required>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
    <a href="liste_paiements.php" class="btn btn-secondary">Annuler</a>
</div>

</form>

</div>
</div>

<?php include("../includes/footer.php"); ?>