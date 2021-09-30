<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

$Client=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$Client->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$Client->execute();
$InfoClient=$Client->fetch(PDO::FETCH_OBJ);

$Erreur.=$_GET['erreur'];

?>
<!-- *******************************
*** Script r�alis� par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Abonnement</title>
<META name="robots" content="noindex, nofollow">
<link href="<?php echo $Home; ?>/lib/css/misenpa.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<CENTER>
<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/menuAccueil.inc.php");
?>  

<div id="Content">
<div id="Center">
<img src="<?php echo $Home; ?>/lib/img/facture-devis.jpg">
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

Le paiement n'a pas aboutie !
Veuillez r�essayer ult�rieurement !

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>