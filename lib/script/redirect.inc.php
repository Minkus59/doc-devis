<?php
session_start();

$SessionClient=$_SESSION['idclient'];
//$Actif=$_SESSION['Actif'];

$VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$VerifSessionClient->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$VerifSessionClient->execute();

$NumRowSessionClient=$VerifSessionClient->rowCount();

//Actif 1 > p�riode d'essai; Actif 2 > Abo en cour; Actif 3 > Abo VIP;
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
    $Erreur="Vous devez �tre connect� pour acc�der � cette page<BR />";
    //$Erreur.="Peut �tre que votre abonnement ou votre p�riode d'essais est t�rmin�<BR />";
    //$Erreur.="Rendez-vous � la page ' Param�tre > Mon compte > Prolonger mon abonnement ' en bas de la page</p>";

	header('location:'.$Home.'/?erreur='.urlencode($Erreur));
}
?>