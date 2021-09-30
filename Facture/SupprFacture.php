<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

    $SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE id=:id AND hash=:hash");
    $SelectArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
	$SelectArticle->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
    $SelectArticle->execute();
	$Article=$SelectArticle->fetch(PDO::FETCH_OBJ);

	$Update=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET source='', facture='0' WHERE source=:code AND hash=:hash");
    $Update->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
	$Update->bindParam(':code', $Article->code, PDO::PARAM_STR);
	$Update->execute();

	$delete=$cnx->prepare("DELETE FROM ".$Prefix."devis_facture WHERE id=:id AND hash=:hash");
    $delete->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
	$delete->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$delete->execute();

    $deleteSupplement=$cnx->prepare("DELETE FROM ".$Prefix."devis_facture_supplement WHERE facture=:facture AND hash=:hash");
    $deleteSupplement->bindParam(':facture', $Article->code, PDO::PARAM_STR);
    $deleteSupplement->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $deleteSupplement->execute();

	$deleteArticle=$cnx->prepare("DELETE FROM ".$Prefix."devis_facture_article WHERE devis=:devis AND hash=:hash");
	$deleteArticle->bindParam(':devis', $Article->code, PDO::PARAM_STR);
	$deleteArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
	$deleteArticle->execute();

    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:hash");
    $SelectParam->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectParam->execute();
	$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

	$NbFac = $Param->nb_facture - 1;
 
	$UpdateParam=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_facture=:nb_facture WHERE hash=:hash");
	$UpdateParam->bindParam(':nb_facture', $NbFac, PDO::PARAM_STR);
	$UpdateParam->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
	$UpdateParam->execute();

 	header('Location:'.$Home.'/Facture/');
	
}


if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
	header('Location:'.$Home.'/Facture/');
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
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

<article>
Etes-vous sur de vouloir supprimer ce devis ?

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