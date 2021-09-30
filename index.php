<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");       

/*
$Salt999=md5("2015-01-06 11:33:49");
$Salt2999=md5("Cqdfx301");
$MdpCrypt999=crypt($Salt2999, $Salt999);
echo $MdpCrypt999;
*/

/* Démarre la session */
session_start();

$SessionClient=$_SESSION['idclient'];
$Actif=$_SESSION['Actif'];

$VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$VerifSessionClient->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$VerifSessionClient->execute();

$NumRowSessionClient=$VerifSessionClient->rowCount();

if ((isset($SessionClient))&&($NumRowSessionClient==1)) {
    $Cnx_Ok=true;
}
else {
    $Cnx_Ok=false;
}

$Erreur.=$_GET['erreur'];
$Email=trim($_POST['email']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Tel=trim($_POST['tel']);
$Code = md5(uniqid(rand(), true));
$Client=trim($_POST['client']);
$Ip=$_SERVER['REMOTE_ADDR'];
$Email=trim($_POST['email']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);
$Temps=time();
$Essai="Période d'essai";

if (isset($_POST['cnx'])) {
    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."devis_compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();

    $VerifValid=$cnx->prepare("SELECT (valided) FROM ".$Prefix."devis_compte WHERE valided=1 AND email=:email");
    $VerifValid->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifValid->execute();
    $NbRowsValid=$VerifValid->rowCount();

    $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE email=:email");
    $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
    $RecupClient->execute();
    $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);

    //$Fin=$RecupC->essai+1209600;
    //$Fin2=$RecupC->fin;

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !</p>";
    }

    elseif ($NbRowsEmail!=1) {          
        $Erreur="Cette adresse E-mail ne correspond à aucun compte !</p>";
    }

    elseif (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractéres !</p>";
    }

    elseif ($NbRowsValid!=1) {
        $Erreur="Votre compte n'a pas été activé !<br />";
        $Erreur.="Lors de votre inscription un e-mail vous a été envoyé<br />";
        $Erreur.="Veuillez valider votre adresse e-mail en cliquant sur le lien.<br />";
        $Erreur.="vous pouvais toujours recevoir le mail a nouveau en cliquant sur ' recevoir '<br />";
        $Erreur.="<form action='' method='post'/><input type='hidden' name='client' value='".$RecupC->hash."'/><input type='hidden' name='email' value='".$RecupC->email."'/><input type='submit' name='Recevoir' value='Recevoir'/></form></p>";
    }

    else {
        $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."devis_compte WHERE email=:email");
        $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $Salt2=md5($Mdp);
        $MdpCrypt=crypt($Salt2, $Salt);

        $VerifMdp=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE mdp=:mdp AND email=:email");
        $VerifMdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
        $VerifMdp->bindParam(':email', $Email, PDO::PARAM_STR);
        $VerifMdp->execute();
        $nb_rowsMdp=$VerifMdp->rowCount();

       if ($nb_rowsMdp==1) {
       //Tous est ok

            $RecupClient=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET last_cnx=NOW() WHERE email=:email");
            $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupClient->execute();

            $_SESSION['idclient']=$RecupC->hash;

            header("location:".$Home."/Dashboard/");

       /*
            if ($RecupC->actif==1) {
                if ($Temps>=$Fin) {
                    // periode d'essai terminer Mettre actif a 0
                    $UpdateActif=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET actif=0, abo='Désactivé' WHERE email=:email");
                    $UpdateActif->bindParam(':email', $Email, PDO::PARAM_STR);
                    $UpdateActif->execute();
            
                    $Erreur.="- Votre période d'essai est arrivé à echéance<br />";
                    $Erreur.="Veuillez selectionner un abonnement afin de retrouver vos services<br />";
                    header("location:".$Home."/Abonnement/?erreur=".urlencode($Erreur));
                }
            }

            if ($RecupC->actif==2) {
                if ($Temps>=$Fin2) {
                    // Abo terminer Mettre actif a 0
                    $UpdateActif=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET actif=0, abo='Désactivé' WHERE email=:email");
                    $UpdateActif->bindParam(':email', $Email, PDO::PARAM_STR);
                    $UpdateActif->execute();
                }
            }

            $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE email=:email");
            $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupClient->execute();
            $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);


            if ($RecupC->actif==0) {
                $_SESSION['idclient']=$RecupC->hash;
                $_SESSION['Actif']="0";
                $Erreur="Votre compte n'est plus actif.<br />";
                $Erreur.="- Soit votre période d'essai est terminé<br />";
                $Erreur.="- Soit votre abonnement est arrivé à echéance<br />";
                $Erreur.="Veuillez prolonger votre abonnement afin de retrouver vos services</p>";
                header("location:".$Home."/Abonnement/?erreur=".urlencode($Erreur));
            }

            //Actif 1 > période d'essai; Actif 2 > Abo en cour; Actif 3 > Abo VIP;

            if ($RecupC->actif==1) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();

                $_SESSION['idclient']=$RecupC->hash;
                $_SESSION['Actif']="1";

                header("location:".$Home."/Dashboard/");
            }

            if ($RecupC->actif==2) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();

                $_SESSION['idclient']=$RecupC->hash;
                $_SESSION['Actif']="2";

                header("location:".$Home."/Dashboard/");
            }

            if ($RecupC->actif==3) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();

                $_SESSION['idclient']=$RecupC->hash;
                $_SESSION['Actif']="3";

                header("location:".$Home."/Dashboard/");
            }
            */
        }
        else {
            $Erreur="Le mot de passe ne correspond pas à cette adresse e-mail !</p>";       
        }
    }
}


