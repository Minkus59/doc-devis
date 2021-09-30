<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$Client=$cnx->prepare("SELECT * FROM ".$Prefix."devis_compte WHERE hash=:client");
$Client->bindParam(':client', $_SESSION['idclient'], PDO::PARAM_STR);
$Client->execute();
$InfoClient=$Client->fetch(PDO::FETCH_OBJ);

$Email=trim($_POST['email']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Tel=trim($_POST['tel']);
$Cp=trim($_POST['cp']);
$Adresse=$_POST['adresse'];
$Ville=$_POST['ville'];
$Erreur.=$_GET['erreur'];

$Genre=$_GET['genre'];

$Entete ='From: "no-reply@3donweb.fr"<postmaster@3donweb.fr>'."\r\n"; 
$Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";         
$Message ="<html><head><title>Changement d'adresse e-mail</title>
    </head><body>
    <font color='#9e2053'><H1>Changement d'adresse e-mail</H1></font>           
    Veuillez cliquer sur le lien suivant pour valider votre inscription sur $Home.</p>                       
    <a href='$Home/Validation/?id=$InfoClient->hash&Valid=1'>Cliquez ici</a></p>                   
    ____________________________________________________</p>
    Cordialement<br />
    $Home</p>
    <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
    </body></html>";

if (isset($_POST['Modifier0'])) {

    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."devis_compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif ($NbRowsEmail==1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");       
    }
    else {
        $ModiftUser=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET email=:email, valided=0 WHERE hash=:hash");
        $ModiftUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $ModiftUser->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $ModiftUser->execute();

        if (!mail($Email, "Changement d'adresse e-mail", $Message, $Entete)) {                          
            $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
            header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");       
        }
                        
        else {
            $Erreur="Enregistrement effectué avec succès !<br />";
            $Erreur.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."</p>";
            header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");           
        }
    }
}
if (isset($_POST['Modifier1'])) {

    if (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    else {
        $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."devis_compte WHERE hash=:hash");
        $RecupCreated->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $Salt2=md5($Mdp);
        $MdpCrypt=crypt($Salt2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET mdp=:mdpcrypt WHERE hash=:hash");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertMdp->execute();

        $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }
}

if (isset($_POST['Modifier3'])) {

    if (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif (strlen($Prenom)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif (strlen($Tel)<=9) { 
        $Erreur="Le numéro de téléphone doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif (strlen($Adresse)<=2) { 
        $Erreur="l'adresse doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif (strlen($Cp)<=4) { 
        $Erreur="Le code postal doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }

    elseif (strlen($Ville)<=2) { 
        $Erreur="La ville doit etre saisie !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");   
    }
    else {

        $InsertInfo=$cnx->prepare("UPDATE ".$Prefix."devis_compte SET nom=:nom, prenom=:prenom , tel=:tel, adresse=:adresse, cp=:cp, ville=:ville WHERE hash=:hash");
        $InsertInfo->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertInfo->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertInfo->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertInfo->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $InsertInfo->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $InsertInfo->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $InsertInfo->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertInfo->execute();
    
    $Erreur="Enregistrement effectué avec succès !</p>";
        header("location:".$Home."/Parametre/Compte/?erreur=".urlencode($Erreur)."");       
    }
}
?>
<!-- *******************************
*** Script réalisé par 3donweb ***
********* www.3donweb.fr *********
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

<?php
if ($Genre=="connexion") {
    ?>
    <fieldset>
    <legend>E-mail</legend></p>
    <form method="POST" action="">
    <label class="col_2">Adresse E-mail<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="email" name="email" value="<?php echo $InfoClient->email; ?>" required="required"/>
    <br />
    <input type="submit" name="Modifier0" value="Modifier"/>
    </form>
    </fieldset></p>
    <fieldset>
    <legend>Mot de passe</legend></p>
    <form method="POST" action="">
    <label class="col_2">Créer un mot de passe<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="password" name="mdp" required="required"/>
    <br />
    <label class="col_2">Confirmer le mot de passe<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="password" name="mdp2" required="required"/>
    <br />
    <input type="submit" name="Modifier1" value="Modifier"/>
    </form>
    </fieldset></p>
<?php
}
if ($Genre=="personnelles") {
    ?>
    <fieldset>
    <legend>Informations personnelles</legend></p>
    <form method="POST" action="">
    <label class="col_1">Nom<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="text" name="nom" value="<?php echo stripslashes($InfoClient->nom); ?>" required="required"/>
    <br />
    <label class="col_1">Prénom<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="text" name="prenom" value="<?php echo stripslashes($InfoClient->prenom); ?>" required="required"/>
    <br />
    <label class="col_1">Numéro de téléphone<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="text" name="tel" value="<?php echo $InfoClient->tel; ?>" required="required"/>
    <br />
    <label class="col_1">Adresse de siège<font color='#FF0000'>*</font> :</label>
    <textarea class="Moyen" name="adresse" required="required"><?php echo stripslashes($InfoClient->adresse); ?></textarea>
    <br />
    <label class="col_1">Code postal<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="text" name="cp" value="<?php echo $InfoClient->cp; ?>" required="required"/>
    <br />
    <label class="col_1">Ville<font color='#FF0000'>*</font> :</label>
    <input class="Moyen" type="text" name="ville" value="<?php echo stripslashes($InfoClient->ville); ?>" required="required"/>
    <br />
    <input type="submit" name="Modifier3" value="Modifier"/>
    </form>
    </fieldset>
    <?php
    }
?>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>