<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Paramétre</title>
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

<H1>Modification de vos paramétres</H1>

Bienvenue dans la configuration du logiciel Doc-Devis</p>

Afin de garantir une utilisation optimale veuillez suivre attentivement cette étape. </p>

Nous allons vous guidez pas à pas tout au long du processus  </p>

<H1>FAQ</H1>

<H3>Le Dashboard</H3>
C'est votre tableau de bord, vous avez accès selon votre abonnement au suivi de votre activité (Chiffre d'affaire, TVA, etc...).  </p>


Pour toutes assistances, veuillez nous contacter au 06 52 66 06 45.  </p>

 Assistance gratuite !
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>