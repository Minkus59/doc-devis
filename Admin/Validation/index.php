<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

$Client=trim($_GET['id']);
$Valided=trim($_GET['Valid']);

if ((isset($Client))&&(!empty($Client))&&(isset($Valided))&&(!empty($Valided))) {

    $VerifClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
    $VerifClient->bindParam(':email', $Client, PDO::PARAM_STR);
    $VerifClient->execute();
    $NbRowsClient=$VerifClient->rowCount();

    $VerifValid=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE activate=:valid AND email=:email");
    $VerifValid->bindParam(':valid', $Valided, PDO::PARAM_STR);
    $VerifValid->bindParam(':email', $Client, PDO::PARAM_STR);
    $VerifValid->execute();
    $NbRowsValid=$VerifValid->rowCount();
        
    if ($Valided!=1) {
        $Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";   
    }

    elseif ($NbRowsClient!=1) {
        $Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";
    }

    elseif ($NbRowsValid==1) {
        $Erreur="Votre compte est déjà  actif vous pouvez dès Ã  présent vous connecter !<br />";
    }

    else {   
        $InsertValided=$cnx->prepare("UPDATE ".$Prefix."neuro_compte_Admin SET activate=1 WHERE email=:email");
        $InsertValided->bindParam(':email', $Client, PDO::PARAM_STR);
        $InsertValided->execute();

        if ((!$VerifValid)||(!$VerifClient)||(!$InsertValided)) {
            $SupprValided=$cnx->prepare("UPDATE ".$Prefix."neuro_compte_Admin SET activate=0 WHERE email=:email");
            $SupprValided->bindParam(':email', $Client, PDO::PARAM_STR);
            $SupprValided->execute();

            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultèrieurement !";
        }

        else {
            $Erreur= "Merci d'avoir validé votre compte.<br />";
            $Erreur.= "Vous pouvez dès à présent vous connecter !<br />";
        }   
    }
}
else {
    $Erreur="Erreur !";
}
?>

<!-- ************************************
*** Script realise par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>

<title>NeuroSoft Team - AccÃ¨s PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article class="ArticleAccueilAdmin">

<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; }   ?>
</article>
</section>
</div>

</body>

</html>