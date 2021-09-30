<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Erreur.=FiltreTextGET('erreur');
$Statue=$_POST['statue'];

if (isset($_POST['statueDevis'])) {
   $_SESSION['statueDevis']=$_POST['statueDevis'];
}

if (isset($_POST['codeClient'])) {
   $_SESSION['codeClient']=$_POST['codeClient'];
}

if  ($Erreur===false) {
  $Erreur.="Erreur";
}

if ((isset($Statue))&&(isset($_POST['id']))) {

  $Update=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET statue=:statue WHERE hash=:client AND id=:id");
  $Update->bindParam(':client', $SessionClient, PDO::PARAM_STR);
  $Update->bindParam(':statue', $Statue, PDO::PARAM_STR); 
  $Update->bindParam(':id', $_POST['id'], PDO::PARAM_STR); 
  $Update->execute();
  
  header("location:".$Home."/Devis/#ligne".$_POST['id']);
}

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$SelectClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectClient->execute();

if ((!isset($_SESSION['statueDevis']))||($_SESSION['statueDevis']=="NULL")) {
   if ((!isset($_SESSION['codeClient']))||($_SESSION['codeClient']=="NULL")) {
        $SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash ORDER BY id DESC");
        $SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectDevis->execute();
   }
   else {
        $SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash AND client=:client ORDER BY id DESC");
        $SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectDevis->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
        $SelectDevis->execute();
   }
}
else {
   if ((!isset($_SESSION['codeClient']))||($_SESSION['codeClient']=="NULL")) {
      $SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash AND statue=:valeur ORDER BY id DESC");
      $SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
      $SelectDevis->bindParam(':valeur', $_SESSION['statueDevis'], PDO::PARAM_STR);
      $SelectDevis->execute();
   }
   else {
        $SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash AND statue=:valeur AND client=:client ORDER BY id DESC");
        $SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectDevis->bindParam(':valeur', $_SESSION['statueDevis'], PDO::PARAM_STR);
        $SelectDevis->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
        $SelectDevis->execute();
   }
}

?>
<!-- *******************************
*** Script rï¿½alisï¿½ par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Devis</title>
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
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>
<caption>
<form name="form2" action="" method="POST">
<select name="statueDevis" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['statueDevis']=="NULL") { echo "selected"; } ?> >-- Tous --</option>
<option value="En cours" <?php if ($_SESSION['statueDevis']=="En cours") { echo "selected"; } ?>>En cours</option>
<option value="Terminer" <?php if ($_SESSION['statueDevis']=="Terminer") { echo "selected"; } ?>>Terminer</option>
<option value="Valider" <?php if ($_SESSION['statueDevis']=="Valider") { echo "selected"; } ?>>Valider</option>
<option value="Annuler" <?php if ($_SESSION['statueDevis']=="Annuler") { echo "selected"; } ?>>Annuler</option>
</select>
</form>

<form name="formClient" action="" method="POST">
<select name="codeClient" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['codeClient']=="NULL") { echo "selected"; } ?> >-- Tous --</option>
<?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Client->code; ?>" <?php if ($_SESSION['codeClient']==$Client->code) { echo "selected"; } ?> ><?php echo $Client->nom; ?></option>
<?php } ?>
</select>
</form>

  </p>
</caption> 
    <table>
    <tr>
        <th>
            N° devis
        </th>
        <th>
            N° facture
        </th>
        <th>
            Client
        </th>
        <th>
            Date
        </th>
        <th>
            Montant TTC
        </th>
        <th>
            Devise
        </th>
        <th>
            Statut
        </th>
        <th>
            Action
        </th>
    </tr>
