<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$Erreur.=FiltreTextGET('erreur');
$Email=FiltreEmail('email');
$MessageDevis=FiltreText('messageDevis');
$MessageFacture=FiltreText('messageFacture');

if ($Erreur===false) {
  $Erreur="Erreur !";
}
  
if (isset($_POST['Valider'])) {

  if ($Email===false) {
    $Erreur="Erreur, l'adresse e-mail semble invalide !";
  }
  
  elseif ($MessageDevis===false) {
    $Erreur="Erreur !";
  }
  
  elseif ($MessageFacture===false) {
    $Erreur="Erreur !";
  }
  
  else {
  $Update=$cnx->prepare("UPDATE ".$Prefix."devis_param SET email=:email, messageDevis=:messageDevis, messageFacture=:messageFacture WHERE hash=:client");
  $Update->bindParam(':client', $SessionClient, PDO::PARAM_STR);
  $Update->bindParam(':email', $Email, PDO::PARAM_STR); 
  $Update->bindParam(':messageDevis', $MessageDevis, PDO::PARAM_STR); 
  $Update->bindParam(':messageFacture', $MessageFacture, PDO::PARAM_STR); 
  $Update->execute(); 
     
  $Erreur="Enregistrement r�ussie !";
  header("location:".$Home."/Parametre/Email/?erreur=".urlencode($Erreur.""));
  }
}
?>
<!-- *******************************
*** Script r�alis� par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Param�tre - Email</title>
<META name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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

<h1>Param�tre E-mail</h1>

<form name="formEmail" action="" method="POST">
<H3>Votre adresse e-mail</H3>
Quand un devis ou une facture est envoy� au client, il aura la possibilit� de r�pondre � cettre adresse e-mail de contact <br />
<input type="email" name="email" value="<?php echo $Param->email; ?>"/>

<p><HR /></p>

<H3>Message � ins�rer dans l'e-mail pour l'envoi des devis</H3>
Entrez ici le message qui sera envoy� avec les devis en pi�ce jointe<br />
<textarea class="Mail" name="messageDevis" class="mail" ><?php echo $Param->messageDevis; ?></textarea>
</p>

<H3>Message � ins�rer dans l'e-mail pour l'envoi des factures</H3>
Entrez ici le message qui sera envoy� avec les factures en pi�ce jointe<br />
<textarea class="Mail" name="messageFacture" class="mail" ><?php echo $Param->messageFacture; ?></textarea>
</p>

<input type="submit" name="Valider" value="Valider">
</form>
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>