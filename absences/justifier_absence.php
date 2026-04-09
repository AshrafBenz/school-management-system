<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("UPDATE absence SET justifiee=NOT justifiee WHERE id_absence=?")->execute([$id]);
$_SESSION['msg']="Statut d'absence mis à jour.";
header("Location: liste_absences.php"); exit();
?>