<?php
while($Devis=$SelectDevis->fetch(PDO::FETCH_OBJ)) { 
$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClient->bindParam(':code', $Devis->client, PDO::PARAM_STR);
$SelectClient->execute();
$Client=$SelectClient->fetch(PDO::FETCH_OBJ);
?>
    <tr <?php if ($Devis->statue=="Annuler") { echo "class='rouge'"; } elseif ($Devis->statue=="Valider") { echo "class='vert'"; } elseif ($Devis->statue=="Terminer") { echo "class='vert'"; } elseif ($Devis->statue=="En cours") { echo "class='orange'"; } ?> id="ligne<?php echo $Devis->id; ?>">
        <td>
            <?php echo $Devis->code; ?>
        </td>
        <td>
            <?php echo $Devis->source; ?>
        </td>
        <td>
            <?php echo $Client->nom; ?>
        </td>
        <td>
            <?php echo date("d/m/y", $Devis->created); ?>
        </td>
    <td>
    <?php
    $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE hash=:hash AND devis=:devis");
    $SelectArticle2->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
    $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectArticle2->execute();
    
     for ($i=0;$Article2=$SelectArticle2->fetch(PDO::FETCH_OBJ);$i++) {
        if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
            $PU2=$Article2->PU;
            $Marge2=$Article2->marge;

            if (($Marge2<1) && ($Marge2>=0)) { 
                $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
            }
            elseif (($Marge2<=100) && ($Marge2>=1)) {
                $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
            }
            else {
                $PUMarge2=number_format($PU2, 2,".", "");
            }

            $Total=round($Article2->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
            $TotalHT+=$Total;
            $ToTVA=round($Total/100*$Article2->TVA, 3, PHP_ROUND_HALF_DOWN);
            $TotalTVA+=$ToTVA;
            $TotalTTC+=$Total+$ToTVA; 
            $TotalPiece+=$Article2->quantite;
            $TotalBox+=$Article2->quantite_box;
            $PoidBrut+=$Article2->poid_brut;
            $PoidNet+=$Article2->poid_net;
        }
        else {
            $PU2=$Article2->PU;
            $Marge2=$Article2->marge;

            if (($Marge2<1) && ($Marge2>=0)) { 
                $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
            }
            elseif (($Marge2<=100) && ($Marge2>=1)) {
                $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
            }
            else {
                $PUMarge2=number_format($PU2, 2,".", "");
            }

            $Total=round($Article2->quantite*$PUMarge2, 2, PHP_ROUND_HALF_DOWN);
            $TotalHT+=$Total;
            $ToTVA=round($Total/100*$Article2->TVA, 2, PHP_ROUND_HALF_DOWN);
            $TotalTVA+=$ToTVA;
            $TotalTTC+=$Total+$ToTVA; 
        }
    } 
       echo number_format($TotalTTC, 2,".", "");
       $TotalTTC="0";
      ?>        
    </td>
    <td>
        <?php echo $Devis->devise; ?>
    </td>
    <td>
      <form name="form_<?php echo $Devis->id; ?>" action="" method="POST">
      <input type="hidden" name="id" value="<?php echo $Devis->id; ?>">
      <select name="statue" required="required" onChange="this.form.submit()">
      <option value="" <?php if ($Devis->statue=="NULL") { echo "selected"; } ?> >- - - - - -</option>
      <option value="En cours" <?php if ($Devis->statue=="En cours") { echo "selected"; } ?> >En cours</option>
      <option value="Terminer" <?php if ($Devis->statue=="Terminer") { echo "selected"; } ?> >Terminer</option>
      <option value="Valider" <?php if ($Devis->statue=="Valider") { echo "selected"; } ?> >Valider</option>
      <option value="Annuler" <?php if ($Devis->statue=="Annuler") { echo "selected"; } ?> >Annuler</option>
      </select>
      </form>
    </td>
    <td>
        <a href="<?php echo $Home; ?>/Devis/Visualisation/?id=<?php echo $Devis->id; ?>" target="_blank"><acronym title="Aperçu en PDF"><img src="<?php echo $Home; ?>/lib/img/pdf.png"/></acronym></a>
        <a href="<?php echo $Home; ?>/Devis/Modifier/?id=<?php echo $Devis->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
        <a href="<?php echo $Home; ?>/Devis/SupprDevis.php?id=<?php echo $Devis->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        <!--<a href="<?php echo $Home; ?>/Devis/Visualisation/?id=<?php echo $Devis->id; ?>&type=mail"><acronym title="Envoyer au client par E-mail"><img src="<?php echo $Home; ?>/lib/img/mail.png"/></acronym></a>-->
        <!--<a href="<?php echo $Home; ?>/Devis/Visualisation/?id=<?php echo $Devis->id; ?>&type=imprimer"><acronym title="Imprimer"><img src="<?php echo $Home; ?>/lib/img/imprimer.png"/></acronym></a>   -->
        <?php if (($Devis->facture==0)&&(($Devis->statue=="Terminer")||($Devis->statue=="Valider"))) { ?>
                <a href="<?php echo $Home; ?>/Facture/Facture.php?id=<?php echo $Devis->id; ?>"><acronym title="Facturer"><img src="<?php echo $Home; ?>/lib/img/facture.png"/></acronym></a>
                <a href="<?php echo $Home; ?>/Recu/Nouveau/?IdSource=<?php echo $Devis->id; ?>&TypeSource=DEV"><acronym title="Editer un reï¿½u"><img src="<?php echo $Home; ?>/lib/img/recu.png"/></acronym></a>
        <?php } ?>
    </td>
</tr>
 
<?php
}  
?>
    </table>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>