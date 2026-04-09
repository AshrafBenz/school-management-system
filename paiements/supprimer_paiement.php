<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("DELETE FROM paiement WHERE id_paiement=?")->execute([$id]);
$_SESSION['msg']="Paiement supprimé.";
header("Location: liste_paiements.php"); exit();
?>