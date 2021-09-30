<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect2.inc.php");

//Select Param
$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

//Select Devis
$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:client AND statue='En cours'");
$SelectDevis->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();

//Select Facture
$SelectFacture=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:client AND statue='En cours'");
$SelectFacture->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectFacture->execute();

//Select Client
$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$SelectClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectClient->execute();

//Selection de l'annee
if (isset($_POST['annee'])) {
   $_SESSION['annee']=$_POST['annee'];
}

if (!isset($_SESSION['annee'])) {
   $_SESSION['annee']=date('y', time());
}

$Annee=$_SESSION['annee'];
$AnneeSup=$Annee+1;
$Janvier=mktime(0,0,0,1,1,$Annee);
$Fevrier=mktime(0,0,0,2,1,$Annee);
$Mars=mktime(0,0,0,3,1,$Annee);
$Avril=mktime(0,0,0,4,1,$Annee);
$Mai=mktime(0,0,0,5,1,$Annee);
$Juin=mktime(0,0,0,6,1,$Annee);
$Juillet=mktime(0,0,0,7,1,$Annee);
$Aout=mktime(0,0,0,8,1,$Annee);
$Septembre=mktime(0,0,0,9,1,$Annee);
$Octobre=mktime(0,0,0,10,1,$Annee);
$Novembre=mktime(0,0,0,11,1,$Annee);
$Descembre=mktime(0,0,0,12,1,$Annee);
$JanvierSup=mktime(0,0,0,1,1,$AnneeSup);

