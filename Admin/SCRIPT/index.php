<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];


    
?>

<!-- ************************************
*** Script rÃ©alisÃ© par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>

<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Script</H1></p>

<table>
<tr>
      <th>Date</th>
      <th>Client</th>
      <th>Ville départ</th>
      <th>Ville retour</th>
</tr>
<?php
$SessionClient="226dc2f899b8c4126f8e0b98ff070be5";
$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash ORDER BY id DESC");
$SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();

while ($Devis=$SelectDevis->fetch(PDO::FETCH_OBJ)) {
$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClient->bindParam(':code', $Devis->client, PDO::PARAM_STR);
$SelectClient->execute();
$Client=$SelectClient->fetch(PDO::FETCH_OBJ);

      $NbVisite=rand(1,4);
      for($i=1;$i<=$NbVisite;$i++) { 
            if ($i<$NbVisite) { 
                  $JM=rand(1,16);
                  $NbJour=$JM * 86400;
                  $Date=$Devis->created - $NbJour ; ?>
                  <tr>
                  <td><?php echo date("d/m/y", $Date); ?></td>
                  <td><?php echo $Client->nom; ?></td>
                  <td>13 Avenue de la Créativité, Villeneuve-d'Ascq, France</td>
                  <td><?php echo $Client->adresse.", ".$Client->ville.", France"; ?></td>
                  </tr><?php
            }
            if ($i==$NbVisite) {
                  $Date=$Devis->created; ?>
                  <tr style="background-color: red;">
                  <td><?php echo date("d/m/y", $Devis->created); ?></td>
                  <td><?php echo $Client->nom; ?></td>
                  <td>13 Avenue de la Créativité, Villeneuve-d'Ascq, France</td>
                  <td><?php echo $Client->adresse.", ".$Client->ville.", France"; ?></td>
                  </tr><?php
            }
      }
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>