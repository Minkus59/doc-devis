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
$Code = md5(uniqid(rand(), true));
$CodeClient=substr($Code, 0, 6);
$Temps=time();

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
        $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."devis_client (hash, code, email, civilite, nom, prenom, tel, tva, adresse, ville, cp) VALUES (:client, :code, :email, :civilite, :nom, :prenom, :tel, :tva, :adresse, :ville, :cp)");
        $InsertUser->bindParam(':client', $SessionClient, PDO::PARAM_STR);  
        $InsertUser->bindParam(':code', $CodeClient, PDO::PARAM_STR);
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

        $Erreur="Enregistrement effectué avec succés !</p>";
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
<title>Client - Nouveau</title>
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

<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

<article> 
<div id="erreur"><?php if (isset($Erreur)) { echo $Erreur; } ?></div>

<H1>Nouveau client</H1>

<form action="" method="POST">

<label class="col_1" for="type">Civilité</label>
<select name="civilite">
<option value="Mr"  selected >Mr</sub></option>
<option value="Mme" >Mme</option>
<option value="Mme" >Mlle</option>
<option value="Sarl" >Sarl</option>
<option value="SAS" >SAS</option>
<option value="SASU" >SASU</option>
<option value="Ets" >Ets</option>
</select>
<br />
<label class="col_1">Nom<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="nom" required="required"/>
<br />
<label class="col_1">Prénom :</label>
<input class="Moyen" type="text" name="prenom"/>
<br />
<label class="col_1">Adresse<font color='#FF0000'>*</font> :</label>
<textarea class="Moyen" name="adresse" required="required"></textarea>
<br />
<label class="col_1">Code postal<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="cp" required="required"/>
<br />
<label class="col_1">Ville<font color='#FF0000'>*</font> :</label>
<input class="Moyen" type="text" name="ville" required="required"/>
<br />
<label class="col_1">Numéro de TVA :</label>
<input class="Moyen" type="text" name="tva"/>
<br />
<label class="col_1">Numéro de téléphone :</label>
<input class="Moyen" type="text" name="tel"/>
<br />
<label class="col_1">Adresse E-mail :</label>
<input class="Moyen" type="email" name="email"/>
</p>
<span class="col_1"></span>
<input type="submit" name="Valider" value="Créer"/>
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