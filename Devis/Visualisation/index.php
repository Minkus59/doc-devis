<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/FPDF/fpdf.php");

$SJ='86400';
$Typo='Helvetica';
$ColorText="255";
$ColorText2="0";

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

if (($Param->model=="model11")||($Param->model=="model12")||($Param->model=="model13")) {
    $TailleText="11";
    $Height="6";
}
if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
    $TailleText="9";
    $Height="5";
}

$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE id=:id");
$SelectDevis->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
$SelectDevis->execute();
$Devis=$SelectDevis->fetch(PDO::FETCH_OBJ);

if ($Devis->devise=="EUR") {
     $Devise="EUR";
}
if ($Devis->devise=="USD") {
     $Devise="$";
}

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClient->bindParam(':code', $Devis->client, PDO::PARAM_STR);
$SelectClient->execute();
$ClientDevis=$SelectClient->fetch(PDO::FETCH_OBJ);

$SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE hash=:hash AND devis=:devis");
$SelectArticle->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
$SelectArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectArticle->execute();

$SelectSupplement=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_supplement WHERE hash=:hash AND devis=:devis");
$SelectSupplement->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
$SelectSupplement->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectSupplement->execute();
$Supplement=$SelectSupplement->fetch(PDO::FETCH_OBJ);

if (($Param->model=="model11")||($Param->model=="model21")||($Param->model=="model31")) {
$ColorFont1="100";
$ColorFont2="100";
$ColorFont3="100";
}

if (($Param->model=="model12")||($Param->model=="model22")||($Param->model=="model32")) {
$ColorFont1="23";
$ColorFont2="57";
$ColorFont3="85";
}

if (($Param->model=="model13")||($Param->model=="model23")||($Param->model=="model33")) {
$ColorFont1="108";
$ColorFont2="22";
$ColorFont3="22";
}

//------------------------------------------------------

require($_SERVER['DOCUMENT_ROOT']."/Devis/Visualisation/".$Param->model.".php");

//------------------------------------------------------

// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(auto, 20);

$pdf->setlogo($Param->logo);
$pdf->setDate(date("d/m/Y", $Devis->created));
$pdf->setClient($Devis->client);
$pdf->setDevisNumber($Devis->code);
if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
    $pdf->setTaux($Devis->taux);
}

if (($Param->model=="model11")||($Param->model=="model12")||($Param->model=="model13")) {
    $pdf->setClientAdresse($ClientDevis->civilite.' '.$ClientDevis->nom.' '.$ClientDevis->prenom." \n".$ClientDevis->adresse." \n".$ClientDevis->cp.', '.$ClientDevis->ville." \nTel : ".$ClientDevis->tel);
}
if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
    $pdf->setClientAdresse($ClientDevis->civilite.' '.$ClientDevis->nom.' '.$ClientDevis->prenom." \n".$ClientDevis->adresse." \n".$ClientDevis->cp.', '.$ClientDevis->ville." \n".$ClientDevis->tva);
}

$pdf->setEcheance(date("d/m/Y", $Devis->created+($SJ*$Param->echeance)));
$pdf->setRemarque(stripslashes($Devis->remarque));
$pdf->setNumTVA($Param->TVAintra);
$pdf->setPied(stripslashes($Param->pied));
$pdf->setTailleText($TailleText);
$pdf->setWidth($Height);
$pdf->setTypo($Typo);
$pdf->setColorText($ColorText);
$pdf->setColorText2($ColorText2);
$pdf->setColorFont1($ColorFont1);
$pdf->setColorFont2($ColorFont2);
$pdf->setColorFont3($ColorFont3);
$pdf->setAccompte($Devis->acompte_montant);
$pdf->setPenalite($Param->penalite);

