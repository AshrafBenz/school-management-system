<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
try{ $pdo->prepare("DELETE FROM parent WHERE id_parent=?")->execute([$id]); $_SESSION['msg']="Parent supprimé."; }
catch(PDOException $e){ $_SESSION['err']="Impossible de supprimer."; }
header("Location: liste_parents.php"); exit();
?>