<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

if (isset($_POST['Valider'])) {

$Categorie=FiltreText('categorie');

  if ($Categorie===false) {
    $Erreur="Erreur !";
  }
    else {
    $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."devis_categorie (categorie, hash) VALUES (:categorie, :hash)");
    $InsertArticle->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
    $InsertArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $InsertArticle->execute();

    $Erreur="Catégorie ajouter avec sucèes";
    header("location:".$Home."/Article/Categorie/?erreur=".urlencode($Erreur));
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
<title>Article - Categorie</title>
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

<H1>Ajouter une catégorie</H1>

<form name="form_ajout" action="" method="POST">
<input class="Moyen" type="text" name="categorie" placeholder="Catégorie" required="required"/></p>

<input type="submit" name="Valider" value="Ajouter"/>
</form>

</p>
<font color='#FF0000'>*</font> : Informations requises  </p>

<H1>Liste des catégories existante</H1>

<table>
    <TR>
        <TH>
             Categorie
        </TH>
        <TH>
             Action
        </TH>
    </TR>
<?php while ($List=$ListCategorie->fetch(PDO::FETCH_OBJ)) {  ?>
    <TR>
        <TD>
             <?php echo $List->categorie;  ?>
        </TD>
        <TD>
             <a href="<?php echo $Home; ?>/Article/Categorie/SupprCategorie.php?id=<?php echo $List->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        </TD>
    </TR>
    <?php }  ?>
</table>


</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>