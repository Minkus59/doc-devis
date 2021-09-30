<?php
session_start();

$SessionClient=$_SESSION['idclient'];
//$Actif=$_SESSION['Actif'];

$VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$VerifSessionClient->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$VerifSessionClient->execute();

$NumRowSessionClient=$VerifSessionClient->rowCount();

//Actif 1 > période d'essai; Actif 2 > Abo en cour; Actif 3 > Abo VIP;
/*
if ((isset($SessionClient))&&($NumRowSessionClient==1)&&($Actif!=0)) {
	$Cnx_Ok=true;
}
*/

if ((isset($SessionClient))&&($NumRowSessionClient==1)) {
	$Cnx_Ok=true;
}
else {
	$Cnx_Ok=false;
    $Erreur="Vous devez être connecté pour accéder à cette page<BR />";
    //$Erreur.="Peut être que votre abonnement ou votre période d'essais est términé<BR />";
    //$Erreur.="Rendez-vous à la page ' Paramétre > Mon compte > Prolonger mon abonnement ' en bas de la page</p>";

	header('location:'.$Home.'/?erreur='.urlencode($Erreur));
}
?>