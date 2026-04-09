<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Paiements"; $active_page="paiements";
$f_statut = $_GET['statut'] ?? '';
$par_page=12; $page_num=max(1,(int)($_GET['page']??1)); $offset=($page_num-1)*$par_page;
$conds=[]; $params=[];
if($f_statut){ $conds[]="p.statut=?"; $params[]=$f_statut; }
$where=$conds?"WHERE ".implode(" AND ",$conds):"";
$total=$pdo->prepare("SELECT COUNT(*) FROM paiement p $where"); $total->execute($params);
$nb_total=$total->fetchColumn(); $nb_pages=ceil($nb_total/$par_page);
$stmt=$pdo->prepare("
    SELECT p.*, CONCAT(e.prenom,' ',e.nom) AS eleve_nom
    FROM paiement p LEFT JOIN eleve e ON p.id_eleve=e.id_eleve
    $where ORDER BY p.date_paiement DESC LIMIT $par_page OFFSET $offset
");
$stmt->execute($params); $rows=$stmt->fetchAll();
$tot_paye    = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiement WHERE statut='Paye'")->fetchColumn();
$tot_impaye  = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiement WHERE statut='Non_paye'")->fetchColumn();
$nb_impaye   = $pdo->query("SELECT COUNT(*) FROM paiement WHERE statut='Non_paye'")->fetchColumn();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:24px">
    <div class="stat-card green"><div class="stat-icon">✅</div><div class="stat-value" style="font-size:1.3rem"><?php echo number_format($tot_paye,0,'.',','); ?></div><div class="stat-label">Encaissé (MAD)</div></div>
    <div class="stat-card red"><div class="stat-icon">❌</div><div class="stat-value" style="font-size:1.3rem"><?php echo number_format($tot_impaye,0,'.',','); ?></div><div class="stat-label">Impayés (MAD)</div></div>
    <div class="stat-card orange"><div class="stat-icon">⏳</div><div class="stat-value"><?php echo $nb_impaye; ?></div><div class="stat-label">Factures en attente</div></div>
</div>
<div class="page-header">
    <div><h1>Paiements</h1><p><?php echo $nb_total; ?> paiement(s)</p></div>
    <a href="ajouter_paiement.php" class="btn btn-primary">+ Ajouter</a>
</div>
<div class="card">
<div class="card-header">
    <h3>💳 Liste des paiements</h3>
    <form method="GET" class="search-box">
        <select name="statut" class="form-control" style="width:auto">
            <option value="">Tous les statuts</option>
            <option value="Paye"     <?php echo $f_statut=='Paye'?'selected':''; ?>>Payés</option>
            <option value="Non_paye" <?php echo $f_statut=='Non_paye'?'selected':''; ?>>Non payés</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        <a href="liste_paiements.php" class="btn btn-secondary btn-sm">✕</a>
    </form>
</div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">💳</div><h3>Aucun paiement</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr><th>Élève</th><th>Montant</th><th>Motif</th><th>Mode</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
    <td class="td-bold"><?php echo htmlspecialchars($r['eleve_nom']??'—'); ?></td>
    <td><strong><?php echo number_format($r['montant'],2,'.',','); ?> MAD</strong></td>
    <td><?php echo htmlspecialchars($r['motif']); ?></td>
    <td><span class="badge badge-blue"><?php echo $r['mode_paiement']??'—'; ?></span></td>
    <td><?php echo date('d/m/Y',strtotime($r['date_paiement'])); ?></td>
    <td><span class="badge <?php echo $r['statut']=='Paye'?'badge-green':'badge-red'; ?>"><?php echo $r['statut']=='Paye'?'Payé':'Non payé'; ?></span></td>
    <td>
        <a href="toggle_paiement.php?id=<?php echo $r['id_paiement']; ?>" class="btn btn-success btn-sm" title="Basculer statut">⇄</a>
        <a href="supprimer_paiement.php?id=<?php echo $r['id_paiement']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
        <a href="modifier_paiement.php?id=<?php echo $r['id_paiement']; ?>" class="btn btn-secondary btn-sm">✏️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php if($nb_pages>1): ?>
<div class="pagination">
<?php for($i=1;$i<=$nb_pages;$i++): ?>
<a href="?page=<?php echo $i; ?>&statut=<?php echo urlencode($f_statut); ?>" class="page-link <?php echo $i==$page_num?'active':''; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>