$Mois=array('1' => 'Janvier', '2' => 'Fevrier', '3' => 'Mars', '4' => 'Avril', '5' => 'Mai', '6' => 'Juin', '7' => 'Juillet', '8' => 'Aout', '9' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre');
$Date=array('1' => $Janvier, '2' => $Fevrier, '3' => $Mars, '4' => $Avril, '5' => $Mai, '6' => $Juin, '7' => $Juillet, '8' => $Aout, '9' => $Septembre, '10' => $Octobre, '11' => $Novembre, '12' => $Descembre);

if (isset($_POST['codeClient'])) {
   $_SESSION['codeClient']=$_POST['codeClient'];
}

if ((!isset($_SESSION['codeClient']))||($_SESSION['codeClient']=="NULL")) {
    //Calcul Annuel EUR TOUS client-----------------------------------
    $SelectDevisAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='EUR'");
    $SelectDevisAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDevisAnneeEUR->execute(); 
    $CountEUR=$SelectDevisAnneeEUR->rowCount();

    for ($i=0;$ArticleAnneeEUR=$SelectDevisAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {

        $SelectArticleRecuAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture_article WHERE hash=:hash AND devis=:devis");
        $SelectArticleRecuAnneeEUR->bindParam(':devis', $ArticleAnneeEUR->code, PDO::PARAM_STR);
        $SelectArticleRecuAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticleRecuAnneeEUR->execute();

        for ($i=0;$ArticleRecuAnneeEUR=$SelectArticleRecuAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {
            if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
                $PU2=$ArticleRecuAnneeEUR->PU;
                $Marge2=$ArticleRecuAnneeEUR->marge;

                if (($Marge2<1) && ($Marge2>=0)) { 
                    $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
                }
                elseif (($Marge2<=100) && ($Marge2>=1)) {
                    $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
                }
                else {
                    $PUMarge2=number_format($PU2, 2,".", "");
                }

                $TotalRAEUR=round($ArticleRecuAnneeEUR->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
                $TotalHTRAEUR+=$TotalRAEUR;
                $ToTVARAEUR=round($TotalRAEUR/100*$ArticleRecuAnneeEUR->TVA, 3, PHP_ROUND_HALF_DOWN);
                $TotalTVARAEUR+=$ToTVARAEUR;
                $TotalTTCRAEUR+=$TotalRAEUR+$ToTVARAEUR;
            }
            else {
                $PU2=$ArticleRecuAnneeEUR->PU;
                $Marge2=$ArticleRecuAnneeEUR->marge;

                if (($Marge2<1) && ($Marge2>=0)) { 
                    $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
                }
                elseif (($Marge2<=100) && ($Marge2>=1)) {
                    $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
                }
                else {
                    $PUMarge2=number_format($PU2, 2,".", "");
                }

                $TotalRAEUR=round($ArticleRecuAnneeEUR->quantite*$PUMarge2, 2, PHP_ROUND_HALF_DOWN);
                $TotalHTRAEUR+=$TotalRAEUR;
                $ToTVARAEUR=round($TotalRAEUR/100*$ArticleRecuAnneeEUR->TVA, 2, PHP_ROUND_HALF_DOWN);
                $TotalTVARAEUR+=$ToTVARAEUR;
                $TotalTTCRAEUR+=$TotalRAEUR+$ToTVARAEUR;
            }
        }
    } 

    $SelectRecuAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE hash=:hash AND devise='EUR'");
    $SelectRecuAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectRecuAnneeEUR->execute(); 

    for ($i=0;$RecuAnneeEUR=$SelectRecuAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {  
        $TotalTTCRecuEUR+=$RecuAnneeEUR->montant;
    } 

    $ResteEUR= $TotalTTCRAEUR-$TotalTTCRecuEUR;
    $TotalTTCRecuEUR=0;

    //Calcul Annuel USD-----------------------------------
    $SelectDevisAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='USD'");
    $SelectDevisAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDevisAnneeUSD->execute(); 
    $CountUSD=$SelectDevisAnneeUSD->rowCount();

    for ($i=0;$ArticleAnneeUSD=$SelectDevisAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {
        $SelectArticleRecuAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture_article WHERE hash=:hash AND devis=:devis");
        $SelectArticleRecuAnneeUSD->bindParam(':devis', $ArticleAnneeUSD->code, PDO::PARAM_STR);
        $SelectArticleRecuAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticleRecuAnneeUSD->execute();

        for ($i=0;$ArticleRecuAnneeUSD=$SelectArticleRecuAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {
            $PU2=$ArticleRecuAnneeUSD->PU;
            $Marge2=$ArticleRecuAnneeUSD->marge;

            if (($Marge2<1) && ($Marge2>=0)) { 
                $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
            }
            elseif (($Marge2<=100) && ($Marge2>=1)) {
                $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
            }
            else {
                $PUMarge2=number_format($PU2, 2,".", "");
            }

            $TotalRAUSD=round($ArticleRecuAnneeUSD->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
            $TotalHTRAUSD+=$TotalRAUSD;
            $ToTVARAUSD=round($TotalRAUSD/100*$ArticleRecuAnneeUSD->TVA, 3, PHP_ROUND_HALF_DOWN);
            $TotalTVARAUSD+=$ToTVARAUSD;
            $TotalTTCRAUSD+=$TotalRAUSD+$ToTVARAUSD;
        }
    } 

    $SelectRecuAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE hash=:hash AND devise='USD'");
    $SelectRecuAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectRecuAnneeUSD->execute(); 

    for ($i=0;$RecuAnneeUSD=$SelectRecuAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {  
        $TotalTTCRecuUSD+=$RecuAnneeUSD->montant;
    } 

    $ResteUSD= $TotalTTCRAUSD-$TotalTTCRecuUSD;
    $TotalTTCRecuUSD=0;
}
else {
    //Calcul Annuel EUR PAr client -----------------------------------
    $SelectDevisAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE client=:client AND hash=:hash AND statue!='Annuler' AND devise='EUR'");
    $SelectDevisAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDevisAnneeEUR->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
    $SelectDevisAnneeEUR->execute(); 

    for ($i=0;$ArticleAnneeEUR=$SelectDevisAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {

        $SelectArticleRecuAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture_article WHERE hash=:hash AND devis=:devis");
        $SelectArticleRecuAnneeEUR->bindParam(':devis', $ArticleAnneeEUR->code, PDO::PARAM_STR);
        $SelectArticleRecuAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticleRecuAnneeEUR->execute();

        for ($i=0;$ArticleRecuAnneeEUR=$SelectArticleRecuAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {
            if (($Param->model=="model11")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
                $PU2=$ArticleRecuAnneeEUR->PU;
                $Marge2=$ArticleRecuAnneeEUR->marge;

                if (($Marge2<1) && ($Marge2>=0)) { 
                    $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
                }
                elseif (($Marge2<=100) && ($Marge2>=1)) {
                    $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
                }
                else {
                    $PUMarge2=number_format($PU2, 2,".", "");
                }

                $TotalRAEUR=round($ArticleRecuAnneeEUR->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
                $TotalHTRAEUR+=$TotalRAEUR;
                $ToTVARAEUR=round($TotalRAEUR/100*$ArticleRecuAnneeEUR->TVA, 3, PHP_ROUND_HALF_DOWN);
                $TotalTVARAEUR+=$ToTVARAEUR;
                $TotalTTCRAEUR+=$TotalRAEUR+$ToTVARAEUR;
            }
            else {
                $PU2=$ArticleRecuAnneeEUR->PU;
                $Marge2=$ArticleRecuAnneeEUR->marge;

                if (($Marge2<1) && ($Marge2>=0)) { 
                    $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
                }
                elseif (($Marge2<=100) && ($Marge2>=1)) {
                    $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
                }
                else {
                    $PUMarge2=number_format($PU2, 2,".", "");
                }

                $TotalRAEUR=round($ArticleRecuAnneeEUR->quantite*$PUMarge2, 2, PHP_ROUND_HALF_DOWN);
                $TotalHTRAEUR+=$TotalRAEUR;
                $ToTVARAEUR=round($TotalRAEUR/100*$ArticleRecuAnneeEUR->TVA, 2, PHP_ROUND_HALF_DOWN);
                $TotalTVARAEUR+=$ToTVARAEUR;
                $TotalTTCRAEUR+=$TotalRAEUR+$ToTVARAEUR;
            }
        }
    } 

    $SelectRecuAnneeEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE client=:client AND hash=:hash AND devise='EUR'");
    $SelectRecuAnneeEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectRecuAnneeEUR->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
    $SelectRecuAnneeEUR->execute(); 

    for ($i=0;$RecuAnneeEUR=$SelectRecuAnneeEUR->fetch(PDO::FETCH_OBJ);$i++) {  
        $TotalTTCRecuEUR+=$RecuAnneeEUR->montant;
    } 

    $ResteEUR= $TotalTTCRAEUR-$TotalTTCRecuEUR;
    $TotalTTCRecuEUR=0;

    //Calcul Annuel USD-----------------------------------
    $SelectDevisAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE client=:client AND hash=:hash AND statue!='Annuler' AND devise='USD'");
    $SelectDevisAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDevisAnneeUSD->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
    $SelectDevisAnneeUSD->execute(); 

    for ($i=0;$ArticleAnneeUSD=$SelectDevisAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {

        $SelectArticleRecuAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture_article WHERE hash=:hash AND devis=:devis");
        $SelectArticleRecuAnneeUSD->bindParam(':devis', $ArticleAnneeUSD->code, PDO::PARAM_STR);
        $SelectArticleRecuAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $SelectArticleRecuAnneeUSD->execute();

        for ($i=0;$ArticleRecuAnneeUSD=$SelectArticleRecuAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {
            $PU2=$ArticleRecuAnneeUSD->PU;
            $Marge2=$ArticleRecuAnneeUSD->marge;

            if (($Marge2<1) && ($Marge2>=0)) { 
                $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
            }
            elseif (($Marge2<=100) && ($Marge2>=1)) {
                $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
            }
            else {
                $PUMarge2=number_format($PU2, 2,".", "");
            }

            $TotalRAUSD=round($ArticleRecuAnneeUSD->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
            $TotalHTRAUSD+=$TotalRAUSD;
            $ToTVARAUSD=round($TotalRAUSD/100*$ArticleRecuAnneeUSD->TVA, 3, PHP_ROUND_HALF_DOWN);
            $TotalTVARAUSD+=$ToTVARAUSD;
            $TotalTTCRAUSD+=$TotalRAUSD+$ToTVARAUSD;
        }
    } 

    $SelectRecuAnneeUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE client=:client AND hash=:hash AND devise='USD'");
    $SelectRecuAnneeUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectRecuAnneeUSD->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
    $SelectRecuAnneeUSD->execute(); 

    for ($i=0;$RecuAnneeUSD=$SelectRecuAnneeUSD->fetch(PDO::FETCH_OBJ);$i++) {  
        $TotalTTCRecuUSD+=$RecuAnneeUSD->montant;
    } 

    $ResteUSD= $TotalTTCRAUSD-$TotalTTCRecuUSD;
    $TotalTTCRecuUSD=0;
}
?>
<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<META name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
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
if (isset($MAJ)) { echo "<p><font color='#FF6600'>".stripslashes($MAJ)."</font></p>"; } ?>

<p>
<form name="formClient" action="" method="POST">
<select name="codeClient" required="required" onChange="this.form.submit()">
<option value="NULL" <?php if ($_SESSION['codeClient']=="NULL") { echo "selected"; } ?> >-- Tous --</option>
<?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Client->code; ?>" <?php if ($_SESSION['codeClient']==$Client->code) { echo "selected"; } ?> ><?php echo $Client->nom; ?></option>
<?php } ?>
</select>
</form>
</p>

<div id="dashInfo">
<H1>Total des factures</H1>

<?php echo "Total HT : <b>".number_format($TotalHTRAEUR, 2,".", "")." EUR</b><br />";
echo "Total TVA : <b>".number_format($TotalTVARAEUR, 2,".", "")." EUR</b><br />";
echo "Total TTC : <b>".number_format($TotalTTCRAEUR, 2,".", "")." EUR</b><br /><br />";
    
if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
echo "Total HT : <b>".number_format($TotalHTRAUSD, 2,".", "")." USD</b><br />"; 
echo "Total TVA : <b>".number_format($TotalTVARAUSD, 2,".", "")." USD</b><br />"; 
echo "Total TTC : <b>".number_format($TotalTTCRAUSD, 2,".", "")." USD</b><br /><br />"; 
} ?>
Basé sur le Total TTC de vos factures "en cour" et "payer"
</div>

<div id="dashInfo">
<H1>Vos clients vous doivent</H1>
<?php 
echo "Total TTC : <b>".number_format($ResteEUR, 2,".", "")." EUR</b><br /><br />";

if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) {
echo "Total TTC : <b>".number_format($ResteUSD, 2,".", "")." USD</b><br /><br />";
} ?>

Basé sur le Total TTC de vos factures "en cour" et "payer"<BR />
En sont déduit le total des reçus
</div> 

<?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
<div id="dashInfo">
<H1>Répartition</H1>

<?php
if (($_SESSION['codeClient']=="NULL")||(!isset($_SESSION['codeClient']))) {
    $SelectDateEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='EUR' AND created BETWEEN :date1 AND :date2");
    $SelectDateEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDateEUR->bindParam(':date1', $Janvier, PDO::PARAM_STR);
    $SelectDateEUR->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectDateEUR->execute(); 
    $CountEUR=$SelectDateEUR->rowCount();

    $SelectDateUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='USD' AND created BETWEEN :date1 AND :date2");
    $SelectDateUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDateUSD->bindParam(':date1', $Janvier, PDO::PARAM_STR);
    $SelectDateUSD->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectDateUSD->execute(); 
    $CountUSD=$SelectDateUSD->rowCount();
}
else {
    $SelectDateEUR=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='EUR' AND client=:client AND created BETWEEN :date1 AND :date2");
    $SelectDateEUR->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDateEUR->bindParam(':date1', $Janvier, PDO::PARAM_STR);
    $SelectDateEUR->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectDateEUR->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);
    $SelectDateEUR->execute(); 
    $CountEUR=$SelectDateEUR->rowCount();

    $SelectDateUSD=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash AND statue!='Annuler' AND devise='USD' AND client=:client AND created BETWEEN :date1 AND :date2");
    $SelectDateUSD->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectDateUSD->bindParam(':date1', $Janvier, PDO::PARAM_STR);
    $SelectDateUSD->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectDateUSD->bindParam(':client', $_SESSION['codeClient'], PDO::PARAM_STR);   
    $SelectDateUSD->execute(); 
    $CountUSD=$SelectDateUSD->rowCount();
}


//Calcul pourcentage de facture par devise
$TotalCountDevise=$CountUSD+$CountEUR;
$PourcentageEUR=($CountEUR/$TotalCountDevise)*100;
$PourcentageUSD=($CountUSD/$TotalCountDevise)*100;

echo number_format($PourcentageEUR, 2, ".", "")." % EUR<BR />";
echo number_format($PourcentageUSD, 2, ".", "")." % USD<BR />";
?>
</div>
<?php } ?>

<p>
<form name="formAnnee" action="" method="POST">
&nbsp;&nbsp;&nbsp;&nbsp;<select name="annee" required="required" onChange="this.form.submit()">
<option value="2018" <?php if ($_SESSION['annee']=="2018") { echo "selected"; } ?> >2018</option>
<option value="2017" <?php if ($_SESSION['annee']=="2017") { echo "selected"; } ?> >2017</option>
<option value="2016" <?php if ($_SESSION['annee']=="2016") { echo "selected"; } ?> >2016</option>
<option value="2015" <?php if ($_SESSION['annee']=="2015") { echo "selected"; } ?> >2015</option>
</select>
</form>  
</p>  

<div id="dashInfo">
<H1>Chiffre d'affaire</H1>

<iframe src="<?php echo $Home; ?>/Dashboard/Graph/" width="550" height="320"></iframe>

    <table>
    <tr>
        <th>
            Mois
        </th>
        <th>
            Recette
        </th>
    </tr>
<?php  

for ($y=1;$y<=12;$y++) {
  $o=$y+1;
  if ($y=="12") {
    $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
    $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectArticle2->execute();    
  }
  else {
    $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
    $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date2', $Date[$o], PDO::PARAM_STR);    
    $SelectArticle2->execute();
  }
  
  for ($i=0;$Article2=$SelectArticle2->fetch(PDO::FETCH_OBJ);$i++) {  
    $TotalTTC+=$Article2->montant;
  }  
  ?>
      <tr>
          <td>
              <?php echo $Mois[$y]."<BR />"; ?> 
          </td>
          <td>
              <?php echo number_format($TotalTTC, 2,".", "");
              $TotalTTC=0; ?>
          </td>
      </tr> 
  <?php 
}
?> 
</table>
</div>

<div id="dashInfo">
<H1>Recette TVA</H1>

<iframe src="<?php echo $Home; ?>/Dashboard/Graph2/" width="550" height="320"></iframe>

    <table>
    <tr>
        <th>
            Mois
        </th>
        <th>
            Total
        </th>
    </tr>
<?php  
for ($y=1;$y<=12;$y++) {
  $o=$y+1;
  if ($y=="12") {
    $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
    $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date2', $JanvierSup, PDO::PARAM_STR);    
    $SelectArticle2->execute();    
  }
  else {
    $SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE date BETWEEN :date1 AND :date2 AND hash=:hash");
    $SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date1', $Date[$y], PDO::PARAM_STR);
    $SelectArticle2->bindParam(':date2', $Date[$o], PDO::PARAM_STR);    
    $SelectArticle2->execute();
  }
  
  for ($i=0;$Article2=$SelectArticle2->fetch(PDO::FETCH_OBJ);$i++) {  
    $TotalTTC=$Article2->montant;
    $ToTVA=($TotalTTC/100)*  20;
    $TotalTVA+=$ToTVA;
  }
  ?>
      <tr>
          <td>
              <?php echo $Mois[$y]."<BR />"; ?> 
          </td>
          <td>
              <?php echo number_format($TotalTVA, 2,".", "");
              $TotalTVA="0";
              $TotalTTC="0"; ?>
          </td>
      </tr> 
  <?php    
}
?>  
</table>
</div>

<div id="dashInfo">
<H1><a href="<?php echo $Home; ?>/Devis/">Devis en cours</a></H1>

    <table>
    <tr>
        <th>
            N° devis
        </th>
        <th>
            Client
        </th>
        <th>
            Date
        </th>
        <th>
            Action
        </th>
    </tr>
<?php
while($Devis=$SelectDevis->fetch(PDO::FETCH_OBJ)) {
$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClient->bindParam(':code', $Devis->client, PDO::PARAM_STR);
$SelectClient->execute();
$Client=$SelectClient->fetch(PDO::FETCH_OBJ);
?>
  <tr>
        <td class="code">
            <?php echo $Devis->code; ?>
        </td>
        <td class="code">
            <?php echo $Client->nom; ?>
        </td>
        <td class="code">
            <?php echo date("d/m/y", $Devis->created); ?>
        </td>
        <td class="code">
            <a href="<?php echo $Home; ?>/Devis/Visualisation/?id=<?php echo $Devis->id; ?>" target="_blank"><acronym title="Aperçu en PDF"><img src="<?php echo $Home; ?>/lib/img/pdf.png"/></acronym></a>
      <a href="<?php echo $Home; ?>/Devis/Modifier/?id=<?php echo $Devis->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Devis/SupprDevis.php?id=<?php echo $Devis->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        </td>
  </tr>
<?php
}                                                        
?>
</table>
</div>

<div id="dashInfo">
<H1><a href="<?php echo $Home; ?>/Facture/">Facture en cours</a></H1>
    <table>
    <tr>
        <th>
            N° facture
        </th>
        <th>
            Client
        </th>
        <th>
            Date
        </th>
        <th>
            Action
        </th>
    </tr>

<?php
while($Facture=$SelectFacture->fetch(PDO::FETCH_OBJ)) {
$SelectClientFacture=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
$SelectClientFacture->bindParam(':code', $Facture->client, PDO::PARAM_STR);
$SelectClientFacture->execute();
$ClientFacture=$SelectClientFacture->fetch(PDO::FETCH_OBJ);
?>
    <tr>
        <td class="code">
            <?php echo $Facture->code; ?>
        </td>
        <td class="code">
            <?php echo $ClientFacture->nom; ?>
        </td>
        <td class="code">
            <?php echo date("d/m/y", $Facture->created); ?>
        </td>
        <td class="code">
            <a href="<?php echo $Home; ?>/Facture/Visualisation/?id=<?php echo $Facture->id; ?>" target="_blank"><acronym title="Aperçu en PDF"><img src="<?php echo $Home; ?>/lib/img/pdf.png"/></acronym></a>
      <a href="<?php echo $Home; ?>/Facture/Modifier/?id=<?php echo $Facture->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Facture/SupprFacture.php?id=<?php echo $Facture->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        </td>
  </tr>
<?php
}                                                        
?>
</table>
</div>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>