<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("UPDATE paiement SET statut=IF(statut='Paye','Non_paye','Paye') WHERE id_paiement=?")->execute([$id]);
$_SESSION['msg']="Statut de paiement mis à jour.";
header("Location: liste_paiements.php"); exit();
?>