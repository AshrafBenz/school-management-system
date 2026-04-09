<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Parents";
$active_page="parents";

// 🔍 RECHERCHE
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if($search != ''){
    $stmt = $pdo->prepare("
        SELECT * FROM parent 
        WHERE nom LIKE ? OR prenom LIKE ? OR telephone LIKE ?
        ORDER BY nom
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $rows = $stmt->fetchAll();
} else {
    $rows = $pdo->query("SELECT * FROM parent ORDER BY nom")->fetchAll();
}

include("../includes/header.php");
?>

<?php if(isset($_SESSION['msg'])): ?>
<div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Parents / Tuteurs</h1>
        <p><?php echo count($rows); ?> contact(s)</p>
    </div>
    <a href="ajouter_parent.php" class="btn btn-primary">+ Ajouter</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>👨‍👩‍👧 Liste des parents</h3>

        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control"
                   placeholder="Rechercher nom, prénom, téléphone..."
                   value="<?php echo htmlspecialchars($search); ?>"
                   style="width:250px">

            <button type="submit" class="btn btn-secondary btn-sm">Rechercher</button>

            <?php if($search != ''): ?>
                <a href="liste_parents.php" class="btn btn-secondary btn-sm">Effacer</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(empty($rows)): ?>

        <div class="empty-state">
            <div class="icon">👨‍👩‍👧</div>
            <h3>Aucun parent trouvé</h3>
        </div>

    <?php else: ?>

        <div class="table-responsive">
        <table>
        <thead>
        <tr>
            <th>Nom complet</th>
            <th>Lien</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Profession</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td class="td-bold"><?php echo htmlspecialchars($r['prenom'].' '.$r['nom']); ?></td>
            <td><span class="badge badge-purple"><?php echo htmlspecialchars($r['lien_parente']); ?></span></td>
            <td><?php echo htmlspecialchars($r['telephone']); ?></td>
            <td style="font-size:.82rem"><?php echo htmlspecialchars($r['email']??'—'); ?></td>
            <td><?php echo htmlspecialchars($r['profession']??'—'); ?></td>
            <td>
                <a href="modifier_parent.php?id=<?php echo $r['id_parent']; ?>" class="btn btn-secondary btn-sm">✏️</a>
                <a href="supprimer_parent.php?id=<?php echo $r['id_parent']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>

        </table>
        </div>

    <?php endif; ?>

</div>

<?php include("../includes/footer.php"); ?>