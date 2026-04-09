<?php
session_start();
require_once("../connexion/connexion.php");
if(!isset($_SESSION['user_id'])){ header("Location: ../authentification/connexion.php"); exit(); }
$id=(int)($_GET['id']??0);
$pdo->prepare("DELETE FROM note WHERE id_note=?")->execute([$id]);
$_SESSION['msg']="Note supprimée.";
header("Location: liste_notes.php"); exit();
?>