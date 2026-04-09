<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
try{ $pdo->prepare("DELETE FROM classe WHERE id_classe=?")->execute([$id]); $_SESSION['msg']="Classe supprimée."; }
catch(PDOException $e){ $_SESSION['err']="Impossible: élèves associés."; }
header("Location: liste_classes.php"); exit();
?>