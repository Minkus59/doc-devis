<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client ORDER BY nom ASC");
$Select->execute();

$Ouverture = fopen("contact-".$SessionClient.".csv", "w+");
fputcsv($Ouverture, array('Code', 'Civilite', 'Nom', 'Prenom', 'Adresse', 'Code postal', 'Ville', 'E-mail', 'Telephone'), ';');

while($AjoutListe=$Select->fetch(PDO::FETCH_OBJ)) {
    fputcsv($Ouverture, array($AjoutListe->code, $AjoutListe->civilite, $AjoutListe->nom, $AjoutListe->prenom, $AjoutListe->adresse, $AjoutListe->cp, $AjoutListe->ville, $AjoutListe->email, $AjoutListe->tel), ';');
}
fclose($Ouverture);

header("Content-Type: application/force-download");
header('Content-Disposition: attachment; filename="contact-'.$SessionClient.'.csv"');
header('Content-Length: '.  filesize('contact-'.$SessionClient.'.csv'));
readfile('contact-'.$SessionClient.'.csv');
?>