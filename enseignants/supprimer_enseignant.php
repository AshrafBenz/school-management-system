<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
try{
    $pdo->prepare("DELETE FROM enseignant WHERE id_enseignant=?")->execute([$id]);
    $_SESSION['msg']="Enseignant supprimé.";
}catch(PDOException $e){ $_SESSION['err']="Impossible: données liées."; }
header("Location: liste_enseignants.php"); exit();
?>