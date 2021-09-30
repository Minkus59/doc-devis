<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php"); 
  
if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) {

   $Nom=FiltreText('nom');
   $Tel=FiltreTel('tel');
   $Cp=FiltreText('cp');
   $Sujet=FiltreText('sujet');
   $Message=FiltreText('message');
   $Email=FiltreEmail('email');

   session_start();

  if ($Nom[0]===false) {
    $Erreur=$Nom[1];
  }  
  else {
    $_SESSION['nom']=$Nom;
  } 
      
  if ($Tel[0]===false) {
    $Erreur=$Tel[1]; 
  }  
  else {
    $_SESSION['tel']=$Tel;
  } 
   
  if ($Cp[0]===false) {
    $Erreur=$Cp[1]; 
  }  
  else {
    $_SESSION['cp']=$Cp;
  } 
   
  if ($Sujet[0]===false) {
    $Erreur=$Sujet[1];
  }  
  else {
    $_SESSION['sujet']=$Sujet;
  } 
   
  if ($Message[0]===false) {
    $Erreur=$Message[1];
  }  
  else {
    $_SESSION['message']=$Message;
  }  
         
  if ($Email[0]===false) {
    $Erreur=$Email[1]; 
  }    
  else {
    $_SESSION['email']=$Email;
  }  
  
  if (!isset($Erreur)) { 

    $boundary = md5(uniqid(mt_rand()));
    
    $Entete = "From: ".$Societe."<".$Serveur.">\r\n";
    $Entete .= "Reply-to: ".$Societe."<".$Email.">\r\n";
    $Entete .= 'MIME-Version: 1.0' . "\r\n";                        
    $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";
    $Entete .='Content-Transfer-Encoding: 8bit';
    
    $Message_mail="<html><head><title>Demande de contact</title></head>
    <body>Message de : ".$Email."<BR />
    Nom : ".$Nom."<BR />
    Tel : ".$Tel."<BR />
    Code postal : ".$Cp."<BR />
    Sujet : ".$Sujet."<BR />
    <BR />
    Message : ".$Message."</p>
    ____________________________________________________</p>
    Cordialement NeuroSoft Team<br />
    www.neuro-soft.fr</p>
    <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
    </body></html>";

    if (mail($Destinataire, "Demande de contact", $Message_mail, $Entete)===false) {
       $Erreur = urlencode("L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !");
       header('location:'.$Home.'/Contact/?erreur='.$Erreur);
    }
    else {
      session_unset();
      session_destroy();
      $Erreur=urlencode("Votre message à bien été enregistré, il sera traité dans les meilleurs délais !");
      header('location:'.$Home.'/Contact/?erreur='.$Erreur);
    }
  }
  else {
    header('location:'.$Home.'/Contact/?erreur='.urlencode($Erreur));
  }
}
else {
  header("location:".$Home."/Contact/");
}
?>