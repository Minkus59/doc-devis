<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$Client=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$Client->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$Client->execute();
$InfoClient=$Client->fetch(PDO::FETCH_OBJ);

$Erreur.=$_GET['erreur'];

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Compte</title>
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

<fieldset>
<legend>Informations de connexion</legend></p>
Adresse e-mail : <?php echo $InfoClient->email; ?><br />
Mot de passe : Vous seul le connaissez !</p>
<input type="button" name="Modifier" value="Modifier" onclick="self.location.href='<?php echo $Home; ?>/Parametre/Compte/Modification?genre=connexion'" />
</fieldset>
</p>
<fieldset>
<legend>Informations personnelles</legend></p>
Nom : <?php echo stripslashes($InfoClient->nom); ?><br />
Prénom : <?php echo stripslashes($InfoClient->prenom); ?><br />
Téléphone : <?php echo $InfoClient->tel; ?><br />
Adresse : <?php echo stripslashes($InfoClient->adresse); ?><br />
Code postal : <?php echo $InfoClient->cp; ?><br />
Ville : <?php echo stripslashes($InfoClient->ville); ?></p>
<input type="button" name="Modifier" value="Modifier" onclick="self.location.href='<?php echo $Home; ?>/Parametre/Compte/Modification?genre=personnelles'" />
</fieldset>
<!--
</p>
<fieldset>
<legend>Mon abonnement</legend></p>
Type d'abonnement : <?php //echo $InfoClient->abo; ?><br />
<?php //if (($InfoClient->actif=="2")||($InfoClient->actif=="3")) { ?>
Depuis le : <?php //echo date("d/m/y", $InfoClient->debut); ?><br />
Jusqu'au : <?php //echo date("d/m/y", $InfoClient->fin); ?>
<?php// } 
// if ($InfoClient->actif=="1") { ?>
Jusqu'au : <?php //echo date("d/m/y", $InfoClient->essai + 1209600); ?>
<?php //} ?>
</p>
<input type="button" name="Prolonger" value="Prolonger" onclick="self.location.href='<?php //echo $Home; ?>/Abonnement/'" />

</fieldset></p>
-->

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>