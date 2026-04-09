<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$page_title="Emploi du temps"; $active_page="emploi_temps";
$f_classe = isset($_GET['id_classe']) ? (int)$_GET['id_classe'] : 0;
$jours = ['Lun'=>'Lundi','Mar'=>'Mardi','Mer'=>'Mercredi','Jeu'=>'Jeudi','Ven'=>'Vendredi','Sam'=>'Samedi'];
$where = $f_classe ? "WHERE et.id_classe=?" : "";
$params = $f_classe ? [$f_classe] : [];
$stmt=$pdo->prepare("
    SELECT et.*, c.libelle AS classe_nom, m.libelle AS mat_nom,
           CONCAT(ens.prenom,' ',ens.nom) AS prof_nom
    FROM emploi_temps et
    LEFT JOIN classe c ON et.id_classe=c.id_classe
    LEFT JOIN matiere m ON et.id_matiere=m.id_matiere
    LEFT JOIN enseignant ens ON et.id_enseignant=ens.id_enseignant
    $where
    ORDER BY FIELD(et.jour,'Lun','Mar','Mer','Jeu','Ven','Sam'), et.heure_debut
");
$stmt->execute($params); $all=$stmt->fetchAll();
$byDay=[]; foreach(array_keys($jours) as $j) $byDay[$j]=[];
foreach($all as $s) $byDay[$s['jour']][]=$s;
$classes=$pdo->query("SELECT id_classe, libelle FROM classe ORDER BY libelle")->fetchAll();
include("../includes/header.php");
?>
<?php if(isset($_SESSION['msg'])): ?><div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div><?php endif; ?>
<div class="page-header">
    <div><h1>Emploi du temps</h1></div>
    <div style="display:flex;gap:8px">
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <select name="id_classe" class="form-control" style="width:auto">
                <option value="">Toutes les classes</option>
                <?php foreach($classes as $cl): ?>
                    <option value="<?php echo $cl['id_classe']; ?>" <?php echo $f_classe==$cl['id_classe']?'selected':''; ?>><?php echo htmlspecialchars($cl['libelle']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
        </form>
        <a href="ajouter_cours.php" class="btn btn-primary">+ Ajouter un cours</a>
    </div>
</div>
<div class="card">
<div class="card-body">
<div class="planning-grid">
<?php foreach($jours as $code => $nom): ?>
<div>
    <div class="day-title"><?php echo $nom; ?></div>
    <?php if(empty($byDay[$code])): ?>
        <div style="text-align:center;color:#d1d5db;font-size:.75rem;padding:12px">—</div>
    <?php else: ?>
        <?php foreach($byDay[$code] as $s): ?>
        <div class="slot">
            <div class="slot-time"><?php echo substr($s['heure_debut'],0,5).' – '.substr($s['heure_fin'],0,5); ?></div>
            <div class="slot-subject"><?php echo htmlspecialchars($s['mat_nom']??'—'); ?></div>
            <div class="slot-teacher"><?php echo htmlspecialchars($s['prof_nom']??'—'); ?></div>
            <?php if($s['salle']): ?><div class="slot-room">📍 <?php echo htmlspecialchars($s['salle']); ?></div><?php endif; ?>
            <a href="supprimer_cours.php?id=<?php echo $s['id_edt']; ?>" class="slot-delete" onclick="return confirm('Supprimer ?')">✕</a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
<?php include("../includes/footer.php"); ?>