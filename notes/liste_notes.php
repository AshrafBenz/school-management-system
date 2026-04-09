<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Notes"; $active_page="notes";
$f_eleve= $_GET['id_eleve'] ?? '';
$f_matiere= $_GET['id_matiere'] ?? '';
$f_trim= $_GET['trimestre'] ?? '';
$par_page=12; $page_num=max(1,(int)($_GET['page']??1)); $offset=($page_num-1)*$par_page;
$conds=[]; $params=[];
if($f_eleve){ $conds[]="n.id_eleve=?";   $params[]=$f_eleve; }
if($f_matiere){ $conds[]="n.id_matiere=?"; $params[]=$f_matiere; }
if($f_trim){ $conds[]="n.trimestre=?";  $params[]=$f_trim; }
$where = $conds ? "WHERE ".implode(" AND ",$conds) : "";
$total=$pdo->prepare("SELECT COUNT(*) FROM note n $where"); $total->execute($params);
$nb_total=$total->fetchColumn(); $nb_pages=ceil($nb_total/$par_page);
$stmt=$pdo->prepare("
    SELECT n.*, CONCAT(e.prenom,' ',e.nom) AS eleve_nom, m.libelle AS matiere_nom,
           CONCAT(ens.prenom,' ',ens.nom) AS prof_nom
    FROM note n
    LEFT JOIN eleve e ON n.id_eleve=e.id_eleve
    LEFT JOIN matiere m ON n.id_matiere=m.id_matiere
    LEFT JOIN enseignant ens ON n.id_enseignant=ens.id_enseignant
    $where ORDER BY n.date_eval DESC LIMIT $par_page OFFSET $offset
");
$stmt->execute($params);
$rows=$stmt->fetchAll();
$eleves = $pdo->query("SELECT id_eleve, CONCAT(prenom,' ',nom) AS fn FROM eleve ORDER BY nom")->fetchAll();
$matieres = $pdo->query("SELECT id_matiere, libelle FROM matiere ORDER BY libelle")->fetchAll();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<div class="page-header">
    <div><h1>Notes</h1><p><?php echo $nb_total; ?> note(s)</p></div>
    <a href="ajouter_note.php" class="btn btn-primary">+ Ajouter une note</a>
</div>
<div class="card">
<div class="card-header">
    <h3>📝 Liste des notes</h3>
    <form method="GET" class="search-box">
        <select name="id_eleve" class="form-control" style="width:auto">
            <option value="">Tous les élèves</option>
            <?php foreach($eleves as $el): ?>
                <option value="<?php echo $el['id_eleve']; ?>" <?php echo $f_eleve==$el['id_eleve']?'selected':''; ?>><?php echo htmlspecialchars($el['fn']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="id_matiere" class="form-control" style="width:auto">
            <option value="">Toutes les matières</option>
            <?php foreach($matieres as $m): ?>
                <option value="<?php echo $m['id_matiere']; ?>" <?php echo $f_matiere==$m['id_matiere']?'selected':''; ?>><?php echo htmlspecialchars($m['libelle']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="trimestre" class="form-control" style="width:auto">
            <option value="">Tous trimestres</option>
            <option value="T1" <?php echo $f_trim=='T1'?'selected':''; ?>>T1</option>
            <option value="T2" <?php echo $f_trim=='T2'?'selected':''; ?>>T2</option>
            <option value="T3" <?php echo $f_trim=='T3'?'selected':''; ?>>T3</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        <a href="liste_notes.php" class="btn btn-secondary btn-sm">✕</a>
    </form>
</div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">📝</div><h3>Aucune note trouvée</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr><th>Élève</th><th>Matière</th><th>Note /20</th><th>Type</th><th>Trimestre</th><th>Date</th><th>Enseignant</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($rows as $r):
    $v=(float)$r['valeur'];
    $cls=$v>=14?'note-high':($v>=10?'note-mid':'note-low');
    $type_badge=['Devoir'=>'badge-blue','Examen'=>'badge-red','TP'=>'badge-green'];
    $tb=$type_badge[$r['type_eval']] ?? 'badge-gray';
?>
<tr>
    <td class="td-bold"><?php echo htmlspecialchars($r['eleve_nom']); ?></td>
    <td><?php echo htmlspecialchars($r['matiere_nom']); ?></td>
    <td class="<?php echo $cls; ?>" style="font-size:1rem"><?php echo number_format($v,2); ?></td>
    <td><span class="badge <?php echo $tb; ?>"><?php echo $r['type_eval']; ?></span></td>
    <td><span class="badge badge-purple"><?php echo $r['trimestre']; ?></span></td>
    <td><?php echo date('d/m/Y',strtotime($r['date_eval'])); ?></td>
    <td style="font-size:.82rem"><?php echo htmlspecialchars($r['prof_nom']??'—'); ?></td>
    <td>
        <a href="supprimer_note.php?id=<?php echo $r['id_note']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
        <a href="modifier_note.php?id=<?php echo $r['id_note']; ?>" class="btn btn-secondary btn-sm">✏️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php if($nb_pages>1): ?>
<div class="pagination">
<?php for($i=1;$i<=$nb_pages;$i++): ?>
<a href="?page=<?php echo $i; ?>&id_eleve=<?php echo urlencode($f_eleve); ?>&id_matiere=<?php echo urlencode($f_matiere); ?>&trimestre=<?php echo urlencode($f_trim); ?>"
   class="page-link <?php echo $i==$page_num?'active':''; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>