if ($Param->escompte=="0") {
    if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")) {
        $pdf->setEscompte("No discount for early settlement");
    }
    if (($Param->model=="model11")||($Param->model=="model12")||($Param->model=="model13")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
        $pdf->setEscompte("Pas d'escompte pour réglement anticipé");
    }
}
else {
  $pdf->setEscompte($Param->escompte." %");
}

$pdf->setDebut(stripslashes($Devis->debut));
$pdf->setDuree(stripslashes($Devis->duree));
$pdf->setType($Param->type);
$pdf->setAE($Param->AE);
$pdf->setAcompteActif($Devis->acompte);

$pdf->setAdresse($Param->civilite.' '.stripslashes($Param->nom).' '.stripslashes($Param->prenom)." \n".stripslashes($Param->adresse)." \n".$Param->cp.', '.stripslashes($Param->ville)."\nSiret n°: ".$Param->siret." \n".stripslashes($Param->registre)."\n".stripslashes($Param->autre1)."\n".stripslashes($Param->autre2));

$pdf->AddPage();
$pdf->SetFont( $Typo, "", $TailleText);

if (($Param->model=="model11")||($Param->model=="model12")||($Param->model=="model13")) {
    $pdf->SetWidths(array(10,80,20,30,30,20));
}
if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
    $pdf->SetWidths(array(10,30,50,15,15,15,15,20,20));
}

for ($i=1;$Article=$SelectArticle->fetch(PDO::FETCH_OBJ);$i++) {
    if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
        $PU2=$Article->PU;
        $Marge2=$Article->marge;

        if (($Marge2<1) && ($Marge2>=0)) { 
            $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
        }
        elseif (($Marge2<=100) && ($Marge2>=1)) {
            $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
        }
        else {
            $PUMarge2=number_format($PU2, 2,".", "");
        }

        $Total=round($Article->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
        $TotalHT+=$Total;
        $ToTVA=round($Total/100*$Article->TVA, 3, PHP_ROUND_HALF_DOWN);
        $TotalTVA+=$ToTVA;
        $TotalTTC+=$Total+$ToTVA; 
        $TotalPiece+=$Article->quantite;
        $TotalBox+=$Article->quantite_box;
        $PoidBrut+=$Article->poid_brut;
        $PoidNet+=$Article->poid_net;

        if ($Devis->acompte==1) { $AccomptE= round( $TotalTTC/100*$Devis->acompte_montant, 3, PHP_ROUND_HALF_DOWN); }
        if ($Devis->acompte==2) { $AccomptE= round( $Devis->acompte_montant, 3, PHP_ROUND_HALF_DOWN); }
    }
    else {
        $PU2=$Article->PU;
        $Marge2=$Article->marge;

        if (($Marge2<1) && ($Marge2>=0)) { 
            $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
        }
        elseif (($Marge2<=100) && ($Marge2>=1)) {
            $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
        }
        else {
            $PUMarge2=number_format($PU2, 2,".", "");
        }

        $Total=round($Article->quantite*$PUMarge2, 2, PHP_ROUND_HALF_DOWN);
        $TotalHT+=$Total;
        $ToTVA=round($Total/100*$Article->TVA, 2, PHP_ROUND_HALF_DOWN);
        $TotalTVA+=$ToTVA;
        $TotalTTC+=$Total+$ToTVA; 

        if ($Devis->acompte==1) { $AccomptE= round( $TotalTTC/100*$Devis->acompte_montant, 2, PHP_ROUND_HALF_DOWN); }
        if ($Devis->acompte==2) { $AccomptE= round( $Devis->acompte_montant, 2, PHP_ROUND_HALF_DOWN); }
    }

    if ($Article->PU=="0") {
        $Article->PU="Offert";
        $Total="Offert";
    }
    else {
        $Article->PU=number_format($PUMarge2, 2,".", " ")." ".$Devise;
        $Total=number_format($Total, 2,".", " ")." ".$Devise;
    }

    if (($Param->model=="model11")||($Param->model=="model12")||($Param->model=="model13")) {
        $pdf->Row(array($i, stripslashes($Article->description),$Article->quantite,$Article->PU,$Total,number_format($Article->TVA, 2,".", " ")." %"));
    }
    if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
        $pdf->Row(array($i, $Article->reference,stripslashes($Article->description),$Article->quantite_box,$Article->quantite,$Article->poid_brut,$Article->poid_net,$Article->PU,$Total));
    }
}

