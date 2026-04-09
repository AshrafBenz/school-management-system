<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title  = "Élèves";
$active_page = "eleves";

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination
$par_page = 10;
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $par_page;

if($search != ''){
    $total = $pdo->prepare("SELECT COUNT(*) FROM eleve WHERE nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?");
    $total->execute(["%$search%", "%$search%", "%$search%"]);
    $stmt = $pdo->prepare("
        SELECT e.*, c.libelle AS classe_nom
        FROM eleve e LEFT JOIN classe c ON e.id_classe = c.id_classe
        WHERE e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?
        ORDER BY e.nom ASC LIMIT $par_page OFFSET $offset
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $total = $pdo->query("SELECT COUNT(*) FROM eleve");
    $stmt = $pdo->prepare("
        SELECT e.*, c.libelle AS classe_nom
        FROM eleve e LEFT JOIN classe c ON e.id_classe = c.id_classe
        ORDER BY e.nom ASC LIMIT $par_page OFFSET $offset
    ");
    $stmt->execute();
}

$nb_total   = $total->fetchColumn();
$nb_pages   = ceil($nb_total / $par_page);
$eleves     = $stmt->fetchAll();

include("../includes/header.php");
?>

<?php if(isset($_SESSION['msg'])): ?>
    <div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['err'])): ?>
    <div class="alert alert-error">✕ <?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Élèves</h1>
        <p><?php echo $nb_total; ?> élève(s) enregistré(s)</p>
    </div>
    <a href="ajouter_eleve.php" class="btn btn-primary">+ Ajouter un élève</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>👨‍🎓 Liste des élèves</h3>
        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control" placeholder="Rechercher nom, prénom, matricule..." value="<?php echo htmlspecialchars($search); ?>" style="width:250px">
            <button type="submit" class="btn btn-secondary btn-sm">Rechercher</button>
            <?php if($search != ''): ?>
                <a href="liste_eleves.php" class="btn btn-secondary btn-sm">Effacer</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(empty($eleves)): ?>
        <div class="empty-state">
            <div class="icon">👨‍🎓</div>
            <h3>Aucun élève trouvé</h3>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom complet</th>
                    <th>Sexe</th>
                    <th>Date naissance</th>
                    <th>Classe</th>
                    <th>Téléphone</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($eleves as $el): ?>
                <tr>
                    <td><span class="badge badge-blue"><?php echo htmlspecialchars($el['matricule']); ?></span></td>
                    <td class="td-bold"><?php echo htmlspecialchars($el['prenom'].' '.$el['nom']); ?></td>
                    <td>
                        <?php if($el['sexe'] == 'M'): ?>
                            <span class="badge badge-blue">M</span>
                        <?php else: ?>
                            <span class="badge badge-purple">F</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $el['date_naissance'] ? date('d/m/Y', strtotime($el['date_naissance'])) : '—'; ?></td>
                    <td><?php echo $el['classe_nom'] ? '<span class="badge badge-gray">'.htmlspecialchars($el['classe_nom']).'</span>' : '—'; ?></td>
                    <td><?php echo htmlspecialchars($el['telephone'] ?? '—'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($el['date_inscription'])); ?></td>
                    <td>
                        <a href="modifier_eleve.php?id=<?php echo $el['id_eleve']; ?>" class="btn btn-secondary btn-sm">✏️</a>
                        <a href="supprimer_eleve.php?id=<?php echo $el['id_eleve']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cet élève ?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($nb_pages > 1): ?>
    <div class="pagination">
        <?php for($i = 1; $i <= $nb_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
               class="page-link <?php echo $i == $page_num ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>