<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Enseignants"; $active_page="enseignants";
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$par_page=10; $page_num=max(1,(int)($_GET['page']??1)); $offset=($page_num-1)*$par_page;
if($search != ''){
    $total=$pdo->prepare("SELECT COUNT(*) FROM enseignant WHERE nom LIKE ? OR prenom LIKE ? OR matricule_ens LIKE ?"); $total->execute(["%$search%","%$search%","%$search%"]);
    $stmt=$pdo->prepare("SELECT * FROM enseignant WHERE nom LIKE ? OR prenom LIKE ? OR matricule_ens LIKE ? ORDER BY nom LIMIT $par_page OFFSET $offset"); $stmt->execute(["%$search%","%$search%","%$search%"]);
}else{
    $total=$pdo->query("SELECT COUNT(*) FROM enseignant");
    $stmt=$pdo->prepare("SELECT * FROM enseignant ORDER BY nom LIMIT $par_page OFFSET $offset"); $stmt->execute();
}
$nb_total=$total->fetchColumn(); $nb_pages=ceil($nb_total/$par_page); $rows=$stmt->fetchAll();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<?php if(isset($_SESSION['err'])): ?><div class="alert alert-error">✕ <?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div><?php endif; ?>
<div class="page-header">
    <div><h1>Enseignants</h1><p><?php echo $nb_total; ?> enseignant(s)</p></div>
    <a href="ajouter_enseignant.php" class="btn btn-primary">+ Ajouter</a>
</div>
<div class="card">
<div class="card-header">
    <h3>👨‍🏫 Liste des enseignants</h3>
    <form method="GET" class="search-box">
        <input type="text" name="search" class="form-control" placeholder="Nom, matricule..." value="<?php echo htmlspecialchars($search); ?>" style="width:220px">
        <button type="submit" class="btn btn-secondary btn-sm">OK</button>
        <?php if($search != ''): ?><a href="liste_enseignants.php" class="btn btn-secondary btn-sm">✕</a><?php endif; ?>
    </form>
</div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">👨‍🏫</div><h3>Aucun enseignant</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr>
<th>Matricule</th><th>Nom complet</th><th>Spécialité</th><th>Grade</th><th>Email</th><th>Téléphone</th><th>Embauche</th><th>Actions</th>
</tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
    <td><span class="badge badge-purple"><?php echo htmlspecialchars($r['matricule_ens']); ?></span></td>
    <td class="td-bold"><?php echo htmlspecialchars($r['prenom'].' '.$r['nom']); ?></td>
    <td><?php echo htmlspecialchars($r['specialite']); ?></td>
    <td><?php echo $r['grade'] ? '<span class="badge badge-yellow">'.htmlspecialchars($r['grade']).'</span>' : '—'; ?></td>
    <td style="font-size:.82rem"><?php echo htmlspecialchars($r['email']); ?></td>
    <td><?php echo htmlspecialchars($r['telephone'] ?? '—'); ?></td>
    <td><?php echo date('d/m/Y', strtotime($r['date_embauche'])); ?></td>
    <td>
        <a href="modifier_enseignant.php?id=<?php echo $r['id_enseignant']; ?>" class="btn btn-secondary btn-sm">✏️</a>
        <a href="supprimer_enseignant.php?id=<?php echo $r['id_enseignant']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php if($nb_pages > 1): ?>
<div class="pagination">
<?php for($i=1;$i<=$nb_pages;$i++): ?>
<a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="page-link <?php echo $i==$page_num?'active':''; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>