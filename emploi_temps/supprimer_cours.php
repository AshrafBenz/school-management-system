<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("DELETE FROM emploi_temps WHERE id_edt=?")->execute([$id]);
$_SESSION['msg']="Cours supprimé.";
header("Location: liste_emploi.php"); exit();
?>