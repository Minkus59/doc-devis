<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid']; 

if ((isset($_GET['NeuroSoft']))&&($_GET['NeuroSoft']=="CQDFX303")) {
   if (isset($_POST['inscription'])) {
        $Email=FiltreEmail('email');
        $Mdp=FiltreMDP('mdp');
        $Mdp2=FiltreMDP('mdp2');
        $Now=time();

        $boundary = md5(uniqid(mt_rand()));

        $Entete = "MIME-Version: 1.0\n";
        $Entete .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
        $Entete .= "From: \"$Societe\"<\"$Serveur\">\n";
        $Entete .= "Reply-to: \"$Societe\"<\"$Destinataire\">\n";
        $Entete .= "\n";
        
        $Message="Ce message est au format MIME.\n";
        
        $Message.="--$boundary\n";
        $Message.= "Content-Type: text/html; charset=utf-8\n";
        
        $Message.="\n";
        $Message.="<html><head><title>Validation d'inscription</title></head><body>
                    <font color='#9e2053'><H1>Validation d'inscription</H1></font>          
                    Veuillez cliquer sur le lien suivant pour valider votre inscription.</p>
                    <a href='".$Home."/Admin/Validation/?id=".$Email."&Valid=1'>Cliquez ici</a></p>
                    ____________________________________________________</p>
                    Cordialement NeuroSoft Team<br />
                    www.neuro-soft.fr</p>
                    <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>
                    </body></html>";
        $Message.="\n\n";
        
        $Message.="--$boundary--\n";

        if ($Email[0]===false) {
           $Erreur=$Email[1];
        }
        elseif ($Mdp[0]===false) {
           $Erreur=$Mdp[1];
        }
        elseif ($Mdp2[0]===false) {
           $Erreur=$Mdp2[1]; 
        }
        elseif ($Mdp2!=$Mdp) {
           $Erreur="Les mots de passe ne sont pas identique !"; 
        }
        else {
            $Compteur=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin");
            $Compteur->execute();
            $NbCompte=$Compteur->rowCount();

            if($NbCompte==0) {
                $Preparation1=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_compte_Admin (
                id int(32) unsigned NOT NULL AUTO_INCREMENT,
                email varchar(50) NOT NULL,
                mdp varchar(32) DEFAULT NULL,
                activate int(1) NOT NULL DEFAULT '0',
                type varchar(5) NOT NULL DEFAULT 'user',
                created datetime NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY email (email)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
                
                $Preparation2=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Admin_secu_mdp (
                id int(32) unsigned NOT NULL AUTO_INCREMENT,
                hash varchar(32) NOT NULL,
                email varchar(50) NOT NULL,
                created datetime NOT NULL,
                PRIMARY KEY (id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

                $Preparation3=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Album (
                id int(32) unsigned NOT NULL AUTO_INCREMENT,
                lien longtext,
                PRIMARY KEY (id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

                $Preparation4=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Article (
                id int(32) unsigned NOT NULL AUTO_INCREMENT,
                position int(5) NOT NULL DEFAULT '1',
                message longtext NOT NULL,
                page longtext NOT NULL,
                statue int(1) NOT NULL DEFAULT '1',
                created int(11) NOT NULL,
                PRIMARY KEY (id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"); 

                $Preparation5=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Page (
                id int(32) unsigned NOT NULL AUTO_INCREMENT,
                libele varchar(50) NOT NULL,
                lien varchar(50) NOT NULL,
                position int(2) NOT NULL DEFAULT '1',
                sous_menu int(1) NOT NULL DEFAULT '0',
                parrin varchar(50) NULL,
                statue int(1) NOT NULL DEFAULT '0',
                titre varchar(70) NULL,
                description varchar(170) NULL,
                created int(32) NOT NULL,
                PRIMARY KEY (id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
                
                $Home2=$Home."/";
                $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (libele, lien, position, statue, created) VALUES('Accueil', :lien, '0', '2', :created)");
                $Insert->BindParam(":lien", $Home2, PDO::PARAM_STR);
                $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                $Insert->execute();

                $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (position, libele, lien, statue, created) VALUES('0', 'Mentions-légales', '".$Home."/Mentions-legales/', '2', :created)");
                $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                $Insert->execute();
                
                $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (position, libele, lien, statue, created) VALUES('0', 'Contact', '".$Home."/Contact/', '2', :created)");
                $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                $Insert->execute();
            }
            
            $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
            $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupClient->execute();
            $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);
            $NbRowsEmail=$RecupClient->rowCount();
            
            if ($NbRowsEmail==1) {
                $Erreur="Cette adresse E-mail existe déjà  !<br />"; 
            }
            else {                
                if ($NbCompte==0) {
                    $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."neuro_compte_Admin (email, type, activate, created) VALUES (:email, 'Admin', '1', NOW())");
                    $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
                    $InsertUser->execute();  
                }
                elseif ($NbCompte==1) {
                    $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."neuro_compte_Admin (email, type, created) VALUES (:email, 'Admin', NOW())");
                    $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
                    $InsertUser->execute();  
                }
                else {
                    $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."neuro_compte_Admin (email, created) VALUES (:email, NOW())");
                    $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
                    $InsertUser->execute(); 
                }

                 $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
                 $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
                 $RecupCreated->execute();

                 $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
                 $Salt=md5($DateCrea->created);
                 $Mdp2=md5($Mdp2);
                 $MdpCrypt=crypt($Mdp2, $Salt);

                 $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."neuro_compte_Admin SET mdp=:mdpcrypt WHERE email=:email");
                 $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
                 $InsertMdp->bindParam(':email', $Email, PDO::PARAM_STR);
                 $InsertMdp->execute();

                 if (($InsertUser==false)||($InsertMdp==false)) {
                     $Erreur="Une erreur est survenue, veuillez réessayer<br />";
                 }
                 else {
                      if (!mail($Email, "Validation d'inscription", $Message, $Entete)) {
                            $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !<br />"; 
                      }
                        
                      else {
                           $Valid="Bonjour,<br />";
                           $Valid.="Merci de vous être inscrit<br />";
                           $Valid.="Un E-mail de confirmation vous a été envoyé à  l'adresse suivante : ".$Email."<br />";
                           $Valid.="Veuillez valider votre adresse e-mail avant de vous connecter !<br />";
                           header("location:".$Home."/Admin/?valid=".urlencode($Valid));
                      }
                 }
            }
       }
   }
}

if (isset($_POST['OK'])) {

    $Email=FiltreEmail('email');
    $Mdp=FiltreMDP('mdp');
    
    if ($Email[0]===false) {
       $Erreur=$Email[1];
    }

    elseif ($Mdp[0]===false) {
       $Erreur=$Mdp[1]; 
    }
    else {
        $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
        $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupClient->execute();
        $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);
        $NbRowsEmail=$RecupClient->rowCount();

        if ($NbRowsEmail!=1) {
            $Erreur="Cette adresse E-mail ne correspond à aucun compte !<br />";
        }
        elseif ($RecupC->activate!=1) {
            $Erreur="le compte n'est pas activé, veuillez activer votre compte avant de vous connecter!<br />";
        }

        else {
            $Salt=md5($RecupC->created);
            $Mdp=md5($Mdp);
            $MdpCrypt=crypt($Mdp, $Salt);

            $Mdp=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE mdp=:mdp AND email=:email");
            $Mdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
            $Mdp->bindParam(':email', $Email, PDO::PARAM_STR);
            $Mdp->execute();
            $nb_rows=$Mdp->rowCount();

            if ($nb_rows!=1) { 
                $Erreur="Le mot de passe ne correspond pas à  cette adresse e-mail !<br />";
            }
            else {  
                session_start();                
                $_SESSION['NeuroAdmin']=$Email;
                $Valid="Vous êtes connecté ";
                header("location:".$Home."/Admin/?valid=".urlencode($Valid));
            } 
        }
    }
}
?>

<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>
<title>NeuroSoft Team - Accà¨s PRO</title>
  
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

<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }

if ($Cnx_Admin===false) { ?>

    <p><H1>Connexion</H1></p>
    <form name="form_cnx" action="" method="POST">
    <input name="email" type="email" placeholder="E-mail" required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Adresse e-mail saisie lors de la création du compte"/>
    <br />
    <input name="mdp" type="password" placeholder="Mot de passe" required="required"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Mot de passe saisie lors de la création du compte"/>
    </p>
    <input type="submit" name="OK" value="OK"/>
    </form>
    <p><label><a href="<?php echo $Home; ?>/Admin/Securite/">Mot de passe oublié ?</a></label></p>
<?php }
else { ?>
    <a href="<?php echo $Home; ?>/Admin/lib/script/deconnexion.php">Déconnexion</a>
<?php }

if ((isset($_GET['NeuroSoft']))&&($_GET['NeuroSoft']=="CQDFX303")) {    ?>

    <p><HR /></p>
    <p><H1>Inscription</H1></p>
    <p><form name="form_inscription" action="" method="POST">
    <input type="email" placeholder="Adresse e-mail" name="email" required="required"/> 
    <br />
    <input type="password" placeholder="Créer un mot de passe" name="mdp" required="required"/> 
    <br />
    <input type="password" placeholder="Confirmer le mot de passe" name="mdp2" required="required"/>
    </p>
    <input type="submit" name="inscription" value="Inscription"/></p>
    </form>
    </p>
<?php
}
?>
</article>
</section>
</div>

</body>

</html>