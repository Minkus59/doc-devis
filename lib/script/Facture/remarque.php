<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
$Remarque=trim(utf8_decode($_POST['remarque']));

$Insert2=$cnx->prepare("UPDATE ".$Prefix."devis_facture SET remarque=:remarque WHERE hash=:client AND id=:id");
$Insert2->bindParam(':remarque', $Remarque, PDO::PARAM_STR);
$Insert2->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$Insert2->bindParam(':id', $Id, PDO::PARAM_STR);
$Insert2->execute();

echo "<div id='Valid'><img src='".$Home."/lib/img/valid.png'></div>";

?>