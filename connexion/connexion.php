<?php
    $servername = 'localhost'; //variable qui contient le nom du serveur
    $bdd='gestion_ecole';        //variable qui contient Nom de la base de donnée
    $username = 'root';   //variable qui contient nom utilisateur
    $password = '';       //variable qui contient mot de passe
/*****************************************************/
 /*$servername = 'sql201.byethost8.com'; //variable qui contient le nom du serveur
    $bdd='b8_37978240_gvoitre';        //variable qui contient Nom de la base de donnée
    $username = 'b8_37978240';   //variable qui contient nom utilisateur
    $password = '123456';       //variable qui contient mot de passe*/

/*****************************************************
  1. Connexion à la base de données avec PDO
  Attention, on précise ici deux options :
  - Le mode d'erreur : le mode exception permet à PDO de nous prévenir violament quand on fait une erreur ;-)
  - Le mode d'exploitation : FETCH_ASSOC veut dire qu'on exploitera les données sous la forme de tableaux associatifs
  ****************************************************/
try
{
$pdo = new PDO("mysql:host=$servername;dbname=$bdd;charset=utf8", $username, $password);
  //On définit le mode d'erreur de PDO sur Exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
    /*On capture les exceptions si une exception est lancée et on affiche
    *les informations relatives à celle-ci*/
    catch(PDOException $e)
        {
        echo "Erreur : " . $e->getMessage();
        }	
?>