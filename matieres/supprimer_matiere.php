<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
try{ $pdo->prepare("DELETE FROM matiere WHERE id_matiere=?")->execute([$id]); $_SESSION['msg']="Matière supprimée."; }
catch(PDOException $e){ $_SESSION['err']="Impossible: notes ou absences liées."; }
header("Location: liste_matieres.php"); exit();
?>