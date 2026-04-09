<?php
require_once("../connexion/connexion.php");

$search = $_GET['search'] ?? '';
$classe = $_GET['classe'] ?? '';

$sql = "SELECT id_eleve, CONCAT(prenom,' ',nom) as nom FROM eleve WHERE 1";
$params = [];

if($search != ''){
    $sql .= " AND (nom LIKE ? OR prenom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if($classe != ''){
    $sql .= " AND id_classe = ?";
    $params[] = $classe;
}

$sql .= " ORDER BY nom";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));