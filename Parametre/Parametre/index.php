<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$Nb_devis=$_POST['nb_devis'];
$Nb_facture=$_POST['nb_facture'];
$Erreur=$_GET['erreur'];

if (isset($_POST['Modifier'])) {
    $Insert6=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_devis=:nb_devis WHERE hash=:client");
    $Insert6->bindParam(':nb_devis', $Nb_devis, PDO::PARAM_STR);
    $Insert6->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert6->execute();

  $Insert7=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_facture=:nb_facture WHERE hash=:client");
    $Insert7->bindParam(':nb_facture', $Nb_facture, PDO::PARAM_STR);
    $Insert7->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert7->execute();

    $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Parametre/?erreur=".urlencode($Erreur)."");
}

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Paramétre - Logiciel</title>
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

<h1>Paramétre du précédent logiciel</h1>

<form action="" method="POST">
<H3>N° du dernier devis</H3>
Enregistrer ici le numéro du dernier devis de votre ancien système, pour que le programme continue la suite.(0 par défaut)<br />
N° :
<input type="text" name="nb_devis" value="<?php echo $Param->nb_devis; ?>"/>
</p>

<p><HR /></p>

<H3>N° de la dernière facture</H3>
Enregistrer ici le numéro de la dernière facture de votre ancien système, pour que le programme continue la suite.(0 par défaut)<br />
N° :
<input type="text" name="nb_facture" value="<?php echo $Param->nb_facture; ?>"/>
</p>
<input type="submit" name="Modifier" value="Modifier"/>
</p>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>