<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Absences"; $active_page="absences";
$f_justif = $_GET['justif'] ?? '';
$par_page=12; $page_num=max(1,(int)($_GET['page']??1)); $offset=($page_num-1)*$par_page;
$conds=[]; $params=[];
if($f_justif !== ''){ $conds[]="a.justifiee=?"; $params[]=$f_justif; }
$where = $conds ? "WHERE ".implode(" AND ",$conds) : "";
$total=$pdo->prepare("SELECT COUNT(*) FROM absence a $where"); $total->execute($params);
$nb_total=$total->fetchColumn(); $nb_pages=ceil($nb_total/$par_page);
$stmt=$pdo->prepare("
    SELECT a.*, CONCAT(e.prenom,' ',e.nom) AS eleve_nom, m.libelle AS matiere_nom
    FROM absence a
    LEFT JOIN eleve e ON a.id_eleve=e.id_eleve
    LEFT JOIN matiere m ON a.id_matiere=m.id_matiere
    $where ORDER BY a.date_absence DESC LIMIT $par_page OFFSET $offset
");
$stmt->execute($params); $rows=$stmt->fetchAll();
$total_abs  = $pdo->query("SELECT COUNT(*) FROM absence")->fetchColumn();
$non_justif = $pdo->query("SELECT COUNT(*) FROM absence WHERE justifiee=0")->fetchColumn();
$total_h    = $pdo->query("SELECT COALESCE(SUM(nb_heures),0) FROM absence")->fetchColumn();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:24px">
    <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-value"><?php echo $total_abs; ?></div><div class="stat-label">Total absences</div></div>
    <div class="stat-card red"><div class="stat-icon">⚠️</div><div class="stat-value"><?php echo $non_justif; ?></div><div class="stat-label">Non justifiées</div></div>
    <div class="stat-card orange"><div class="stat-icon">⏱️</div><div class="stat-value"><?php echo number_format($total_h,1); ?>h</div><div class="stat-label">Heures perdues</div></div>
</div>
<div class="page-header">
    <div><h1>Absences</h1><p><?php echo $nb_total; ?> absence(s)</p></div>
    <a href="ajouter_absence.php" class="btn btn-primary">+ Enregistrer</a>
</div>
<div class="card">
<div class="card-header">
    <h3>📅 Registre des absences</h3>
    <form method="GET" class="search-box">
        <select name="justif" class="form-control" style="width:auto">
            <option value="">Toutes</option>
            <option value="0" <?php echo $f_justif==='0'?'selected':''; ?>>Non justifiées</option>
            <option value="1" <?php echo $f_justif==='1'?'selected':''; ?>>Justifiées</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        <a href="liste_absences.php" class="btn btn-secondary btn-sm">✕</a>
    </form>
</div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">📅</div><h3>Aucune absence</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr><th>Élève</th><th>Date</th><th>Heures</th><th>Matière</th><th>Motif</th><th>Statut</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
    <td class="td-bold"><?php echo htmlspecialchars($r['eleve_nom']??'—'); ?></td>
    <td><?php echo date('d/m/Y',strtotime($r['date_absence'])); ?></td>
    <td><strong><?php echo $r['nb_heures']; ?>h</strong></td>
    <td><?php echo htmlspecialchars($r['matiere_nom']??'—'); ?></td>
    <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo htmlspecialchars($r['motif']??'—'); ?></td>
    <td>
        <?php if($r['justifiee']): ?>
            <span class="badge badge-green">Justifiée</span>
        <?php else: ?>
            <span class="badge badge-red">Non justifiée</span>
        <?php endif; ?>
    </td>
    <td>
        <a href="justifier_absence.php?id=<?php echo $r['id_absence']; ?>" class="btn btn-success btn-sm" title="Basculer justification">✓</a>
        <a href="supprimer_absence.php?id=<?php echo $r['id_absence']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php if($nb_pages>1): ?>
<div class="pagination">
<?php for($i=1;$i<=$nb_pages;$i++): ?>
<a href="?page=<?php echo $i; ?>&justif=<?php echo urlencode($f_justif); ?>" class="page-link <?php echo $i==$page_num?'active':''; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>