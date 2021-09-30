<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
$Taux=$_POST['taux'];

$Insert2=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET taux=:taux WHERE hash=:client AND id=:id");
$Insert2->bindParam(':taux', $Taux, PDO::PARAM_STR);
$Insert2->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$Insert2->bindParam(':id', $Id, PDO::PARAM_STR);
$Insert2->execute();

header("location:".$Home."/Devis/Modifier/?id=".$Id);
?>