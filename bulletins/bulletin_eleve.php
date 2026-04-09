<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title="Bulletins";
$active_page="bulletins";

$id_eleve  = isset($_GET['id_eleve'])  ? (int)$_GET['id_eleve']  : 0;
$trimestre = isset($_GET['trimestre']) ? $_GET['trimestre']     : 'T1';
$annee     = isset($_GET['annee'])     ? trim($_GET['annee'])   : date('Y').'-'.(date('Y')+1);
$search    = isset($_GET['search'])    ? trim($_GET['search'])  : '';

$eleve     = null;
$notes     = [];
$moy_gen   = 0;
$bulletin  = null;

if($id_eleve){

    
    $stmt=$pdo->prepare("
        SELECT e.*, c.libelle AS classe_nom 
        FROM eleve e 
        LEFT JOIN classe c ON e.id_classe=c.id_classe 
        WHERE e.id_eleve=?");
    $stmt->execute([$id_eleve]);
    $eleve=$stmt->fetch();

    $stmt=$pdo->prepare("
        SELECT 
            m.id_matiere,
            m.libelle AS mat,
            m.coefficient AS coef,
            AVG(n.valeur) AS moyenne,
            SUM(n.valeur * m.coefficient) AS note_ponderee,
            GROUP_CONCAT(n.commentaire SEPARATOR ' | ') AS commentaire
        FROM note n
        JOIN matiere m ON n.id_matiere = m.id_matiere
        WHERE n.id_eleve = ? AND n.trimestre = ?
        GROUP BY m.id_matiere
        ORDER BY m.libelle");
    $stmt->execute([$id_eleve, $trimestre]);
    $notes=$stmt->fetchAll();

    if(!empty($notes)){
        $sum_pond = 0;
        $sum_coef = 0;

        foreach($notes as $n){
            $sum_pond += $n['note_ponderee'];
            $sum_coef += $n['coef'];
        }

        $moy_gen = $sum_coef > 0 ? round($sum_pond / $sum_coef, 2) : 0;
    }

    $stmt=$pdo->prepare("
        SELECT * FROM bulletin 
        WHERE id_eleve=? AND trimestre=? AND annee_scolaire=?");
    $stmt->execute([$id_eleve, $trimestre, $annee]);
    $bulletin=$stmt->fetch();
}

if($search != ''){
    $stmt = $pdo->prepare("
        SELECT id_eleve, CONCAT(prenom,' ',nom) AS fn, matricule 
        FROM eleve 
        WHERE nom LIKE ? OR prenom LIKE ?
        ORDER BY nom
    ");
    $stmt->execute(["%$search%", "%$search%"]);
    $eleves = $stmt->fetchAll();
} else {
    $eleves = $pdo->query("
        SELECT id_eleve, CONCAT(prenom,' ',nom) AS fn, matricule 
        FROM eleve ORDER BY nom
    ")->fetchAll();
}

include("../includes/header.php");
?>

<div class="page-header"><div><h1>Bulletins scolaires</h1></div></div>

<div class="card" style="margin-bottom:24px">
<div class="card-body">

<form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap">

    <div class="form-group">
        <label>Recherche élève</label>
        <input type="text" name="search" class="form-control"
               placeholder="Nom ou prénom..."
               value="<?php echo htmlspecialchars($search); ?>">
    </div>

    <div class="form-group">
        <label>Élève</label>
        <select name="id_eleve" class="form-control">
            <option value="">— Sélectionner —</option>
            <?php foreach($eleves as $el): ?>
                <option value="<?php echo $el['id_eleve']; ?>" <?php echo $id_eleve==$el['id_eleve']?'selected':''; ?>>
                    <?php echo htmlspecialchars($el['fn']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    
    <div class="form-group">
        <label>Trimestre</label>
        <select name="trimestre" class="form-control">
            <option value="T1" <?php echo $trimestre=='T1'?'selected':''; ?>>T1</option>
            <option value="T2" <?php echo $trimestre=='T2'?'selected':''; ?>>T2</option>
            <option value="T3" <?php echo $trimestre=='T3'?'selected':''; ?>>T3</option>
        </select>
    </div>

    <div class="form-group">
        <label>Année</label>
        <input type="text" name="annee" value="<?php echo htmlspecialchars($annee); ?>" class="form-control">
    </div>

    <button class="btn btn-primary">🔍 Rechercher</button>

</form>

</div>
</div>

<?php if($eleve): ?>
<div class="card">

<div style="background:#1e3a5f; color:white; padding:24px">
    <h2>Bulletin Scolaire</h2>
    <span><?php echo $trimestre; ?></span>
</div>

<div style="background:#f9fafb; padding:20px">
    <strong><?php echo htmlspecialchars($eleve['prenom'].' '.$eleve['nom']); ?></strong> |
    <?php echo htmlspecialchars($eleve['classe_nom']); ?> |
    Moyenne :
    <strong style="color:<?php echo $moy_gen>=14?'green':($moy_gen>=10?'orange':'red'); ?>">
        <?php echo number_format($moy_gen,2); ?>/20
    </strong>
</div>
<div style="text-align:right; margin:15px;">
    <a href="../bulletins/bulletin_pdf.php?id=<?php echo $id_eleve; ?>" 
       class="btn btn-primary">
       📄 Télécharger PDF
    </a>
</div>

<div class="card-body">
<table>
<thead>
<tr>
<th>Matière</th>
<th>Coefficient</th>
<th>Note /20</th>
<th>Note pondérée</th>
<th>Commentaire</th>
</tr>
</thead>

<tbody>
<?php foreach($notes as $n):
    $v = (float)$n['moyenne'];
    $cls = $v>=14?'note-high':($v>=10?'note-mid':'note-low');
?>
<tr>
<td class="td-bold"><?php echo htmlspecialchars($n['mat']); ?></td>
<td><?php echo $n['coef']; ?></td>
<td class="<?php echo $cls; ?>"><?php echo number_format($v,2); ?></td>
<td><?php echo number_format($n['note_ponderee'],2); ?></td>
<td><?php echo htmlspecialchars($n['commentaire']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</div>
<?php endif; ?>

<?php include("../includes/footer.php"); ?>