<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$SelectClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectClient->execute();

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash");
$SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();

$Type=$_GET['type'];
$Civilite=$_POST['civilite'];
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Adresse=$_POST['adresse'];
$Ville=$_POST['ville'];
$Cp=$_POST['cp'];
$Tva=$_POST['tva'];
$Tel=trim($_POST['tel']);
$Email=trim($_POST['email']);
$Client=trim($_POST['code']);
$Code2 = md5(uniqid(rand(), true));
$CodeClient=substr($Code2, 0, 6);
$Temps=time();

$Nb=$Param->nb_devis+1;
$CodeDevis2="DEV-".$Nb;
 
if (isset($_POST['Valider1'])) {

    if ((isset($Type))&&($Type=="Nouveau")) { 

        if (strlen($Nom)<=2) { 
            $Erreur="Le nom doit etre saisie !</p>";
        }
        elseif (strlen($Ville)<=2) { 
            $Erreur="La ville doit etre saisie !</p>";
        }
        elseif (strlen($Cp)<=2) { 
            $Erreur="Le code postal doit etre saisie !</p>";
        }
        elseif (strlen($Adresse)<=2) { 
            $Erreur="L'adresse doit etre saisie !</p>";
        }
        else {
            $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."devis_client (tva, hash, code, email, civilite, nom, prenom, tel, adresse, ville, cp) VALUES (:tva, :client, :code, :email, :civilite, :nom, :prenom, :tel, :adresse, :ville, :cp)");
            $InsertUser->bindParam(':client', $SessionClient, PDO::PARAM_STR);  
            $InsertUser->bindParam(':code', $CodeClient, PDO::PARAM_STR);
            $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
            $InsertUser->bindParam(':civilite', $Civilite, PDO::PARAM_STR);
            $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
            $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
            $InsertUser->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
            $InsertUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
            $InsertUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
            $InsertUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
            $InsertUser->bindParam(':tva', $Tva, PDO::PARAM_STR);
            $InsertUser->execute();

            $InsertDevis=$cnx->prepare("INSERT INTO ".$Prefix."devis_devis (hash, client, created) VALUES (:hash, :client, :temps)");
            $InsertDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
            $InsertDevis->bindParam(':client', $CodeClient, PDO::PARAM_STR);
            $InsertDevis->bindParam(':temps', $Temps, PDO::PARAM_STR);
            $InsertDevis->execute();

            $SelectCode=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE created=:temps AND hash=:client");
            $SelectCode->bindParam(':temps', $Temps, PDO::PARAM_STR);
            $SelectCode->bindParam(':client', $SessionClient, PDO::PARAM_STR);
            $SelectCode->execute();
            $CodeDevis=$SelectCode->fetch(PDO::FETCH_OBJ);

            $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_devis=:nb_devis WHERE hash=:client");
            $UpdateParam->bindParam(':nb_devis', $Nb, PDO::PARAM_STR);
            $UpdateParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
            $UpdateParam->execute();

            $InsertCode=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET code=:code WHERE created=:temps");
            $InsertCode->bindParam(':code', $CodeDevis2, PDO::PARAM_STR);
            $InsertCode->bindParam(':temps', $Temps, PDO::PARAM_STR);
            $InsertCode->execute();

            header("location:".$Home."/Devis/Modifier/?id=".$CodeDevis->id."");
        }
    }

    if ((isset($Type))&&($Type=="Existant")) { 

      $InsertDevis=$cnx->prepare("INSERT INTO ".$Prefix."devis_devis (hash, client, created) VALUES (:hash, :client, :temps)");
      $InsertDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
      $InsertDevis->bindParam(':client', $Client, PDO::PARAM_STR);
      $InsertDevis->bindParam(':temps', $Temps, PDO::PARAM_STR);
      $InsertDevis->execute();

      $SelectCode=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE created=:temps AND hash=:client");
      $SelectCode->bindParam(':temps', $Temps, PDO::PARAM_STR);
      $SelectCode->bindParam(':client', $SessionClient, PDO::PARAM_STR);
      $SelectCode->execute();
      $CodeDevis=$SelectCode->fetch(PDO::FETCH_OBJ);

      $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_devis=:nb_devis WHERE hash=:client");
      $UpdateParam->bindParam(':nb_devis', $Nb, PDO::PARAM_STR);
      $UpdateParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
      $UpdateParam->execute();

      $InsertCode=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET code=:code WHERE created=:temps");
      $InsertCode->bindParam(':code', $CodeDevis2, PDO::PARAM_STR);
      $InsertCode->bindParam(':temps', $Temps, PDO::PARAM_STR);
      $InsertCode->execute();

      header("location:".$Home."/Devis/Modifier/?id=".$CodeDevis->id."");
    }
}

?>
<!-- *******************************
*** Script rï¿½alisï¿½ par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Devis - Nouveau</title>
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

<H1>Client</H1>

<form name="form_devis" id="form_devis" action="" method="POST">

<label class="col_1" for="type">Type<font color='#FF0000'>*</font> :</label>
<select name="type" id="type" onChange="form_x(this.form)" required="required">
<option value="?type=NULL" <?php if ($Type=="NULL") { echo "selected"; } ?> >-- Selection --</option>
<option value="?type=Nouveau" <?php if ($Type=="Nouveau") { echo "selected"; } ?> >Nouveau client</option>
<option value="?type=Existant" <?php if ($Type=="Existant") { echo "selected"; } ?> >Client existant</option>
</select>
</p>

<?php if ((isset($Type))&&($Type=="Nouveau")) { ?>

<label class="col_1" for="type">Civilité</label>
<select name="civilite">
<option value="Mr"  selected >Mr</option>
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
<br />

<?php } 

 if ((isset($Type))&&($Type=="Existant")) { ?>

<label class="col_1">Code client<font color='#FF0000'>*</font> :</label>
<select name="code" required="required">
<option value="">-- Selection --</option>
<?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Client->code; ?>" ><?php echo $Client->nom; ?></option>
<?php } ?>
</select>

<?php } ?>
<p>
<span class="col_1"></span>
<input type="submit" name="Valider1" value="Créer"/>
</form>

</p>
<font color='#FF0000'>*</font> : Informations requises

<script type="text/javascript" src="<?php echo $Home; ?>/lib/script/select_client.js"></script>

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>