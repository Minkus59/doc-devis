<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Id=$_GET['id'];
$IdSource=$_GET['IdSource'];
$TypeSource=$_GET['TypeSource'];
$Source=$_POST['source'];
$Date=$_POST['date'];
$date=explode("/", $Date);
$Montant=$_POST['montant'];
$Lettre=$_POST['lettre'];
$Motif=$_POST['motif'];
$Mode=$_POST['mode'];
$Commentaire=$_POST['commentaire'];
$Code = md5(uniqid(rand(), true));
$CodeRecu=substr($Code, 0, 6);
$devise=$_POST['devise'];

if (isset($_GET['IdSource'])) { 
    if ($_GET['TypeSource']=="DEV") {
        $SelectSource=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE id=:id AND hash=:client");
        $SelectSource->bindParam(':id', $IdSource, PDO::PARAM_STR);
        $SelectSource->bindParam(':client', $SessionClient, PDO::PARAM_STR);
        $SelectSource->execute();
        $SelectSourceCheck=$SelectSource->fetch(PDO::FETCH_OBJ);
    }
    if ($_GET['TypeSource']=="FAC") {
        $SelectSource=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE id=:id AND hash=:client");
        $SelectSource->bindParam(':id', $IdSource, PDO::PARAM_STR);
        $SelectSource->bindParam(':client', $SessionClient, PDO::PARAM_STR);
        $SelectSource->execute();
        $SelectSourceCheck=$SelectSource->fetch(PDO::FETCH_OBJ);
    }
}

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

if ($Param->devise=="EUR") {
     $Devise="€";
}
if ($Param->devise=="USD") {
     $Devise="$";
}

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$Select->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$Select->execute();

$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash ORDER BY id DESC");
$SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();

$SelectFacture=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash ORDER BY id DESC");
$SelectFacture->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectFacture->execute();

if (isset($_GET['id'])) { 
    $SelectRecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE id=:id AND hash=:hash");
    $SelectRecu->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectRecu->BindParam(":id", $Id, PDO::PARAM_INT);
    $SelectRecu->execute();
    $Recu=$SelectRecu->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    if(empty($Source)) {
        $Erreur="La source doit être selectionnée";
    }
    elseif (strlen($date[2])!=4) {
        $Erreur="l'année doit comporter 4 chiffres";
    }
    else {
        $Date=mktime(0, 0, 0, $date[1], $date[0], $date[2]);

        $SelectDevis2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE code=:code AND hash=:hash");
        $SelectDevis2->bindParam(':code', $Source, PDO::PARAM_STR);
        $SelectDevis2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectDevis2->execute();
        $ClientDevisSource=$SelectDevis2->fetch(PDO::FETCH_OBJ);
        $CountDevis=$SelectDevis2->rowCount();

        $SelectFacture2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE code=:code AND hash=:hash");
        $SelectFacture2->bindParam(':code', $Source, PDO::PARAM_STR);
        $SelectFacture2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectFacture2->execute();
        $ClientFactureSource=$SelectFacture2->fetch(PDO::FETCH_OBJ);
        $CountFacture=$SelectFacture2->rowCount();

        if ($CountDevis==1) {
            $Client=$ClientDevisSource->client;
        }
        if ($CountFacture==1) {
            $Client=$ClientFactureSource->client;
        }

        $InsertUser=$cnx->prepare("UPDATE ".$Prefix."devis_recu SET date=:date, devise=:devise, montant=:montant, lettre=:lettre, mode=:mode, motif=:motif, commentaire=:commentaire, client=:client, source=:source WHERE id=:id AND hash=:hash");
        $InsertUser->BindParam(":id", $Id, PDO::PARAM_INT);
        $InsertUser->BindParam(":date", $Date, PDO::PARAM_STR);
        $InsertUser->BindParam(":montant", $Montant, PDO::PARAM_STR);
        $InsertUser->BindParam(":lettre", $Lettre, PDO::PARAM_STR);
        $InsertUser->BindParam(":motif", $Motif, PDO::PARAM_STR);
        $InsertUser->BindParam(":mode", $Mode, PDO::PARAM_STR);
        $InsertUser->BindParam(":commentaire", $Commentaire, PDO::PARAM_STR);
        $InsertUser->BindParam(":devise", $devise, PDO::PARAM_STR);
        $InsertUser->BindParam(":hash", $SessionClient, PDO::PARAM_STR);
        $InsertUser->BindParam(":client", $Client, PDO::PARAM_STR);
        $InsertUser->BindParam(":source", $Source, PDO::PARAM_STR);
        $InsertUser->execute();

        if ($InsertUser===false) {
            $Erreur="Erreur serveur, veuillez réessayer ultérieurement !";
        }
        else  {     
            $Valid="Reçu modifier avec succès";
            header("location:".$Home."/Recu/?valid=".urlencode($Valid));
        }
    }
}

