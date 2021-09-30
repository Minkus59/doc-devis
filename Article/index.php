<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Erreur.=$_GET['erreur'];

$SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE hash=:client ORDER BY categorie DESC");
$SelectArticleExist->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectArticleExist->execute();

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheReference'])) {
        $RechercheReference=trim($_POST['RechercheReference']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE reference=:reference AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient,':reference'=> $RechercheReference)); 
    }
    if (!empty($_POST['RechercheDescription'])) {
        $RechercheDescription=trim($_POST['RechercheDescription']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE description LIKE :description AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient, ':description' => "%".$RechercheDescription."%")); 
    }
    if (!empty($_POST['RechercheCategorie'])) {
        $RechercheCategorie=trim($_POST['RechercheCategorie']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE categorie=:categorie AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient,':categorie' =>$RechercheCategorie)); 
    }
    if (!empty($_POST['RecherchePu'])) {
        $RecherchePu=trim($_POST['RecherchePu']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE pu=:pu AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient,':pu' => $RecherchePu)); 
    }
    if (!empty($_POST['RechercheTva'])) {
        $RechercheTva=trim($_POST['RechercheTva']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE tva=:tva AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient,':tva' => $RechercheTva)); 
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
<title>Article</title>
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
    <table>
<TR>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Référence
    </TH>
    <?php } ?>
    <TH>
        Description
    </TH>
    <TH>
        PU HT
    </TH>
    <TH>
        TVA
    </TH>
    <TH>
        Categorie
    </TH>
    <TH>
        Action
    </TH>
</TR>

<form name="form_recherche" action="" method="POST">
<TR>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        <input class="Moyen" type="text" name="RechercheReference" autofocus/>
    </TH>
    <?php } ?>
    <TH>
        <input class="Max" type="text" name="RechercheDescription"/>
    </TH>
    <TH>
        <input class="Mini" type="text" name="RecherchePu"/>
    </TH>
    <TH >
        <input class="Mini" type="text" name="RechercheTva"/>
    </TH>
    <TH>
        <select name="RechercheCategorie">
            <option value="">-- --</option><?php
            while ($Categorie=$ListCategorie->fetch(PDO::FETCH_OBJ)) { 
            echo "<option value='".$Categorie->categorie."'>".$Categorie->categorie."</option>";
            } ?>
        </select>
    </TH>
    <TH>
        <input type="submit" name="MoteurRecherche" value="Rechercher"/>
    </TH>
</TR>
</form>

<?php
while($ArticleExist=$SelectArticleExist->fetch(PDO::FETCH_OBJ)) { ?>
    <tr>
        <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
        <td class="Moyen">
            <?php echo $ArticleExist->reference; ?>
        </td>
        <?php } ?>
        <td class="Max">
            <?php echo nl2br(stripslashes($ArticleExist->description)); ?>
        </td>
        <td class="Mini">
            <?php echo $ArticleExist->PU; ?>
        </td>
        <td class="Mini">
            <?php echo $ArticleExist->TVA; ?>
        </td>     
        <td class="Mini">
            <?php echo $ArticleExist->categorie; ?>
        </td>
        <td class="Mini">
            <a href="<?php echo $Home; ?>/Article/Modifier/?id=<?php echo $ArticleExist->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Article/SupprArticle.php?id=<?php echo $ArticleExist->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        </td>
    </tr>

<?php
}
?>
    </table

</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>