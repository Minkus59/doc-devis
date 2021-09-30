<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$Erreur=$_GET['erreur'];
$Model=$_POST['model'];


if (isset($_POST['Modifier'])) {

  $Insert9=$cnx->prepare("UPDATE ".$Prefix."devis_param SET model=:model WHERE hash=:client");
    $Insert9->bindParam(':model', $Model, PDO::PARAM_STR);
    $Insert9->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert9->execute();

    $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Design/?erreur=".urlencode($Erreur.""));
}

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Paramétre - Design</title>
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

<h1>Choix du model</h1>

<H3>Modèle de devis/facture</H3>

<form action="" method="POST">
Sélection<font color='#FF0000'>*</font> :
<select name="model" >
<option value="model11" <?php if ($Param->model=="model11") { echo "selected"; } ?> >Modèle 1 - FR - Gris</option>
<option value="model12" <?php if ($Param->model=="model12") { echo "selected"; } ?> >Modèle 1 - FR - Bleu</option>
<option value="model13" <?php if ($Param->model=="model13") { echo "selected"; } ?> >Modèle 1 - FR - Rouge</option>
<option value="" >---------------------</option>
<option value="model21" <?php if ($Param->model=="model21") { echo "selected"; } ?> >Modèle (Import/Export) - US - Gris</option>
<option value="model22" <?php if ($Param->model=="model22") { echo "selected"; } ?> >Modèle (Import/Export) - US - Bleu</option>
<option value="model23" <?php if ($Param->model=="model23") { echo "selected"; } ?> >Modèle (Import/Export) - US - Rouge</option>
<option value="" >---------------------</option>
<option value="model31" <?php if ($Param->model=="model31") { echo "selected"; } ?> >Modèle (Import/Export) - FR - Gris</option>
<option value="model32" <?php if ($Param->model=="model32") { echo "selected"; } ?> >Modèle (Import/Export) - FR - Bleu</option>
<option value="model33" <?php if ($Param->model=="model33") { echo "selected"; } ?> >Modèle (Import/Export) - FR - Rouge</option>
</select>
</p>
<input type="submit" name="Modifier" value="Modifier"/>
</form>
</p>
<font color='#FF0000'>*</font> : Informations requise
</p>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>