<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
$Devise=$_POST['devise'];

$Insert2=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET devise=:devise WHERE hash=:client AND id=:id");
$Insert2->bindParam(':devise', $Devise, PDO::PARAM_STR);
$Insert2->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$Insert2->bindParam(':id', $Id, PDO::PARAM_STR);
$Insert2->execute();

echo "<img src='".$Home."/lib/img/valid.png'>";

?>