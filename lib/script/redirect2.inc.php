<?php
/* Démarre la session */
session_start();

$SessionClient=$_SESSION['idclient'];
//$Actif=$_SESSION['Actif'];

$VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$VerifSessionClient->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$VerifSessionClient->execute();

$NumRowSessionClient=$VerifSessionClient->rowCount();

if ((isset($SessionClient))&&($NumRowSessionClient==1)) {
	$Cnx_Ok=true;
}
else {
	$Cnx_Ok=false;
    $Erreur="Vous devez être connecté pour accéder à cette page</p>";

	header('location:'.$Home.'/?erreur='.urlencode($Erreur));
}

?>