if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
    $pdf->setDdp($Supplement->ddp);
    $pdf->setTb($TotalBox);
    $pdf->setTp($TotalPiece);
    $pdf->setGw($PoidBrut);
    $pdf->setNw($PoidNet);
    $pdf->setCn($Supplement->cn);
    $pdf->setOp($Supplement->op);
}

$pdf->setAccompteE(number_format($AccomptE, 2,".", " ")." ".$Devise);
$pdf->setTotalHT(number_format($TotalHT, 2,".", " ")." ".$Devise);
$pdf->setTotalTVA(number_format($TotalTVA, 2,".", " ")." ".$Devise);
$pdf->setTotalTTC(number_format($TotalTTC, 2,".", " ")." ".$Devise);

$pdf->Bas();

if ((isset($_GET['type']))&&($_GET['type']=="mail")) {
  if (isset($_POST['oui'])) {
    $pdf->Output($Devis->code."-".$SessionClient.".pdf", "F"); 
  
    $file_name = $Home."/Devis/Visualisation/".$Devis->code."-".$SessionClient.".pdf";
    $boundary = md5(uniqid(mt_rand()));
    // Piéce jointe
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
    $message.="<html><head><title>".$Param->nom." - DEVIS</title></head><body>".nl2br($Param->messageDevis)."</body></html>";
    $message.="\n\n";
  
    $message.="--$boundary\n";
    $message.= "Content-Type: application/pdf;name=\"$Devis->code.pdf\"\n";
    $message.= "Content-Transfer-Encoding: base64\n";
  
    $message.="\n";
    $message.=$content."\n";
    $message.="\n\n";
    
    $message.="--$boundary--\n";
    
   if (!mail($ClientDevis->email, $Param->nom." - DEVIS ", $message, $header)) {
        $Erreur="L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
    } 
    else {   
      unlink($Devis->code."-".$SessionClient.".pdf");
  
      $Erreur="Un E-mail contenant le devis en pièce jointe vient d'être envoyer à ".$ClientDevis->email.".</p>";
      header('Location:'.$Home.'/Devis/?erreur='.urlencode($Erreur));
    } 
  }
  if (isset($_POST['non'])) {
    header('Location:'.$Home.'/Devis/');  
  }
}

else {
   $pdf->Output($Devis->code.".pdf", "I");
}

if ((isset($_GET['type']))&&($_GET['type']=="mail")) { 

  if ($ClientDevis->email=="") {
    $Erreur="Aucun E-mail n'est renseigné dans la fiche client !";
    header('Location:'.$Home.'/Devis/?erreur='.urlencode($Erreur));  
  }
  if ($Param->email=="") {
    $Erreur="Aucun E-mail de retour n'est renseigné dans les paramétres !";
    header('Location:'.$Home.'/Devis/?erreur='.urlencode($Erreur));  
  }  
  
  ?>

  <!DOCTYPE html>
  <html>
  <head>
  <title>Confirmation</title>
  <META name="robots" content="noindex, nofollow">
  <link href="<?php echo $Home; ?>/lib/css/misenpa.css" rel="stylesheet" type="text/css"/>
  </head>

  <body>
  <CENTER>
  <?php 
  require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
  require($_SERVER['DOCUMENT_ROOT']."/lib/script/menu.inc.php");
  ?>  

  <div id="Content">
  <div id="Center">

  <article>
  Etes-vous sur de vouloir envoyer ce devis par E-mail à <?php echo $ClientDevis->email; ?> ?

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