<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Modifier note";
$active_page="notes";

$errors=[];
$id_note = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 🔥 récupérer note
$stmt = $pdo->prepare("
    SELECT n.*, e.id_eleve, CONCAT(e.prenom,' ',e.nom) AS eleve_nom, 
           m.id_matiere, m.libelle AS matiere_nom
    FROM note n
    JOIN eleve e ON n.id_eleve = e.id_eleve
    JOIN matiere m ON n.id_matiere = m.id_matiere
    WHERE n.id_note = ?
");
$stmt->execute([$id_note]);
$note = $stmt->fetch();

if(!$note){
    header("Location: liste_notes.php");
    exit();
}

// 🔥 update
if($_SERVER['REQUEST_METHOD']=='POST'){

    $valeur    = $_POST['valeur'];
    $trimestre = $_POST['trimestre'];
    $commentaire = trim($_POST['commentaire']);

    if(empty($valeur)) $errors[]="Note obligatoire.";

    if(empty($errors)){
        $pdo->prepare("
            UPDATE note 
            SET valeur=?, trimestre=?, commentaire=? 
            WHERE id_note=?
        ")->execute([$valeur,$trimestre,$commentaire,$id_note]);

        $_SESSION['msg']="Note modifiée.";
        header("Location: liste_notes.php");
        exit();
    }
}

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
<div class="alert alert-error">✕ <?php echo implode('<br>',$errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div><h1>Modifier note</h1></div>
    <a href="liste_notes.php" class="btn btn-secondary">← Retour</a>
</div>

<div class="card">
<div class="card-body">

<form method="POST">

<div class="form-group">
    <label>Élève</label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($note['eleve_nom']); ?>" disabled>
</div>

<div class="form-group">
    <label>Matière</label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($note['matiere_nom']); ?>" disabled>
</div>

<div class="form-row">

    <div class="form-group">
        <label>Note /20 *</label>
        <input type="number" name="valeur" class="form-control"
               value="<?php echo htmlspecialchars($note['valeur']); ?>"
               min="0" max="20" step="0.01" required>
    </div>

    <div class="form-group">
        <label>Trimestre</label>
        <select name="trimestre" class="form-control">
            <option value="T1" <?php echo $note['trimestre']=='T1'?'selected':''; ?>>T1</option>
            <option value="T2" <?php echo $note['trimestre']=='T2'?'selected':''; ?>>T2</option>
            <option value="T3" <?php echo $note['trimestre']=='T3'?'selected':''; ?>>T3</option>
        </select>
    </div>

</div>

<div class="form-group">
    <label>Commentaire</label>
    <textarea name="commentaire" class="form-control" rows="3"><?php echo htmlspecialchars($note['commentaire']??''); ?></textarea>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
    <a href="liste_notes.php" class="btn btn-secondary">Annuler</a>
</div>

</form>

</div>
</div>

<?php include("../includes/footer.php"); ?>