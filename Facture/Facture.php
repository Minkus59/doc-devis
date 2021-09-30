<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {

  $Temps=time();
// Recuperation
  $SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE id=:id");
  $SelectDevis->bindParam(":id", $_GET['id'], PDO::PARAM_STR);
  $SelectDevis->execute();
  $Devis=$SelectDevis->fetch(PDO::FETCH_OBJ);

  $SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE devis=:code AND hash=:hash");
  $SelectArticle->bindParam(":code", $Devis->code, PDO::PARAM_STR);
  $SelectArticle->bindParam(":hash", $SessionClient, PDO::PARAM_STR);
  $SelectArticle->execute();
  
  $SelectSupplement=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_supplement WHERE devis=:code AND hash=:hash");
  $SelectSupplement->bindParam(":code", $Devis->code, PDO::PARAM_STR);
  $SelectSupplement->bindParam(":hash", $SessionClient, PDO::PARAM_STR);
  $SelectSupplement->execute();
  $Supplement=$SelectSupplement->fetch(PDO::FETCH_OBJ);

  $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
  $SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
  $SelectParam->execute();
  $Param=$SelectParam->fetch(PDO::FETCH_OBJ);
  
//Incrementation des facture
  $Nb=$Param->nb_facture +1;
  $CodeFacture="FAC-".$Nb;

//Ajout de la facture
  $InsertFacture=$cnx->prepare("INSERT INTO ".$Prefix."devis_facture (code, client, created, acompte, acompte_montant, remarque, source, devise, taux, hash) VALUE(:code, :client, :temps, :acompte, :acompte_montant, :remarque, :source, :devise, :taux, :hash)");
  $InsertFacture->bindParam(':code', $CodeFacture, PDO::PARAM_STR);
  $InsertFacture->bindParam(":client", $Devis->client, PDO::PARAM_STR);
  $InsertFacture->bindParam(':temps', $Temps, PDO::PARAM_STR);
  $InsertFacture->bindParam(":acompte", $Devis->acompte, PDO::PARAM_STR);  
  $InsertFacture->bindParam(":acompte_montant", $Devis->acompte_montant, PDO::PARAM_STR);  
  $InsertFacture->bindParam(":remarque", $Devis->remarque, PDO::PARAM_STR);
  $InsertFacture->bindParam(":source", $Devis->code, PDO::PARAM_STR);
  $InsertFacture->bindParam(":devise", $Devis->devise, PDO::PARAM_STR);
  $InsertFacture->bindParam(":taux", $Devis->taux, PDO::PARAM_STR);
  $InsertFacture->bindParam(":hash", $Devis->hash, PDO::PARAM_STR);
  $InsertFacture->execute();

  $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."devis_param SET nb_facture=:nb_facture WHERE hash=:client");
  $UpdateParam->bindParam(':nb_facture', $Nb, PDO::PARAM_STR);
  $UpdateParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
  $UpdateParam->execute();
  
//Ajout des supplements
    $InsertSupplement=$cnx->prepare("INSERT INTO ".$Prefix."devis_facture_supplement (ddp, tb, cn, op, facture, hash) VALUES (:ddp, :tb, :cn, :op, :facture, :hash)");
    $InsertSupplement->bindParam(':ddp', $Supplement->ddp, PDO::PARAM_STR);
    $InsertSupplement->bindParam(':tb', $Supplement->tb, PDO::PARAM_STR);
    $InsertSupplement->bindParam(':cn', $Supplement->cn, PDO::PARAM_STR);
    $InsertSupplement->bindParam(':op', $Supplement->op, PDO::PARAM_STR);
    $InsertSupplement->bindParam(':hash', $Devis->hash, PDO::PARAM_STR);
    $InsertSupplement->bindParam(':facture', $CodeFacture, PDO::PARAM_STR);
    $InsertSupplement->execute();
  
//Ajout des articles
    while($Article=$SelectArticle->fetch(PDO::FETCH_OBJ)) {
        $InsertArticleDevis=$cnx->prepare("INSERT INTO ".$Prefix."devis_facture_article (reference, description, quantite_box, quantite, poid_brut, poid_net, PU, TVA, marge, devis, hash) VALUE(:reference, :description, :quantite_box, :quantite, :poid_brut, :poid_net, :PU, :TVA, :marge, :devis, :hash)");
        $InsertArticleDevis->bindParam(":hash", $SessionClient, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":devis", $CodeFacture, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":reference", $Article->reference, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":description", $Article->description, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":quantite_box", $Article->quantite_box, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":quantite", $Article->quantite, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":poid_brut", $Article->poid_brut, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":poid_net", $Article->poid_net, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":PU", $Article->PU, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":TVA", $Article->TVA, PDO::PARAM_STR);
        $InsertArticleDevis->bindParam(":marge", $Article->marge, PDO::PARAM_STR);
        $InsertArticleDevis->execute();
    }

    $UpdateSource=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET source=:source, facture='1' WHERE id=:id");
    $UpdateSource->bindParam(":id", $_GET['id'], PDO::PARAM_STR);
    $UpdateSource->bindParam(":source", $CodeFacture, PDO::PARAM_STR);
    $UpdateSource->execute();

    if (($InsertFacture==false)||($UpdateParam==false)||($InsertArticleDevis==false)||($UpdateSource==false)) {
       $Erreur="Une erreur est survenue, veuillez contactez l'administrateur du logiciel";
       header('Location:'.$Home.'/Devis/?erreur='.$Erreur);
    }
    else {
    header('Location:'.$Home.'/Facture/');
    }
}
    
if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('Location:'.$Home.'/Devis/');
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Confirmation</title>
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
Etes-vous sur de vouloir editer la facture de ce client ?

<TABLE width="300">
  <form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR></form>
</TABLE>
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>