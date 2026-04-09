<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM eleve WHERE id_eleve = ?");
$stmt->execute([$id]);
$eleve = $stmt->fetch();

if(!$eleve){
    $_SESSION['err'] = "Élève introuvable.";
    header("Location: liste_eleves.php");
    exit();
}

$page_title  = "Modifier l'élève";
$active_page = "eleves";
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $matricule      = trim($_POST['matricule']);
    $nom            = trim($_POST['nom']);
    $prenom         = trim($_POST['prenom']);
    $date_naissance = $_POST['date_naissance'];
    $sexe           = $_POST['sexe'];
    $adresse        = trim($_POST['adresse']);
    $telephone      = trim($_POST['telephone']);
    $email          = trim($_POST['email']);
    $id_classe      = !empty($_POST['id_classe']) ? $_POST['id_classe'] : null;

    if(empty($matricule)) $errors[] = "Le matricule est obligatoire.";
    if(empty($nom))       $errors[] = "Le nom est obligatoire.";
    if(empty($prenom))    $errors[] = "Le prénom est obligatoire.";

    if(empty($errors)){
        try {
            $pdo->prepare("
                UPDATE eleve SET matricule=?, nom=?, prenom=?, date_naissance=?, sexe=?,
                adresse=?, telephone=?, email=?, id_classe=? WHERE id_eleve=?
            ")->execute([$matricule, $nom, $prenom, $date_naissance, $sexe, $adresse, $telephone, $email, $id_classe, $id]);

            $_SESSION['msg'] = "Élève modifié avec succès.";
            header("Location: liste_eleves.php");
            exit();
        } catch(PDOException $e){
            $errors[] = "Erreur : " . $e->getMessage();
        }
    }
} else {
    // Pré-remplir le formulaire
    $_POST = $eleve;
}

$classes = $pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();

include("../includes/header.php");
?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-error">✕ <?php echo implode('<br>', $errors); ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Modifier l'élève</h1>
        <p><?php echo htmlspecialchars($eleve['prenom'].' '.$eleve['nom']); ?></p>
    </div>
    <a href="liste_eleves.php" class="btn btn-secondary">← Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row-3">
                <div class="form-group">
                    <label>Matricule <span style="color:red">*</span></label>
                    <input type="text" name="matricule" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['matricule'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nom <span style="color:red">*</span></label>
                    <input type="text" name="nom" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom <span style="color:red">*</span></label>
                    <input type="text" name="prenom" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control"
                           value="<?php echo $_POST['date_naissance'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Sexe</label>
                    <select name="sexe" class="form-control">
                        <option value="M" <?php echo ($_POST['sexe'] ?? '') == 'M' ? 'selected' : ''; ?>>Masculin</option>
                        <option value="F" <?php echo ($_POST['sexe'] ?? '') == 'F' ? 'selected' : ''; ?>>Féminin</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Classe</label>
                    <select name="id_classe" class="form-control">
                        <option value="">— Aucune —</option>
                        <?php foreach($classes as $cl): ?>
                            <option value="<?php echo $cl['id_classe']; ?>"
                                <?php echo ($_POST['id_classe'] ?? null) == $cl['id_classe'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cl['libelle']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✓ Enregistrer</button>
                <a href="liste_eleves.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>