<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

if (isset($_POST['Recevoir'])) {

    $Email=FiltreEmail('email');
    $Hash=md5(uniqid(rand(), true));

    if ($Email[0]===false) {
         $Erreur=$Email[1];
    }
    else {

         $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
         $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
         $VerifEmail->execute();
         $NbRowsEmail=$VerifEmail->rowCount();
         $Data=$VerifEmail->fetch(PDO::FETCH_OBJ);

         $Client=$Data->hash_client;
       
         $VerifSecu=$cnx->prepare("SELECT (email) FROM ".$Prefix."neuro_Admin_secu_mdp WHERE email=:email");
         $VerifSecu->bindParam(':email', $Email, PDO::PARAM_STR);
         $VerifSecu->execute();
         $NbRowsClient=$VerifSecu->rowCount();
    
         if ($NbRowsClient==1) {
                 $Erreur="Une procédure de changement de mot de passe à déjà été demander !<br />";
         }
        
        elseif ($NbRowsEmail!=1) {          
            $Erreur="Cette adresse n'existe pas !<br />";
            $Erreur.='<input type=button value=Retour onclick=javascript:history.back()><br />';    
        }

        else {
            $InsertHash=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Admin_secu_mdp (hash, email, created) VALUES (:hash, :email, NOW())");
            $InsertHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $InsertHash->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertHash->execute();
        
            $Entete = "MIME-Version: 1.0\n";
            $Entete .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
            $Entete .= "From: \"$Societe\"<\"$Serveur\">\n";
            $Entete .= "Reply-to: \"$Societe\"<\"$Destinataire\">\n";
            $Entete .= "\n";
            $Message ="<html><head><title>Changement de mot de passe</title>
                </head><body>
                <font color='#9e2053'><H1>Procédure de changement de mot de passe</H1></font>           
                Veuillez cliquer sur le lien suivant pour changer votre mot de passe sur www.neuro-soft.fr .</p>                        
                <a href='".$Home."/Admin/Validation/Mdp/?id=$Email&hash=$Hash'>Cliquez ici</a></p>
                ____________________________________________________</p>
                Cordialement<br />
                www.neuro-soft.fr</p>
                <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
                </body></html>";

            if (!mail($Email, "Changement de mot de passe", $Message, $Entete)) {                           
                $Erreur="L'e-mail de confirmation n'a pu etre envoyé, vérifiez que vous l'avez entré correctement !<br />";
                $Erreur.='<input type=button value=Retour onclick=javascript:history.back()><br />';                            
            }
            else {
                $Erreur="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
            }
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

<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Changement de mot de passe</H1></p>

<form id="form_email" action="" method="POST">
<input type="email" placeholder="Adresse e-mail" name="email"required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Adresse e-mail saisie lors de la création du compte"/>
</p>
<input type="submit" name="Recevoir" value="Recevoir"/>
</form>
</article>
</section>
</div>
</CENTER>
</body>

</html>