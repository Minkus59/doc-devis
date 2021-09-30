<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

$Erreur=$_GET['erreur'];
$Client=$_GET['id'];
$Hash=$_GET['hash'];

if (isset($_POST['Valider'])) {
        $Mdp=FiltreMDP('mdp');
        $Mdp2=FiltreMDP('mdp2');

        if ($Mdp[0]===false) {
           $Erreur=$Mdp[1];
        }
        elseif ($Mdp2[0]===false) {
           $Erreur=$Mdp2[1];
        }
        elseif ($Mdp2!=$Mdp) {
           $Erreur="Les mots de passe ne sont pas identique !";
        }
    else {
        $RecupHash=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Admin_secu_mdp WHERE hash=:hash");
        $RecupHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
        $RecupHash->execute();

        $NbRowsClient=$RecupHash->rowCount();
    
         if ($NbRowsClient!=1) {
                 $Erreur="Erreur de procédure, veuillez recommencer !<br />";
         }
         else {
              $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
              $RecupCreated->bindParam(':email', $Client, PDO::PARAM_STR);
              $RecupCreated->execute();

              $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
              $Salt=md5($DateCrea->created);
              $MdpCrypt=crypt($Mdp2, $Salt);

              $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."neuro_compte_Admin SET mdp=:mdpcrypt WHERE email=:email");
              $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
              $InsertMdp->bindParam(':email', $Client, PDO::PARAM_STR);
              $InsertMdp->execute();

              $DeleteSecu=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Admin_secu_mdp WHERE email=:email");
              $DeleteSecu->bindParam(':email', $Client, PDO::PARAM_STR);
              $DeleteSecu->execute();

              $Erreur= "Votre mot de passe a bien été validé !<br />";
              $Erreur.= "Vous pouvez dès à présent vous connecter !<br />";
              $Erreur.= '<input type=button onClick=(parent.location="'.$Home.'/Admin/") value="Se connecter"><br/>';
        }
        
    }
}

?>

<!-- ************************************
*** Script realise par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>

<title>NeuroSoft Team - Accès PRO</title>
  
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

<?php
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }

if ((isset($Client))&&(!empty($Client))) {
    $RecupHash=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Admin_secu_mdp WHERE hash=:hash");
    $RecupHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
    $RecupHash->execute();
    $NbRowsHash=$RecupHash->rowCount();

    $VerifClient=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Admin_secu_mdp WHERE email=:email");
    $VerifClient->bindParam(':email', $Client, PDO::PARAM_STR);
    $VerifClient->execute();
    $NbRowsClient=$VerifClient->rowCount();

    if ($NbRowsClient!=1) {
        echo "Aucune procédure de changement de mot de passe n'a été demander !<br />";
    }
    elseif ($NbRowsHash!=1) {
        echo "Erreur de procédure, veuillez recommencé !<br />";
    }

    else { ?>
        <form id="form_mdp" action="" method="POST">

        <input type="password" placeholder="Créer un mot de passe" name="mdp" required="required"/> 
        <br />
        <input type="password" placeholder="Confirmer le mot de passe" name="mdp2" required="required"/>
        </p>
        <input type="submit" name="Valider" value="Valider"/>
        </form><?php 
    }
}
else {
    echo "Erreur !";
}
?>
</article>
</section>
</div>

</body>

</html>