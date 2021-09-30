<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

	$delete=$cnx->prepare("DELETE FROM ".$Prefix."devis_facture_article WHERE id=:id AND hash=:hash");
  $delete->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
  $delete->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$delete->execute();

	header("location:".$Home."/Facture/Modifier/?id=".$_GET['page']);
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {
	header("location:".$Home."/Facture/Modifier/?id=".$_GET['page']);
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
Etes-vous sur de vouloir supprimer cette article ?

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