if (isset($_POST['Valider'])) {
    $Entete ='From: "no-reply@e-soft.fr"<postmaster@e-soft.fr>'."\r\n";  
    $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";  
    $Message ="<html><head><title>Validation d'inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Validation d'inscription</H1></font>
        Merci de vous être inscrit.</p>
        Afin de pouvoir vous connecter, cliquer sur le lien suivant pour valider votre inscription sur $Home.</p>
        <a href='$Home/Validation/?id=$Code&Valid=1'>Cliquez ici</a></p>                   
        ____________________________________________________</p>
        Cordialement<br />
        $Home</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en étes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    $Entete2 ='From: "no-reply@e-soft.fr"<postmaster@e-soft.fr>'."\r\n";
    $Entete2 .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";               
    $Message2 ="<html><head><title>Inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Inscription</H1></font>           
        Une nouvelle inscription !</p>
        Email : ".$Email."<br />
        Nom : ".$Nom."<br />
        Prenom : ".$Prenom."<br />
        Téléphone : ".$Tel."<br />
        ____________________________________________________</p>
        Cordialement<br />
        devis.e-soft.fr</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en étes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."devis_compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !</p>";
    }

    elseif ($NbRowsEmail==1) {          
        $Erreur="Cette adresse E-mail existe déjé, veuillez en choisir une autre !</p>";
    }

    elseif (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractéres !</p>";
    }

    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !</p>";
    }

    elseif (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !</p>";
    }

    elseif (strlen($Prenom)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
    }

    elseif (strlen($Tel)<=9) { 
        $Erreur="Le numéro de téléphone doit etre saisie !</p>";
    }

    else {
        $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."devis_compte (email, nom, prenom, tel , hash, actif, abo, created) VALUES (:email, :nom, :prenom, :tel, :hash, 1, :abo, NOW())");
        $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertUser->bindParam(':hash', $Code, PDO::PARAM_STR);
        $InsertUser->bindParam(':abo', $Essai, PDO::PARAM_STR);
        $InsertUser->execute();

        $InsertSecu=$cnx->prepare("INSERT INTO visite_securite (ip, hash, created) VALUES (:ip, :hash, NOW())");
        $InsertSecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
        $InsertSecu->bindParam(':hash', $Code, PDO::PARAM_STR);
        $InsertSecu->execute();

        $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."devis_compte WHERE email=:email");
        $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $Salt2=md5($Mdp);
        $MdpCrypt=crypt($Salt2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET mdp=:mdpcrypt WHERE email=:email");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertMdp->execute();

        $Insertlogo=$cnx->prepare("INSERT INTO ".$Prefix."devis_param (hash) VALUE(:client)");
        $Insertlogo->bindParam(':client', $Code, PDO::PARAM_STR);
        $Insertlogo->execute();

        $Insertsecu=$cnx->prepare("INSERT INTO ".$Prefix."devis_securite (ip, created, hash) VALUE(:ip, NOW(), :client)");
        $Insertsecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
        $Insertsecu->bindParam(':client', $Code, PDO::PARAM_STR);
        $Insertsecu->execute();

        if ((!$InsertUser)||(!$InsertMdp)||(!$RecupCreated)||(!$VerifEmail)) {

            $DeleteUser=$cnx->prepare("DELETE FROM ".$Prefix."devis_compte WHERE email=:email");
            $DeleteUser->bindParam(':email', $Email, PDO::PARAM_STR);
            $DeleteUser->execute();

            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultérieurement !</p>";
        }
        
        else {
            if (!mail($Email, "Validation d'inscription", $Message, $Entete)) {                             
                $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";                       
            }

            elseif (!mail("minkus59@hotmail.com", "Nouvelle inscription", $Message2, $Entete2)) {
                $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";                           
            }
                        
            else {
                $Erreur="Bonjour, ".$Nom." ".$Prenom."<br />";
                $Erreur.="Merci de vous êtres inscrit sur www.doc-devis.fr<br />";
                $Erreur.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
                $Erreur.="Veuillez valider votre adresse e-mail avant de vous connecter !</p>";
            }
        }
    }
}


