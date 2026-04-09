<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Matières"; $active_page="matieres";
$rows=$pdo->query("SELECT m.*, COUNT(n.id_note) AS nb_notes FROM matiere m LEFT JOIN note n ON m.id_matiere=n.id_matiere GROUP BY m.id_matiere ORDER BY m.libelle")->fetchAll();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<?php if(isset($_SESSION['err'])): ?><div class="alert alert-error">✕ <?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div><?php endif; ?>
<div class="page-header"><div><h1>Matières</h1><p><?php echo count($rows); ?> matière(s)</p></div><a href="ajouter_matiere.php" class="btn btn-primary">+ Ajouter</a></div>
<div class="card">
<div class="card-header"><h3>📚 Liste des matières</h3></div>
<?php if(empty($rows)): ?>
    <div class="empty-state"><div class="icon">📚</div><h3>Aucune matière</h3></div>
<?php else: ?>
<div class="table-responsive"><table>
<thead><tr><th>Code</th><th>Libellé</th><th>Coefficient</th><th>Type</th><th>Notes saisies</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
    <td><span class="badge badge-blue"><?php echo htmlspecialchars($r['code_matiere']); ?></span></td>
    <td class="td-bold"><?php echo htmlspecialchars($r['libelle']); ?></td>
    <td><strong><?php echo $r['coefficient']; ?></strong></td>
    <td><span class="badge <?php echo $r['type_matiere']=='Obl'?'badge-red':'badge-green'; ?>"><?php echo $r['type_matiere']=='Obl'?'Obligatoire':'Optionnelle'; ?></span></td>
    <td><?php echo $r['nb_notes']; ?></td>
    <td>
        <a href="modifier_matiere.php?id=<?php echo $r['id_matiere']; ?>" class="btn btn-secondary btn-sm">✏️</a>
        <a href="supprimer_matiere.php?id=<?php echo $r['id_matiere']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">🗑️</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
</div>
<?php include("../includes/footer.php"); ?>