<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("DELETE FROM absence WHERE id_absence=?")->execute([$id]);
$_SESSION['msg']="Absence supprimée.";
header("Location: liste_absences.php"); exit();
?>