if ((isset($_POST['Recevoir']))&&($_POST['Recevoir']=="Recevoir")) {

    $Entete ='From: "no-reply@e-soft.fr"<postmaster@e-soft.fr>'."\r\n";
    $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";  
    $Message ="<html><head><title>Validation d'inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Validation d'inscription</H1></font>          
        Veuillez cliquer sur le lien suivant pour valider votre inscription sur $Home.</p>                       
        <a href='$Home/Validation/?id=$Client&Valid=1'>Cliquez ici</a></p>                 
        ____________________________________________________</p>
        Cordialement<br />
        $Home</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en étes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    if (!mail($Email, "Validation d'inscription", $Message, $Entete)) {                             
        $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
    }
                
    else {
        $Erreur="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
        $Erreur.="Veuillez valider votre adresse e-mail avant de vous connecter !</p>";                 
    }
}

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
<img src="<?php echo $Home; ?>/lib/img/facture-devis.jpg" width="100%">
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>
     
<?php if ($Cnx_Ok==false) { ?>

<div id="acc_gauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/article/accueil.inc.php"); ?>   
</div>

<div id="acc_droite">
<div id="login">
    <H1>Inscription</H1>
   <!-- <font color='#333333'>Essayer le sevice gratuitement pendant 14 jours !</font><p>-->

    <form id="form_inscription" action="" method="POST">
    <input class="Moyen" type="text" name="nom" placeholder="Nom" required="required"/>
    <br />
    <input class="Moyen" type="text" name="prenom" placeholder="Prénom" required="required"/>
    <br />
    <input class="Moyen" type="text" name="tel" placeholder="Numéro de téléphone" required="required"/>
    <br />
    <input class="Moyen" type="email" name="email" placeholder="Adresse e-mail" required="required"/>
    <br />
    <input class="Moyen" type="password" name="mdp" placeholder="Créer un mot de passe" required="required"/>
    <br />
    <input class="Moyen" type="password" name="mdp2" placeholder="Confirmer le mot de passe" required="required"/>
    </p>
    <input type="submit" name="Valider" value="M'inscrire"/>
    </p>
    </form>
</div>

<div id="login">
  <h1>Connexion</h1>

    <form id="form_cnx" action="" method="POST">
    <input class="Moyen" name="email" type="email" placeholder="Adresse e-mail" required="required"/>
    <br />
    <input class="Moyen" name="mdp" type="password" placeholder="Mot de passe" required="required"/>
    </p>
    <input type="submit" name="cnx" value="Connexion"/></p>
    <a href="<?php echo $Home; ?>/Securite/">Mot de passe oublié ?</a>
    <p>
    </form>
</div>
</div>

<?php
}
else {
    echo "Vous étes connecté !<p>";
    echo '<a href='.$Home.'/deconnexion.php>Déconnexion</a>';
}
?>

</article>

</div>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</center>
</body>
</html>