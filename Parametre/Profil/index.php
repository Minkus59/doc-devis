<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$AE=$_POST['AE'];
$Pied=$_POST['pied'];
$Delai=$_POST['delai'];
$Civilite=$_POST['civilite'];
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Adresse=$_POST['adresse'];
$Cp=$_POST['cp'];
$Ville=$_POST['ville'];
$Siret=$_POST['siret'];
$Registre=$_POST['registre'];
$Autre1=$_POST['autre1'];
$Autre2=$_POST['autre2'];
$Erreur=$_GET['erreur'];

// Upload d'image
$chemin = $_FILES['photo']['name'];
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');
$ext_origin = strchr($chemin, '.');
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;


if ((isset($_POST['ajouter']))&&(in_array($ext_origin, $ext))) {

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
    }
    if (!isset($Erreur)){       

  if (in_array($ext_origin, $ext1)) {
  
        $NouvelleHauteur_photo = 80;
        $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );      
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
                $SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                unlink($rep.basename($Param->logo));

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."devis_param SET logo=:photo WHERE hash=:client");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $Insertlogo->execute();

                $UpdateType=$cnx->prepare("UPDATE ".$Prefix."devis_param SET type='1' WHERE hash=:client");
                $UpdateType->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $UpdateType->execute();

                $Erreur="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Parametre/Profil/?erreur=".urlencode($Erreur)."");
        }   
        else { $Erreur="Erreur !"; }    
    }
    if (in_array($ext_origin, $ext2)) {
  
        $NouvelleHauteur_photo = 80;
        $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );      
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
                $SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                unlink($rep.basename($Param->logo));

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."devis_param SET logo=:photo WHERE hash=:client");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $Insertlogo->execute();

                $UpdateType=$cnx->prepare("UPDATE ".$Prefix."devis_param SET type='1' WHERE hash=:client");
                $UpdateType->bindParam(':client', $SessionClient, PDO::PARAM_STR);
                $UpdateType->execute();

                $Erreur="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Parametre/Profil/?erreur=".urlencode($Erreur));
        }
    else { $Erreur="Erreur !"; }    
    }
  }
}

if (isset($_POST['enregistrer'])) {

    if (strlen($Nom)<=2) {
            $Erreur="Le nom doit être saisie !</p>";
        }

        elseif (strlen($Ville)<=2) {
            $Erreur="La ville doit être saisie !</p>";
        }

        elseif (strlen($Cp)<=2) {
            $Erreur="Le code postal doit être saisie !</p>";
        }

        elseif (strlen($Adresse)<=2) {
            $Erreur="L'adresse doit être saisie !</p>";
        }

        elseif (strlen($Siret)<=2) {
            $Erreur="Le n° de siret doit être saisie !</p>";
        }
        else {
        $UpdateInfo=$cnx->prepare("UPDATE ".$Prefix."devis_param SET civilite=:civilite, nom=:nom, prenom=:prenom, adresse=:adresse, cp=:cp, ville=:ville, siret=:siret, registre=:registre, autre1=:autre1, autre2=:autre2 WHERE hash=:client");
        $UpdateInfo->bindParam(':civilite', $Civilite, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':siret', $Siret, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':registre', $Registre, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':autre1', $Autre1, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':autre2', $Autre2, PDO::PARAM_STR);
        $UpdateInfo->bindParam(':client', $SessionClient, PDO::PARAM_STR);
        $UpdateInfo->execute();

        $Erreur="Enregistrement effectué avec succès !</p>";
                header("location:".$Home."/Parametre/Profil/?erreur=".urlencode($Erreur)."");
        }
}

if (isset($_POST['Modifier1'])) {

    $Insert2=$cnx->prepare("UPDATE ".$Prefix."devis_param SET pied=:pied WHERE hash=:client");
    $Insert2->bindParam(':pied', $Pied, PDO::PARAM_STR);
    $Insert2->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert2->execute();

    $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Profil/?erreur=".urlencode($Erreur)."");
}

if (isset($_POST['Modifier2'])) {
 
    $Insert=$cnx->prepare("UPDATE ".$Prefix."devis_param SET AE=:AE WHERE hash=:client");
    $Insert->bindParam(':AE', $AE, PDO::PARAM_STR);
    $Insert->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $Insert->execute();
  
  $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/Parametre/Profil/?erreur=".urlencode($Erreur)."");
}

