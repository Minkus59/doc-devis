<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

$SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE id=:client");
$SelectArticle->bindParam(':client', $_GET['id'], PDO::PARAM_STR);
$SelectArticle->execute();
$Article=$SelectArticle->fetch(PDO::FETCH_OBJ);

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
$Reference=$_POST['reference'];
$Description=$_POST['description'];
$PU=FiltreNum('PU');
$TVA=FiltreNum('TVA');
$Categorie=FiltreText('categorie');
$Marge=FiltreNum('marge');

  if ($Categorie===false) {
    $Erreur="Erreur !";
  }
  elseif ($PU===false) {
    $Erreur="Erreur !";
  }  
    else {
    $InsertArticle=$cnx->prepare("UPDATE ".$Prefix."devis_article SET reference=:reference, description=:description, PU=:PU, TVA=:TVA, categorie=:categorie, marge=:marge WHERE id=:client");
    $InsertArticle->bindParam(':client', $_GET['id'], PDO::PARAM_STR); 
    $InsertArticle->bindParam(':reference', $Reference, PDO::PARAM_STR);
    $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
    $InsertArticle->bindParam(':PU', $PU, PDO::PARAM_STR);
    $InsertArticle->bindParam(':TVA', $TVA, PDO::PARAM_STR);
    $InsertArticle->bindParam(':marge', $Marge, PDO::PARAM_STR);
    $InsertArticle->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
    $InsertArticle->execute();

    $Erreur="Modification effectué avec succès !</p>";
    header("location:".$Home."/Article/?erreur=".urlencode($Erreur));
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
    <TH>
        Taux TVA <font color='#FF0000'>*</font>
    </TH>
    <TH>
        
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
        <textarea class="Moyen" name="description" required="required"><?php echo stripslashes($Article->description); ?></textarea>
    </TD>
    <TD>
        <input class="Mini" class="center" type="text" name="PU" value="<?php echo $Article->PU; ?>" required="required"/>
    </TD>
    <TD>
        <input class="Mini" class="center" type="text" name="TVA" value="<?php echo $Article->TVA; ?>"/>
    </TD>
    <TD>
        <select name="categorie"/><option value="<?php echo $Article->categorie; ?>"><?php echo $Article->categorie; ?></option>
        <?php while ($List=$ListCategorie->fetch(PDO::FETCH_OBJ)) { ?>
        <option value="<?php echo $List->categorie; ?>"><?php echo $List->categorie; ?></option>
        <?php } ?>
    </TD>
    <TD>
        <input class="Mini" type="text" name="marge" value="<?php echo $Article->marge; ?>"/>
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