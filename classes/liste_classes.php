<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Classes"; $active_page="classes";
$rows=$pdo->query("SELECT c.*, COUNT(e.id_eleve) AS nb_eleves FROM classe c LEFT JOIN eleve e ON c.id_classe=e.id_classe GROUP BY c.id_classe ORDER BY c.annee_scolaire DESC, c.libelle")->fetchAll();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<?php if(isset($_SESSION['err'])): ?><div class="alert alert-error">✕ <?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div><?php endif; ?>
<div class="page-header">
    <div><h1>Classes</h1><p><?php echo count($rows); ?> classe(s)</p></div>
    <a href="ajouter_classe.php" class="btn btn-primary">+ Ajouter</a>
</div>
<div class="card">
<div class="card-header"><h3>🏫 Liste des classes</h3></div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">🏫</div><h3>Aucune classe</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr><th>Libellé</th><th>Niveau</th><th>Année scolaire</th><th>Capacité max</th><th>Effectif</th><th>Taux remplissage</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<?php $pct = $r['capacite_max']>0 ? round(($r['nb_eleves']/$r['capacite_max'])*100) : 0; ?>
<tr>
    <td class="td-bold"><?php echo htmlspecialchars($r['libelle']); ?></td>
    <td><span class="badge badge-blue"><?php echo htmlspecialchars($r['niveau']); ?></span></td>
    <td><?php echo htmlspecialchars($r['annee_scolaire']); ?></td>
    <td><?php echo $r['capacite_max']; ?></td>
    <td><strong><?php echo $r['nb_eleves']; ?></strong></td>
    <td><span class="badge <?php echo $pct>90?'badge-red':($pct>70?'badge-yellow':'badge-green'); ?>"><?php echo $pct; ?>%</span></td>
    <td>
        <a href="modifier_classe.php?id=<?php echo $r['id_classe']; ?>" class="btn btn-secondary btn-sm">✏️</a>
        <a href="supprimer_classe.php?id=<?php echo $r['id_classe']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>