?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Paramétre - Profil</title>
<META name="robots" content="noindex, nofollow">
<META http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
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

<H1>Général</H1>
<form action="" method="POST">
Si vous êtes Auto-entrepreneur, cocher la case <input type="checkbox" name="AE" value="1" <?php if ($Param->AE=="1") { echo "checked"; } ?>/> </p>
</p>
<input type="submit" name="Modifier2" value="Valider"/>
</form>
</p>

<p><HR /></p>

<H1>En-tête</H1>
<form id="form_entete1" name="form_entete1" action="" method="POST">

Civilité<font color='#FF0000'>*</font><br />
<select  name="civilite">
<option value="Mr" <?php if ($Param->civilite=="Mr") { echo "selected"; } ?> >Mr</option>
<option value="Mme" <?php if ($Param->civilite=="Mme") { echo "selected"; } ?>>Mme</option>
<option value="Mlle" <?php if ($Param->civilite=="Mlle") { echo "selected"; } ?>>Mlle</option>
<option value="Sarl" <?php if ($Param->civilite=="Sarl") { echo "selected"; } ?>>Sarl</option>
<option value="SAS" <?php if ($Param->civilite=="SAS") { echo "selected"; } ?>>SAS</option>
<option value="SASU" <?php if ($Param->civilite=="SASU") { echo "selected"; } ?>>SASU</option>
<option value="Ets" <?php if ($Param->civilite=="Ets") { echo "selected"; } ?>>Ets</option>
</select>
<p>
Nom de famille ou nom de société<font color='#FF0000'>*</font><br />
<input class="Moyen" type="text" name="nom" required="required" value="<?php echo stripslashes($Param->nom) ; ?>"/>
<p>
Prénom<br />
<input class="Moyen" type="text" name="prenom" value="<?php echo stripslashes($Param->prenom) ; ?>"/>
<p>
Adresse<font color='#FF0000'>*</font><br />
<textarea class="Moyen" name="adresse" required="required"><?php echo stripslashes($Param->adresse) ; ?></textarea>
<p>
Code postal<font color='#FF0000'>*</font><br />
<input class="Moyen" type="text" name="cp" required="required" value="<?php echo $Param->cp ; ?>"/>
<p>
Ville<font color='#FF0000'>*</font><br />
<input class="Moyen" type="text" name="ville" required="required" value="<?php echo stripslashes($Param->ville) ; ?>"/>
<p>
SIRET<font color='#FF0000'>*</font><br />
<input class="Moyen" type="text" name="siret" required="required" value="<?php echo $Param->siret ; ?>"/>
<p>
N° de registre du commerce et des société ou du répertoire des métiers "ne rien inscrire si Auto-entrepreneur"<br />
<input class="Moyen" type="text" name="registre" value="<?php echo stripslashes($Param->registre) ; ?>"/>
</p>
Complément 1<br />
<input class="Moyen" type="text" name="autre1" value="<?php echo stripslashes($Param->autre1) ; ?>"/>
</p>
Complément 2<br />
<input class="Moyen" type="text" name="autre2" value="<?php echo stripslashes($Param->autre2) ; ?>"/>
</p>
<input type="submit" name="enregistrer" value="Enregistrer"/>
</form>
</p>

<H1>Ajouter un logo</H1>


<form id="form_entete2" name="form_entete2" action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $Param->logo; ?>"/></p>

<input class="Moyen" type="file" name="photo"/> </p>

<input type="submit" name="ajouter" value="Ajouter"/> - <a href="<?php echo $Home; ?>/Parametre/Profil/SupprLogo.php">Supprimer l'image</a>
</form>
<p><HR /></p>

<H1>Pied de page</H1>
<form id="mention" action="" method="POST">
Texte inscrit en bas de page sur chaque devis et facture, (exemple : Capital social de XXXXX €, N° SIRET : XXXXXXXXXXXXXX TVA n°: XXXXXXXXXXXX etc...)<br />

<input class="Max" type="text" name="pied" size="62" value="<?php echo stripslashes($Param->pied); ?>"/>
</p>
<input type="submit" name="Modifier1" value="Modifier"/>
</form>

<script type="text/javascript" src="<?php echo $Home; ?>/lib/script/select_entete.js"></script>
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>