<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
$Reference=$_POST['reference'];
$Description=$_POST['description'];
$PU=FiltreNum('PU');
$Categorie=FiltreText('categorie');
$TVA=FiltreNum('TVA');
$Marge=FiltreNum('marge');

  if ($PU===false) {
    $Erreur="Erreur !";
  }
  elseif ($Categorie===false) {
    $Erreur="Erreur !";
  }

    else {
    $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."devis_article (hash, reference, description, PU, categorie, TVA, marge) VALUES (:client, :reference, :description, :PU, :categorie, :TVA, :marge)");
    $InsertArticle->bindParam(':reference', $Reference, PDO::PARAM_STR);
    $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
    $InsertArticle->bindParam(':PU', $PU, PDO::PARAM_STR);
    $InsertArticle->bindParam(':TVA', $TVA, PDO::PARAM_STR);
    $InsertArticle->bindParam(':marge', $Marge, PDO::PARAM_STR);
    $InsertArticle->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
    $InsertArticle->bindParam(':client', $SessionClient, PDO::PARAM_STR);
    $InsertArticle->execute();

    header("location:".$Home."/Article/");
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
<title>Article - Nouveau</title>
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

<H1>Ajouter un article</H1>
Si le Prix Unitaire est un 0, la mention "Offert" sera inscrit dans la colonne.<p>
<form name="form_ajout" action="" method="POST">
<table>
<TR>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
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
    <TH>
        Taux TVA <font color='#FF0000'>*</font>
    </TH>
    <TH>
        Catégorie
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
        <input class="Mini" type="text" name="reference"/>
    </TD>
    <?php } ?>
    <TD>
        <textarea class="Moyen" name="description" required="required"></textarea>
    </TD>
    <TD>
        <input class="Mini" type="text" name="PU" required="required"/>
    </TD>
    <TD class="TVA">
        <input class="Mini" type="text" name="TVA"/>
    </TD>
    <TD>
        <select name="categorie"/><option value="">--  --</option>
        <?php while ($List=$ListCategorie->fetch(PDO::FETCH_OBJ)) { ?>
        <option value="<?php echo $List->categorie; ?>"><?php echo $List->categorie; ?></option>
        <?php } ?>
    </TD>
    <TD>
        <input class="Mini" type="text" name="marge"/>
    </TD> 
    <TD>
        <input type="submit" name="Valider" value="Ajouter"/>
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