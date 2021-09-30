<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Description=$_POST['description'];
$PU=$_POST['PU'];
$TVA=$_POST['TVA'];
$Quantite=$_POST['quantite'];
$QuantiteBox=$_POST['quantite_box'];
$PoidBrut=$_POST['poid_brut'];
$PoidNet=$_POST['poid_net'];
$Marge=$_POST['marge'];

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

$SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE id=:article AND hash=:client");
$SelectArticle->bindParam(':article', $_GET['id'], PDO::PARAM_STR);
$SelectArticle->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectArticle->execute();
$Article=$SelectArticle->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {

    if(!preg_match("#[0-9.]#", $PU)) {
          $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
  elseif(!preg_match("#[0-9.]#", $TVA)) {
          $Erreur="La TVA n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
  elseif(!preg_match("#[0-9.]#", $Quantite)) {
          $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $UpdateArticle=$cnx->prepare("UPDATE ".$Prefix."devis_devis_article SET description=:description, PU=:PU, TVA=:TVA, quantite_box=:quantite_box, quantite=:quantite, poid_brut=:poid_brut, poid_net=:poid_net, marge=:marge WHERE id=:article AND hash=:client");
        $UpdateArticle->bindParam(':article', $_GET['id'], PDO::PARAM_STR);
        $UpdateArticle->bindParam(':description', $Description, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':PU', $PU, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':TVA', $TVA, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':marge', $Marge, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':quantite_box', $QuantiteBox, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':poid_brut', $PoidBrut, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':poid_net', $PoidNet, PDO::PARAM_STR);
        $UpdateArticle->bindParam(':client', $SessionClient, PDO::PARAM_STR);
        $UpdateArticle->execute();

        header("location:".$Home."/Devis/Modifier/?id=".$_GET['page']."&erreur=".urlencode($Erreur));
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
<title>Article - Modification</title>
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

<H1>Modifier un article</H1>
Si le Prix Unitaire est un 0, la mention "Offert" sera inscrit dans la colonne.<p>
<form name="form_ajout" action="" method="POST">
<table>
<TR>
<TR><?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Référence
    </TH> 
    <?php } ?>
    <TH>
        Description <font color='#FF0000'>*</font>
    </TH>
    <TH>
        Prix Unitaire HT <font color='#FF0000'>*</font>
    </TH>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Quantité Box <font color='#FF0000'>*</font>
    </TH>
    <?php } ?>
  <TH>
        Quantité <font color='#FF0000'>*</font>
    </TH>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Poid Brut <font color='#FF0000'>*</font>
    </TH>
    <TH>
        Poid Net <font color='#FF0000'>*</font>
    </TH>
    <?php } ?>
    <TH>
        Taux TVA <font color='#FF0000'>*</font>
    </TH>
    <TH>
        Marge
    </TH>
    <TH>
        
    </TH>
</TR>
<TR>
<?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" class="center" type="text" name="reference" value="<?php echo $Article->reference; ?>"/>
    </TD>
    <?php } ?>
    <TD>
        <textarea class="Max" name="description" required="required"><?php echo stripslashes($Article->description); ?></textarea>
    </TD>
    <TD>
        <input class="Mini" type="text" name="PU" value="<?php echo $Article->PU; ?>" required="required"/>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" type="text" name="quantite_box" required="required" value="<?php echo $Article->quantite_box; ?>"/>
    </TD>
    <?php } ?>
    <TD>
        <input class="Mini" type="text" name="quantite" value="<?php echo $Article->quantite; ?>" required="required"/>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" type="text" name="poid_brut" required="required" value="<?php echo $Article->poid_brut; ?>"/>
    </TD>
    <TD>
        <input class="Mini" type="text" name="poid_net" required="required" value="<?php echo $Article->poid_net; ?>"/>
    </TD>
    <?php } ?>
    <TD>
        <input class="Mini" type="text" name="TVA" value="<?php echo $Article->TVA; ?>" required="required"/>
    </TD>
    <TD>
        <input class="Mini" type="text" name="marge" value="<?php echo $Article->marge; ?>" />
    </TD>
    <TD>
        <input type="submit" name="Valider" value="Modifier"/>
    </TD>
</TR>
</table>
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