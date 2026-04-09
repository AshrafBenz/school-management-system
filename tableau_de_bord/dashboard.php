<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$page_title  = "Tableau de bord";
$active_page = "dashboard";

$nb_eleves      = $pdo->query("SELECT COUNT(*) FROM eleve")->fetchColumn();
$nb_enseignants = $pdo->query("SELECT COUNT(*) FROM enseignant")->fetchColumn();
$nb_classes     = $pdo->query("SELECT COUNT(*) FROM classe")->fetchColumn();
$nb_abs_nj      = $pdo->query("SELECT COUNT(*) FROM absence WHERE justifiee = 0")->fetchColumn();
$nb_matieres    = $pdo->query("SELECT COUNT(*) FROM matiere")->fetchColumn();
$total_encaisse = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiement WHERE statut = 'Paye'")->fetchColumn();

$derniers_eleves = $pdo->query("
    SELECT e.nom, e.prenom, e.matricule, e.date_inscription, c.libelle AS classe
    FROM eleve e
    LEFT JOIN classe c ON e.id_classe = c.id_classe
    ORDER BY e.date_inscription DESC
    LIMIT 5
")->fetchAll();

$absences_recentes = $pdo->query("
    SELECT a.date_absence, a.nb_heures, CONCAT(e.prenom,' ',e.nom) AS eleve_nom, m.libelle AS matiere
    FROM absence a
    LEFT JOIN eleve e ON a.id_eleve = e.id_eleve
    LEFT JOIN matiere m ON a.id_matiere = m.id_matiere
    WHERE a.justifiee = 0
    ORDER BY a.date_absence DESC
    LIMIT 5
")->fetchAll();

include("../includes/header.php");
?>

<?php if(isset($_SESSION['msg'])): ?>
    <div class="alert alert-success">✓ <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👨‍🎓</div>
        <div class="stat-value"><?php echo $nb_eleves; ?></div>
        <div class="stat-label">Élèves inscrits</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">👨‍🏫</div>
        <div class="stat-value"><?php echo $nb_enseignants; ?></div>
        <div class="stat-label">Enseignants</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">🏫</div>
        <div class="stat-value"><?php echo $nb_classes; ?></div>
        <div class="stat-label">Classes actives</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon">📚</div>
        <div class="stat-value"><?php echo $nb_matieres; ?></div>
        <div class="stat-label">Matières</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value"><?php echo $nb_abs_nj; ?></div>
        <div class="stat-label">Absences non justifiées</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-value" style="font-size:1.2rem"><?php echo number_format($total_encaisse, 0, ',', ' '); ?></div>
        <div class="stat-label">Paiements encaissés (MAD)</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px">

    <div class="card">
        <div class="card-header">
            <h3>👨‍🎓 Derniers élèves inscrits</h3>
            <a href="../eleves/liste_eleves.php" class="btn btn-secondary btn-sm">Voir tout</a>
        </div>
        <?php if(empty($derniers_eleves)): ?>
            <div class="empty-state">
                <div class="icon">👨‍🎓</div>
                <h3>Aucun élève</h3>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Classe</th>
                        <th>Inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($derniers_eleves as $el): ?>
                    <tr>
                        <td><span class="badge badge-blue"><?php echo htmlspecialchars($el['matricule']); ?></span></td>
                        <td class="td-bold"><?php echo htmlspecialchars($el['prenom'].' '.$el['nom']); ?></td>
                        <td><?php echo $el['classe'] ? '<span class="badge badge-gray">'.htmlspecialchars($el['classe']).'</span>' : '—'; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($el['date_inscription'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>⚠️ Absences non justifiées</h3>
            <a href="../absences/liste_absences.php" class="btn btn-secondary btn-sm">Voir tout</a>
        </div>
        <?php if(empty($absences_recentes)): ?>
            <div class="empty-state">
                <div class="icon">✅</div>
                <h3>Aucune absence en attente</h3>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Date</th>
                        <th>Heures</th>
                        <th>Matière</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($absences_recentes as $ab): ?>
                    <tr>
                        <td class="td-bold"><?php echo htmlspecialchars($ab['eleve_nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($ab['date_absence'])); ?></td>
                        <td><strong><?php echo $ab['nb_heures']; ?>h</strong></td>
                        <td><?php echo htmlspecialchars($ab['matiere'] ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php include("../includes/footer.php"); ?>
