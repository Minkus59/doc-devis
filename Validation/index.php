<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");                    

$Client=trim($_GET['id']);
$Valided=trim($_GET['Valid']);
$Temps=time();

if ((isset($Client))&&(!empty($Client))&&(isset($Valided))&&(!empty($Valided))) {

	$VerifClient=$cnx->prepare("SELECT (hash) FROM ".$Prefix."devis_compte WHERE hash=:client");
	$VerifClient->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifClient->execute();
	$NbRowsClient=$VerifClient->rowCount();

	$VerifValid=$cnx->prepare("SELECT (valided) FROM ".$Prefix."devis_compte WHERE valided=:valid AND hash=:client");
	$VerifValid->bindParam(':valid', $Valided, PDO::PARAM_STR);
	$VerifValid->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifValid->execute();
	$NbRowsValid=$VerifValid->rowCount();
		
	if (strlen($Client)!=32) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($Valided!=1) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($NbRowsClient!=1) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($NbRowsValid==1) {
		$Erreur="Votre compte est déjà actif vous pouvez dès à présent vous connecter !</p>";
	}

	else {   
		$InsertValided=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET valided=1 WHERE hash=:client");
		$InsertValided->bindParam(':client', $Client, PDO::PARAM_STR);
		$InsertValided->execute();

		$InsertTemps=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET essai=:temps WHERE hash=:client");
		$InsertTemps->bindParam(':client', $Client, PDO::PARAM_STR);
		$InsertTemps->bindParam(':temps', $Temps, PDO::PARAM_STR);
		$InsertTemps->execute();

		if ((!$VerifValid)||(!$VerifClient)||(!$InsertValided)) {
			$SupprValided=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET valided=0 WHERE hash=:client");
			$SupprValided->bindParam(':client', $Client, PDO::PARAM_STR);
			$SupprValided->execute();

			$SupprTemps=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET essai=:temps WHERE hash=:client");
			$SupprTemps->bindParam(':client', $Client, PDO::PARAM_STR);
			$SupprTemps->bindParam(':temps', $Temps, PDO::PARAM_STR);
			$SupprTemps->execute();

			$Erreur="L'enregistrement des données à échouée, veuillez réessayer ultérieurement !</p>";
		}

		else {
			$Erreur= "Merci d'avoir validé votre compte.<br />";
			$Erreur.= "Vous pouvez dès à présent vous connecter !</p>";
		}	
	}
}
else {
	$Erreur="Erreur !";
}
?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Validation</title>
<meta charset="ISO-8859-15"/> 
<META name="robots" content="index, follow"/>

<META name="author" content="NeuroSoft Team"/>
<META name="publisher" content="Helinckx Michael"/>
<META name="reply-to" content="contact@neuro-soft.fr"/>

<META name="viewport" content="width=device-width, initial-scale=0.3"/>

<link href="<?php echo $Home; ?>/lib/css/misenpa.css" rel="stylesheet" type="text/css"/> 
<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" />
</head>

<body>
<CENTER>
<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/menuAccueil.inc.php");
?>  

<div id="Content">
<div id="Center">

<article> 
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>