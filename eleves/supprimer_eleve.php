<?php
session_start();
require_once("../connexion/connexion.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../authentification/connexion.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo->prepare("DELETE FROM eleve WHERE id_eleve = ?")->execute([$id]);
    $_SESSION['msg'] = "Élève supprimé avec succès.";
} catch(PDOException $e){
    $_SESSION['err'] = "Impossible de supprimer : des données sont liées à cet élève.";
}

header("Location: liste_eleves.php");
exit();
?>