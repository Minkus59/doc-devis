<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/PayPlug/Payplug.php");
Payplug::setConfigFromFile($_SERVER['DOCUMENT_ROOT']."/impinfbdd/parameters.json");

try {
    $ipn = new IPN();
     
    $Montant = $ipn->amount/100;

    $Entete ='From: "no-reply@neuro-soft.fr"<postmaster@neuro-soft.fr>'."\r\n";
        $Entete .= 'MIME-Version: 1.0' . "\r\n";
        $Entete .='Content-Type: text/html; charset="iso-8859-1"'."\r\n";
        $Entete .='Content-Transfer-Encoding: 8bit';

        $Message = "IPN reçu de ".$ipn->firstName." ".$ipn->lastName." avec l'adresse e-mail ".$ipn->email.", pour un montant de ".$Montant." EUR </p>
        Etat du paiement : ".$ipn->state."<br />
        Numéro de transaction : ".$ipn->idTransaction."<br />
        Numéro de commande : ".$ipn->order."<br />
        Numéro client : ".$ipn->customer."<br />
        Origine : ".$ipn->origin."<br />
        Type : ".$ipn->customData."";

    mail("contact@neuro-soft.fr","IPN Valide", $Message, $Entete);
    
    $InsertPaiement=$cnx->prepare("INSERT INTO '.$Prefix.'devis_paiement (etat, id_transaction, montant, email, nom, prenom, commande, hash_client, origin, type) VALUES (:etat, :id_transaction, :montant, :email, :nom, :prenom, :commande, :hash_client, :origin, :type)");
    $InsertPaiement->bindParam(':etat', $ipn->state, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':id_transaction', $ipn->idTransaction, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':montant', $Montant, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':email', $ipn->email, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':nom', $ipn->firstName, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':prenom', $ipn->lastName, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':commande', $ipn->order, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':hash_client', $ipn->customer, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':origin', $ipn->origin, PDO::PARAM_STR);
    $InsertPaiement->bindParam(':type', $ipn->customData, PDO::PARAM_STR);
    $InsertPaiement->execute();               
                                           
    $SelectCompte=$cnx->prepare('SELECT * FROM '.$Prefix.'devis_compte WHERE hash=:hash');
    $SelectCompte->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
    $SelectCompte->execute();
    $Compte=$SelectCompte->fetch(PDO::FETCH_OBJ);

        $Duree1="7776000";
        $Duree2="15552000";
        $Duree3="31104000";
        $Temps=time();
        $TempsPre=$Compte->debut;
        $Abo1="3 mois";
        $Abo2="6 mois";
        $Abo3="1 an";
                           

 if ($Compte->actif=="0") {
        if ($ipn->customData==1) {
            if ($Montant=="75") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
      }
        }
        if ($ipn->customData==2) {
            if ($Montant=="120") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute(); 
            }
        }
        if ($ipn->customData==3) {
            if ($Montant=="180") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
            }
        }
 }

 if ($Compte->actif=="1") {
        if ($ipn->customData==1) {
            if ($Montant=="75") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
            }
        }
        if ($ipn->customData==2) {
            if ($Montant=="120") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
             }
        }
        if ($ipn->customData==3) {
            if ($Montant=="180") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET debut=:debut, fin=:fin, actif="2", abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':debut', $Temps, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':fin', $Temps+$Duree3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
            }
        }
 }

 if ($Compte->actif=="2") {
        if ($ipn->customData==1) {
            if ($Montant=="75") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET fin=:fin, abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':fin', $TempsPre+$Duree1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();   
            }
        }
        if ($ipn->customData==2) {
            if ($Montant=="120") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET fin=:fin, abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':fin', $TempsPre+$Duree2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
            }
        }
        if ($ipn->customData==3) {
            if ($Montant=="180") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET fin=:fin, abo=:abo WHERE hash=:hash');
                $UpdateAbo->bindParam(':fin', $TempsPre+$Duree3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();  
            }
        }
 }
 if ($Compte->actif=="3") {
        if ($ipn->customData==1) {
            if ($Montant=="75") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET abo=:abo WHERE hash=:hash');
                //$UpdateAbo->bindParam(':fin', $TempsPre+$Duree1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo1, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();   
            }
        }
        if ($ipn->customData==2) {
            if ($Montant=="120") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET abo=:abo WHERE hash=:hash');
                //$UpdateAbo->bindParam(':fin', $TempsPre+$Duree2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo2, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();
            }
        }
        if ($ipn->customData==3) {
            if ($Montant=="180") {
                $UpdateAbo=$cnx->prepare('UPDATE '.$Prefix.'devis_compte SET abo=:abo WHERE hash=:hash');
                //$UpdateAbo->bindParam(':fin', $TempsPre+$Duree3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':abo', $Abo3, PDO::PARAM_STR);
                $UpdateAbo->bindParam(':hash', $ipn->customer, PDO::PARAM_STR);
                $UpdateAbo->execute();  
            }
        }
 }  
}
catch (InvalidSignatureException $e) {
    mail("contact@neuro-soft.fr","IPN Frauduleux","La signature n'est pas valide");       
}         
?>