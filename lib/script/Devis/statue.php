<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
$Statue=trim($_POST['statue']);

$Update=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET statue=:statue WHERE hash=:client AND id=:id");
$Update->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$Update->bindParam(':statue', $Statue, PDO::PARAM_STR); 
$Update->bindParam(':id', $_POST['id'], PDO::PARAM_STR); 
$Update->execute();

echo "<div id='Valid'><img src='".$Home."/lib/img/valid.png'></div>";

?>