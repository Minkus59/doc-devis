<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$TVA=$_POST['TVA'];
$Echeance=$_POST['echeance'];
$Penalite=$_POST['penalite'];
$Escompte=$_POST['escompte'];
$Delai=$_POST['delai'];
$Devise=$_POST['devise'];
$Erreur=$_GET['erreur'];

if (isset($_POST['Modifier'])) {

    $Insert=$cnx->prepare("UPDATE ".$Prefix."devis_param SET TVAintra=:TVAintra WHERE hash=:client");
    $Insert->bindParam(':TVAintra', $TVA, PDO::PARAM_STR);
    $Insert->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert->execute();

    $Insert1=$cnx->prepare("UPDATE ".$Prefix."devis_param SET echeance=:echeance WHERE hash=:client");
    $Insert1->bindParam(':echeance', $Echeance, PDO::PARAM_STR);
    $Insert1->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert1->execute();

    $Insert4=$cnx->prepare("UPDATE ".$Prefix."devis_param SET penalite=:penalite WHERE hash=:client");
    $Insert4->bindParam(':penalite', $Penalite, PDO::PARAM_STR);
    $Insert4->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert4->execute();

    $Insert5=$cnx->prepare("UPDATE ".$Prefix."devis_param SET escompte=:escompte WHERE hash=:client");
    $Insert5->bindParam(':escompte', $Escompte, PDO::PARAM_STR);
    $Insert5->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert5->execute();

    $Insert8=$cnx->prepare("UPDATE ".$Prefix."devis_param SET delai=:delai WHERE hash=:client");
    $Insert8->bindParam(':delai', $Delai, PDO::PARAM_STR);
    $Insert8->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert8->execute();

    $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Reglement/?erreur=".urlencode($Erreur));
}

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Paramétre - Réglement</title>
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

<h1>Mention obligatoire</h1>

<form action="" method="POST">

<H3>N° de TVA intra-communautaire</H3>
(ne rien n'inscrire si auto-entrepreneur)<br />
N° :
<input type="text" name="TVA" value="<?php echo $Param->TVAintra; ?>"/>

<p><HR /></p>

<H3>Échéance des devis</H3>
La durée de validité du présent devis<br />
Nombre de jour<font color='#FF0000'>*</font> :
<input type="text" name="echeance" value="<?php echo $Param->echeance; ?>"/>
</p>

<p><HR /></p>

<H3>Délai de paiement</H3>
Délai pour le règlement de la facture<br />
Sélection<font color='#FF0000'>*</font> :
<select name="delai" >
<option value="0" <?php if ($Param->delai=="0") { echo "selected"; } ?> >30 jours à compter de la date d'émission de la facture</option>
<option value="1" <?php if ($Param->delai=="1") { echo "selected"; } ?> >45 jours fin de mois</option>
<option value="2" <?php if ($Param->delai=="2") { echo "selected"; } ?> >60 jours à compter de la date d'émission de la facture</option>
<option value="3" <?php if ($Param->delai=="3") { echo "selected"; } ?> >Paiement comptant à réception de la facture</option>
</select>
</p>

<p><HR /></p>

<H3>Pénalités de retard</H3>
Taux des pénalités de retard, éligibles en cas de non-paiement à la date de règlement<br />
Montant en %<font color='#FF0000'>*</font> :
</p>
<input type="text" name="penalite" value="<?php echo $Param->penalite; ?>"/>
</p>

<p><HR /></p>

<H3>Condition d'escompte</H3>
Conditions d'escompte en cas de paiement anticipé (0 si pas d'escompte, la mention "Pas d'escompte pour règlement anticipé" sera notifié sur le facture) <br />
Montant en %<font color='#FF0000'>*</font> :
</p>
<input type="text" name="escompte" value="<?php echo $Param->escompte; ?>"/>
</p>

<input type="submit" name="Modifier" value="Modifier"/>
</form>
</p>
<font color='#FF0000'>*</font> : Informations requise

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>