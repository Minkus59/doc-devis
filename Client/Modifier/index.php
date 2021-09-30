<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Civilite=$_POST['civilite'];
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Adresse=$_POST['adresse'];
$Ville=$_POST['ville'];
$Cp=$_POST['cp'];
$Tva=$_POST['tva'];
$Tel=trim($_POST['tel']);
$Email=trim($_POST['email']);

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE id=:client");
$SelectClient->bindParam(':client', $_GET['id'], PDO::PARAM_STR);
$SelectClient->execute();
$Client=$SelectClient->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {

    if (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !</p>";
    }
    elseif (strlen($Ville)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
    }
    elseif (strlen($Cp)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
    }
    elseif (strlen($Adresse)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
    }
    else {
        $InsertUser=$cnx->prepare("UPDATE ".$Prefix."devis_client SET email=:email, civilite=:civilite, nom=:nom, prenom=:prenom, tel=:tel, tva=:tva, adresse=:adresse, ville=:ville, cp=:cp WHERE id=:client");
        $InsertUser->bindParam(':client', $_GET['id'], PDO::PARAM_STR); 
        $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertUser->bindParam(':civilite', $Civilite, PDO::PARAM_STR);
        $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertUser->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $InsertUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $InsertUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $InsertUser->bindParam(':tva', $Tva, PDO::PARAM_STR);
        $InsertUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertUser->execute();

        $Erreur="Modification effectué avec succès !</p>";
        header("location:".$Home."/Client/?erreur=".urlencode($Erreur));
    }
}

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Client - Modification</title>
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

<H1>Modification</H1>

<form action="" method="POST">

<label class="col_1" for="type">Civilité</label>
<select name="civilite">
<option value="Mr" <?php if ($Client->civilite=="Mr") { ?> selected <?php } ?> >Mr</sub></option>
<option value="Mme" <?php if ($Client->civilite=="Mme") { ?> selected <?php } ?> >Mme</option>
<option value="Mlle" <?php if ($Client->civilite=="Mlle") { ?> selected <?php } ?> >Mlle</option>
<option value="Sarl" <?php if ($Client->civilite=="Sarl") { ?> selected <?php } ?> >Sarl</option>
<option value="SAS" <?php if ($Client->civilite=="SAS") { ?> selected <?php } ?> >SAS</option>
<option value="SASU" <?php if ($Client->civilite=="SASU") { ?> selected <?php } ?> >SASU</option>
<option value="Ets" <?php if ($Client->civilite=="Ets") { ?> selected <?php } ?> >Ets</option>
<option value="" <?php if ($Client->civilite==" ") { ?> selected <?php } ?> > </option>
</select>

<br />
<label class="col_1">Nom<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="nom" value="<?php echo stripslashes($Client->nom); ?>" required="required"/>
<br />
<label class="col_1">Prénom :</label>
<input class="Moyen" type="text" value="<?php echo stripslashes($Client->prenom); ?>" name="prenom"/>
<br />
<label class="col_1">Adresse<font color='#FF0000'>*</font> :</label>
<textarea class="Moyen" name="adresse" required="required"><?php echo stripslashes($Client->adresse); ?></textarea>
<br />
<label class="col_1">Code postal<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="cp" value="<?php echo $Client->cp; ?>" required="required"/>
<br />
<label class="col_1">Ville<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="ville" value="<?php echo stripslashes($Client->ville); ?>" required="required"/>
<br />
<label class="col_1">Numéro de TVA :</label>
<input class="Moyen" type="text" value="<?php echo $Client->tva; ?>" name="tva"/>
<br />
<label class="col_1">Numéro de téléphone :</label>
<input class="Moyen" type="text" value="<?php echo $Client->tel; ?>" name="tel"/>
<br />
<label class="col_1">Adresse E-mail :</label>
<input class="Moyen" type="email" value="<?php echo $Client->email; ?>" name="email"/>
</p>
<span class="col_1"></span>
<input type="submit" name="Valider" value="Modifier"/>
</form>

</p>
<font color='#FF0000'>*</font> : Informations requises

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>