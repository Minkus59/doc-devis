<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

require($_SERVER['DOCUMENT_ROOT']."/FPDF/fpdf.php");

$SJ='86400';
$TailleText="11";
$Height="6";
$Typo='Helvetica';
$ColorText="255";
$ColorText2="0";

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

if ($Param->devise=="EUR") {
     $Devise="€";
}
if ($Param->devise=="USD") {
     $Devise="$";
}

$SelectRecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE id=:id AND hash=:hash");
$SelectRecu->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectRecu->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
$SelectRecu->execute();
$Recu=$SelectRecu->fetch(PDO::FETCH_OBJ);

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClient->bindParam(':code', $Recu->client, PDO::PARAM_STR);
$SelectClient->execute();
$ClientRecu=$SelectClient->fetch(PDO::FETCH_OBJ);

$ColorFont1="100";
$ColorFont2="100";
$ColorFont3="100";

if ($Param->model=="model11") {
$ColorFont1="100";
$ColorFont2="100";
$ColorFont3="100";
}

if ($Param->model=="model12") {
$ColorFont1="23";
$ColorFont2="57";
$ColorFont3="85";
}

if ($Param->model=="model13") {
$ColorFont1="108";
$ColorFont2="22";
$ColorFont3="22";
}


//------------------------------------------------------

require($_SERVER['DOCUMENT_ROOT']."/Recu/Visualisation/".$Param->model.".php");

//------------------------------------------------------

// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(auto, 45);

$pdf->setSociete($Param->civilite.' '.$Param->nom.' '.$Param->prenom);
$pdf->setlogo($Param->logo);
$pdf->setDate(date("d/m/Y", $Recu->date));
$pdf->setRecuNumber($Recu->code);
$pdf->setCommentaire($Recu->commentaire);
$pdf->setClientAdresse($ClientRecu->civilite.' '.$ClientRecu->nom.' '.$ClientRecu->prenom." \n".$ClientRecu->adresse." \n".$ClientRecu->cp.', '.$ClientRecu->ville." \nTel : ".$ClientRecu->tel);
$pdf->setPied(stripslashes($Param->pied));
$pdf->setTailleText($TailleText);
$pdf->setWidth($Height);
$pdf->setTypo($Typo);
$pdf->setColorText($ColorText);
$pdf->setColorText2($ColorText2);
$pdf->setColorFont1($ColorFont1);
$pdf->setColorFont2($ColorFont2);
$pdf->setColorFont3($ColorFont3);
$pdf->setType($Param->type);
$pdf->setMotif($Recu->motif);
$pdf->setMontant($Recu->montant." ".$Devise);
$pdf->setLettre($Recu->lettre);
$pdf->setMode($Recu->mode);

$pdf->setAdresse($Param->civilite.' '.stripslashes($Param->nom).' '.stripslashes($Param->prenom)." \n".stripslashes($Param->adresse)." \n".$Param->cp.', '.stripslashes($Param->ville)." \n".$Param->siret." \n".stripslashes($Param->registre));

$pdf->AddPage(L, A5);
$pdf->SetFont( $Typo, "", $TailleText);

$pdf->SetWidths(array(90,20,30,30,20));

if ((isset($_GET['type']))&&($_GET['type']=="mail")) {
  if (isset($_POST['oui'])) {
    $pdf->Output($Recu->code.".pdf", "F");
  
    $file_name = $Home."/Recu/Visualisation/".$Recu->code.".pdf";
    $boundary = md5(uniqid(mt_rand()));
    // Pièce jointe
    $content = file_get_contents($file_name);
    $content = chunk_split(base64_encode($content));
    
    $header = "From: ".$Param->nom." <".$Param->email."> <".$Serveur.">\n";
    $header .= "MIME-Version: 1.0\n";
    $header .= "Content-Type:multipart/mixed;\n boundary=\"$boundary\"\n";
    $header .= "\n";
    
    $message="Ce message est au format MIME.\n";
    
    $message.="--$boundary\n";
    $message.="Content-Type: text/html; charset=iso8859-15\n";  
    
    $message.="\n";
    $message.="<html><head><title>".$Param->nom." - Reçu</title></head><body>".nl2br($Param->messageDevis)."</body></html>";
    $message.="\n\n";
  
    $message.="--$boundary\n";
    $message.= "Content-Type: application/pdf;name=\"$Recu->code.pdf\"\n";
    $message.= "Content-Transfer-Encoding: base64\n";
  
    $message.="\n";
    $message.=$content."\n";
    $message.="\n\n";
    
    $message.="--$boundary--\n";
    
   if (!mail($ClientRecu->email, $Param->nom." - Reçu", $message, $header)) {
        $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
    } 
    else {   
      unlink($Recu->code.".pdf");
  
      $Valid="Un E-mail contenant le reçu en pièce jointe vient d'être envoyer à ".$ClientRecu->email.".</p>";
      header('Location:'.$Home.'/Recu/?erreur='.$Erreur);
    } 
  }
  if (isset($_POST['non'])) {
    header('Location:'.$Home.'/Recu/');
  }
}
else {
     $pdf->Output("Recu-".$Recu->code.".pdf", I);
}

if ((isset($_GET['type']))&&($_GET['type']=="mail")) { 

  if ($ClientRecu->email=="") {
    $Erreur="Aucun E-mail n'est renseigné dans la fiche client !";
    header('Location:'.$Home.'/Recu/?erreur='.$Erreur);
  }
  if ($Param->email=="") {
    $Erreur="Aucun E-mail de retour n'est renseigné dans les paramètres !";
    header('Location:'.$Home.'/Recu/?erreur='.$Erreur);
  }  

?>

<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Reçu</title>
<META name="robots" content="noindex, nofollow">
<link href="<?php echo $Home; ?>lib/css/misenpa.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<CENTER>
<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/menu.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/menuRecu.inc.php");
?>  

<div id="Content">
<div id="Center">

<article>
Etes-vous sur de vouloir envoyer ce reçu par E-mail à <?php echo $ClientRecu->email; ?> ?

<TABLE width="300">
  <form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR></form>
</TABLE>
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>
<?php } ?>