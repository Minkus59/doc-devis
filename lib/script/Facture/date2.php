<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
trim($Date=$_POST['date']);
$date=explode("/", $Date);

if (empty($Date)) {
    echo "<font color='#FF0000'>Une date doit être saisie !</font>";
}
elseif (strlen($date[0])!=2) {
    echo "<font color='#FF0000'>Le jour doit conporter 2 chiffres !</font>";
}
elseif (strlen($date[1])!=2) {
    echo "<font color='#FF0000'>Le mois doit conporter 2 chiffres !</font>";
}
elseif (strlen($date[2])!=4) {
    echo "<font color='#FF0000'>L'année doit conporter 4 chiffres !</font>";
}
else {
    $Date=mktime(0, 0, 0, $date[1], $date[0], $date[2]);

    $Insert2=$cnx->prepare("UPDATE ".$Prefix."devis_facture SET created=:date WHERE hash=:client AND id=:id");
    $Insert2->bindParam(':date', $Date, PDO::PARAM_STR);
    $Insert2->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert2->bindParam(':id', $Id, PDO::PARAM_STR);
    $Insert2->execute();

header("location:".$Home."/Facture/Modifier/?id=".$Id);
}
?>