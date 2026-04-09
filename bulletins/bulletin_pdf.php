<?php
require_once("../connexion/connexion.php");
require_once("../dompdf/autoload.inc.php");

use Dompdf\Dompdf;

$id_eleve = $_GET['id'] ?? 0;

// élève
$stmt = $pdo->prepare("
    SELECT e.*, c.libelle AS classe
    FROM eleve e
    JOIN classe c ON e.id_classe=c.id_classe
    WHERE e.id_eleve=?
");
$stmt->execute([$id_eleve]);
$eleve = $stmt->fetch();

// notes
$stmt = $pdo->prepare("
    SELECT m.libelle, m.coefficient, n.valeur
    FROM note n
    JOIN matiere m ON n.id_matiere=m.id_matiere
    WHERE n.id_eleve=?
");
$stmt->execute([$id_eleve]);
$notes = $stmt->fetchAll();

$total = 0;
$coef_total = 0;

// HTML START
$html = '
<style>
body { font-family: DejaVu Sans; }

.header-table {
    width: 100%;
    margin-bottom: 20px;
}

.header-table td {
    border: none;
}

.title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
}
</style>

<table class="header-table">
<tr>
    <td><img src="'.__DIR__.'/../images/logo_ecole.png" height="60"></td>
    <td class="title">
        🎓EduManager<br>
        Bulletin Scolaire
    </td>
    <td style="text-align:right;">
        <img src="'.__DIR__.'/../images/logo_maroc.png" height="60">
    </td>
</tr>
</table>

<div>
<strong>Nom:</strong> '.$eleve['prenom'].' '.$eleve['nom'].'<br>
<strong>Classe:</strong> '.$eleve['classe'].'<br>
<strong>Année:</strong> 2025-2026
</div>

<br>

<table class="table">
<thead>
<tr>
    <th>Matière</th>
    <th>Coef</th>
    <th>Note</th>
    <th>Pondérée</th>
</tr>
</thead>
<tbody>
';

// LOOP
foreach($notes as $n){

    $pond = $n['valeur'] * $n['coefficient'];
    $total += $pond;
    $coef_total += $n['coefficient'];

    $html .= '
    <tr>
        <td>'.$n['libelle'].'</td>
        <td>'.$n['coefficient'].'</td>
        <td>'.$n['valeur'].'</td>
        <td>'.$pond.'</td>
    </tr>';
}

// moyenne
$moyenne = $coef_total ? round($total/$coef_total,2) : 0;

// CLOSE HTML
$html .= '
</tbody>
</table>

<h3>Moyenne générale : '.$moyenne.'/20</h3>

<p style="margin-top:40px;">Signature du directeur</p>
';

// PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4");
$dompdf->render();
$dompdf->stream("bulletin.pdf");