if ((isset($_POST['Valider']))&&(!isset($_GET['id']))) {
    if(empty($Source)) {
        $Erreur="La source doit être selectionnée";
    }
    elseif (strlen($date[2])!=4) {
        $Erreur="l'année doit comporter 4 chiffres";
    }
    else {
        $Date=mktime(0, 0, 0, $date[1], $date[0], $date[2]);

        $SelectDevis2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE code=:code AND hash=:hash");
        $SelectDevis2->bindParam(':code', $Source, PDO::PARAM_STR);
        $SelectDevis2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectDevis2->execute();
        $ClientDevisSource=$SelectDevis2->fetch(PDO::FETCH_OBJ);
        $CountDevis=$SelectDevis2->rowCount();

        $SelectFacture2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE code=:code AND hash=:hash");
        $SelectFacture2->bindParam(':code', $Source, PDO::PARAM_STR);
        $SelectFacture2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectFacture2->execute();
        $ClientFactureSource=$SelectFacture2->fetch(PDO::FETCH_OBJ);
        $CountFacture=$SelectFacture2->rowCount();

        if ($CountDevis==1) {
            $Client=$ClientDevisSource->client;
        }
        if ($CountFacture==1) {
            $Client=$ClientFactureSource->client;
        }

        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."devis_recu (date, montant, lettre, mode, motif, commentaire, devise, created, hash, client, source, code) VALUES(:date, :montant, :lettre, :mode, :motif, :commentaire, :devise, NOW(), :hash, :client, :source, :code)");
        $Insert->BindParam(":date", $Date, PDO::PARAM_STR);
        $Insert->BindParam(":montant", $Montant, PDO::PARAM_STR);
        $Insert->BindParam(":lettre", $Lettre, PDO::PARAM_STR);
        $Insert->BindParam(":motif", $Motif, PDO::PARAM_STR);
        $Insert->BindParam(":mode", $Mode, PDO::PARAM_STR);
        $Insert->BindParam(":commentaire", $Commentaire, PDO::PARAM_STR);
        $Insert->BindParam(":devise", $devise, PDO::PARAM_STR);
        $Insert->BindParam(":hash", $SessionClient, PDO::PARAM_STR);
        $Insert->BindParam(":client", $Client, PDO::PARAM_STR);
        $Insert->BindParam(":source", $Source, PDO::PARAM_STR);
        $Insert->BindParam(":code", $CodeRecu, PDO::PARAM_STR);
        $Insert->execute();

        if ($Insert===false) {
            $Erreur="Erreur serveur, veuillez réessayer ultérieurement !";
        }
        else  {     
            $Valid="Reçu ajouter avec succès";
            header("location:".$Home."/Recu/?valid=".urlencode($Valid));
        }
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
<title>Reçu</title>
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

<H1>Nouveau reçu</H1></p>

<form name="form" action="" method="POST">

<select name="source" required="required">
<option value="">-- Source --</option>
<?php while($Devis=$SelectDevis->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Devis->code; ?>" <?php if ((isset($_GET['id']))&&($Devis->code==$Recu->source)) { echo "selected"; } ?><?php if ((isset($_GET['IdSource']))&&($SelectSourceCheck->code==$Devis->code)) { echo "selected"; } ?>><?php echo $Devis->code; ?></option>
<?php } ?>
<?php while($Facture=$SelectFacture->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Facture->code; ?>" <?php if ((isset($_GET['id']))&&($Facture->code==$Recu->source)) { echo "selected"; } ?><?php if ((isset($_GET['IdSource']))&&($SelectSourceCheck->code==$Facture->code)) { echo "selected"; } ?>><?php echo $Facture->code; ?></option>
<?php } ?>
</select>
</p>
<select name="mode" value="<?php if (isset($_GET['id'])) { echo $Recu->mode; } ?>" required="required">
<option value="">-- Mode de paiement --</option>
<option value="Espece" <?php if ((isset($_GET['id']))&&($Recu->mode=="Espece")) { echo "selected"; } ?>>Espece</option>
<option value="Chéque" <?php if ((isset($_GET['id']))&&($Recu->mode=="Chéque")) { echo "selected"; } ?>>Chéque</option>
<option value="Virement" <?php if ((isset($_GET['id']))&&($Recu->mode=="Virement")) { echo "selected"; } ?>>Virement</option>
</select>
</p>
<select name="devise">
<option value="EUR" <?php if ((isset($_GET['id']))&&($Recu->devise=="EUR")) { echo "selected"; } ?><?php if ((isset($_GET['IdSource']))&&($SelectSourceCheck->devise=="EUR")) { echo "selected"; } ?> >EUR - Euro</option>
<option value="USD" <?php if ((isset($_GET['id']))&&($Recu->devise=="USD")) { echo "selected"; } ?><?php if ((isset($_GET['IdSource']))&&($SelectSourceCheck->devise=="USD")) { echo "selected"; } ?> >USD - Dollar des États-Unies</option>
</select>
</p>
<input class="Moyen" type="text" name="date" value="<?php if (isset($_GET['id'])) { echo date("d/m/Y", $Recu->date); } ?>" placeholder="Date (format : 01/01/1970)" required="required"/></p>
<input class="Moyen" type="text" name="montant" value="<?php if (isset($_GET['id'])) { echo $Recu->montant; } ?>" placeholder="Montant en chiffre" required="required"/> <?php echo $Devise; ?></p>
<input class="Moyen" type="text" name="lettre" value="<?php if (isset($_GET['id'])) { echo $Recu->lettre; } ?>" placeholder="Montant en toutes lettres" required="required"/></p>
<input class="Moyen" type="text" name="motif" value="<?php if (isset($_GET['id'])) { echo $Recu->motif; } ?>" placeholder="Motif du paiement"/></p>
<textarea class="Moyen" name="commentaire" placeholder="Commentaire"><?php if (isset($_GET['id'])) { echo $Recu->commentaire; } ?></textarea></p>

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/></p> <?php } else { ?><input type="submit" name="Valider" value="Ajouter"/></p> <?php } ?>
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