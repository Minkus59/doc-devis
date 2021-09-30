<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");                    
?>
<!DOCTYPE HTML>
<html>

<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); 
?>

<body>
<center>
<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/menuAccueil.inc.php");
?>

<div id="Content">
<div id="Center">
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<article> 
<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/3-mois.jpg">

<H2>45 ¤€</H2>

Paiement en 1 mensualité de 45€<BR />
 (15 €¤ x 3mois = 45 ¤€)
 
<p><input type="button" name="Choix1" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_3mois.php'" />   </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/6-mois.jpg">

<H2>60 ¤€</H2>
Paiement en 1 mensualité de 60€<BR />
 (10 €¤ x 6mois = 60 ¤€)
<p><input type="button" name="Choix2" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_6mois.php'" />    </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/1-an.jpg">

<H2>96 ¤€</H2>
Paiement en 1 mensualité de 96€<BR />
 (8 €¤ x 12mois = 96 ¤€)
 
<p><input type="button" name="Choix3" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_12mois.php'" />    </p>
</div>

</article>

</div>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>