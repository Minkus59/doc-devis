<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Type=$_GET['type'];
$Id=$_GET['id'];
$PagePre=$_SERVER['HTTP_REFERER'];
$Reference=$_POST['reference'];
$Description=$_POST['description'];
$Remarque=$_POST['remarque'];
$Date=$_POST['date'];
$Duree=$_POST['duree'];
$Inserer=$_POST['inserer'];
$Acompte=$_POST['acompte'];
$Accompte=$_POST['accompte'];
$Erreur=FiltreTextGET('erreur');
$Valid=FiltreTextGET('valid');

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."devis_param WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$PU=FiltreNum('PU');
$Marge=FiltreNum('marge');
$Quantite=FiltreNum('quantite');
$TVA=FiltreNum('TVA');
$QuantiteBox=FiltreNum('quantite_box');
$PoidBrut=FiltreNum('poid_brut');
$PoidNet=FiltreNum('poid_net');

if ($PU===false) {
  $Erreur="Erreur !";
}
if ($Quantite===false) {
  $Erreur="Erreur !";
}
if ($TVA===false) {
  $Erreur="Erreur !";
}
if ($QuantiteBox===false) {
  $Erreur="Erreur !";
}
if ($PoidBrut===false) {
  $Erreur="Erreur !";
}
if ($PoidNet===false) {
  $Erreur="Erreur !";
}
if ($Marge===false) {
  $Erreur="Erreur !";
}

$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE id=:id AND hash=:hash");
$SelectDevis->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
$SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();
$Devis=$SelectDevis->fetch(PDO::FETCH_OBJ);

if ($Devis->devise=="EUR") {
     $Devise="€";
}
if ($Devis->devise=="USD") {
     $Devise="$";
}

$SelectTransit=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_supplement WHERE hash=:hash AND devis=:devis");
$SelectTransit->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
$SelectTransit->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectTransit->execute();
$Transit=$SelectTransit->fetch(PDO::FETCH_OBJ);
$NumRowTransit=$SelectTransit->rowCount();

$SelectArticle=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE hash=:hash AND devis=:devis");
$SelectArticle->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
$SelectArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectArticle->execute();

$SelectArticle2=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis_article WHERE hash=:hash AND devis=:devis");
$SelectArticle2->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
$SelectArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectArticle2->execute();

$SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE hash=:client ORDER BY categorie DESC");
$SelectArticleExist->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectArticleExist->execute();

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."devis_categorie WHERE hash=:hash");
$ListCategorie->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$ListCategorie->execute();

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
    if (!empty($_POST['RechercheReference'])) {
        $RechercheReference=trim($_POST['RechercheReference']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."devis_article WHERE reference=:reference AND hash=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionClient,':reference' => $RechercheReference)); 
    }
}

$SelectClientDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:client AND code=:id");
$SelectClientDevis->bindParam(':client', $SessionClient, PDO::PARAM_STR);
$SelectClientDevis->bindParam(':id', $Devis->client, PDO::PARAM_STR);
$SelectClientDevis->execute();
$ClientDevis=$SelectClientDevis->fetch(PDO::FETCH_OBJ);

// Ajout supplement
if (isset($_POST['Model2'])) {
    $Ddp=$_POST['ddp'];
    $Cn=$_POST['cn'];
    $Op=$_POST['op'];
    
    if ($NumRowTransit==0) {
        $InsertSupplement=$cnx->prepare("INSERT INTO ".$Prefix."devis_devis_supplement (ddp, cn, op, devis, hash) VALUES (:ddp, :cn, :op, :devis, :hash)");
        $InsertSupplement->bindParam(':ddp', $Ddp, PDO::PARAM_STR);
        $InsertSupplement->bindParam(':cn', $Cn, PDO::PARAM_STR);
        $InsertSupplement->bindParam(':op', $Op, PDO::PARAM_STR);
        $InsertSupplement->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertSupplement->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
        $InsertSupplement->execute();        
    }
    else {
        $UpdateSupplement=$cnx->prepare("UPDATE ".$Prefix."devis_devis_supplement SET ddp=:ddp, cn=:cn, op=:op WHERE devis=:devis AND hash=:hash");
        $UpdateSupplement->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
        $UpdateSupplement->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $UpdateSupplement->bindParam(':ddp', $Ddp, PDO::PARAM_STR);
        $UpdateSupplement->bindParam(':cn', $Cn, PDO::PARAM_STR);
        $UpdateSupplement->bindParam(':op', $Op, PDO::PARAM_STR);
        $UpdateSupplement->execute();
    }
    header("location:".$PagePre);
}

//Ajout de nouveaux articles
if (isset($_POST['Valider'])) {

    if(!preg_match("#[0-9.]#", $PU)) {
          $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }

  elseif(!preg_match("#[0-9.]#", $Quantite)) {
          $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }

    else {
        if ($Inserer=="1") {
            $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."devis_article (hash, reference, description, PU, TVA, marge) VALUES (:client, :reference, :description, :PU, :TVA, :marge)");
            $InsertArticle->bindParam(':reference', $Reference, PDO::PARAM_STR);
            $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
            $InsertArticle->bindParam(':PU', $PU, PDO::PARAM_STR);
            $InsertArticle->bindParam(':TVA', $TVA, PDO::PARAM_STR);
            $InsertArticle->bindParam(':marge', $Marge, PDO::PARAM_STR);
            $InsertArticle->bindParam(':client', $SessionClient, PDO::PARAM_STR);
            $InsertArticle->execute();
        }

        $InsertArticle2=$cnx->prepare("INSERT INTO ".$Prefix."devis_devis_article (reference, description, PU, quantite_box, quantite, poid_brut, poid_net, TVA, marge, devis, hash) VALUES (:reference, :description, :PU, :quantite_box, :quantite, :poid_brut, :poid_net, :TVA, :marge, :devis, :hash)");
        $InsertArticle2->bindParam(':reference', $Reference, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':description', $Description, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':PU', $PU, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':quantite_box', $QuantiteBox, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':poid_brut', $PoidBrut, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':poid_net', $PoidNet, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':TVA', $TVA, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':marge', $Marge, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
        $InsertArticle2->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertArticle2->execute();

        header("location:".$PagePre);
    }
}
//Ajout d'article de la bdd
if (isset($_POST['Ajouter'])) {
    if(!preg_match("#[0-9.]#", $PU)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
  elseif(!preg_match("#[0-9.]#", $Quantite)) {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."devis_devis_article (reference, description, PU, quantite_box, quantite, poid_brut, poid_net, TVA, marge, devis, hash) VALUES (:reference, :description, :PU, :quantite_box, :quantite, :poid_brut, :poid_net, :TVA, :marge, :devis, :hash)");
        $InsertArticle->bindParam(':reference', $Reference, PDO::PARAM_STR);
        $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
        $InsertArticle->bindParam(':PU', $PU, PDO::PARAM_STR);
        $InsertArticle->bindParam(':quantite_box', $QuantiteBox, PDO::PARAM_STR);
        $InsertArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $InsertArticle->bindParam(':poid_brut', $PoidBrut, PDO::PARAM_STR);
        $InsertArticle->bindParam(':poid_net', $PoidNet, PDO::PARAM_STR);
        $InsertArticle->bindParam(':TVA', $TVA, PDO::PARAM_STR);
        $InsertArticle->bindParam(':marge', $Marge, PDO::PARAM_STR);
        $InsertArticle->bindParam(':devis', $Devis->code, PDO::PARAM_STR);
        $InsertArticle->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
        $InsertArticle->execute();

        header("location:".$PagePre);
    }
}

// Ajout acompte
if (isset($_POST['Activer'])) {

  $UpdateAcompte=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET acompte=:acompte, acompte_montant=:accompte WHERE hash=:hash AND id=:id");
  $UpdateAcompte->bindParam(':acompte', $Acompte, PDO::PARAM_STR);
  $UpdateAcompte->bindParam(':accompte', $Accompte, PDO::PARAM_STR);  
  $UpdateAcompte->bindParam(':id', $Id, PDO::PARAM_STR);
  $UpdateAcompte->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
  $UpdateAcompte->execute();
  
    header("location:".$PagePre);
}

if (isset($_POST['Desactiver'])) {

  $UpdateAcompte=$cnx->prepare("UPDATE ".$Prefix."devis_devis SET acompte=0 WHERE hash=:hash AND id=:id");
  $UpdateAcompte->bindParam(':id', $Id, PDO::PARAM_STR);
  $UpdateAcompte->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
  $UpdateAcompte->execute();

    header("location:".$PagePre);
}

?>

<!-- *******************************
*** Script réalisé par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Devis - Modification</title>
<META name="robots" content="noindex, nofollow">
<link href="<?php echo $Home; ?>/lib/css/misenpa.css" rel="stylesheet" type="text/css"/>

<script>
	function createInstance() {
        var req = null;
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) 
				{
					alert("XHR not created");
				}
			}
	    }
        return req;
	};
</script>
<script>
        function submitFormDate(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageDate');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var date = document.Form_Date.date.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "date=" + date;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/date.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
<script>
        function submitFormDevise(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageDevise');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var devise = document.form_devise.devise.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "devise=" + devise;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/devise.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
<script>
        function submitFormTaux(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageTaux');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var taux = document.form_taux.taux.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "taux=" + taux;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/taux.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
<script>
        function submitFormDebut(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageDebut');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var debut = document.Form_Debut.debut.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "debut=" + debut;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/debut.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
<script>
        function submitFormDuree(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageDuree');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var duree = document.Form_Duree.duree.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "duree=" + duree;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/duree.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
<script>
        function submitFormRemarque(element) { 
            function storing(data) {
            //envoi des element receptionné dans la div
                var element = document.getElementById('AffichageRemarque');
                element.innerHTML = data;
            } 
            
            var req =  createInstance();
            //récuperation des champs du formulaire
            var remarque = document.Form_Remarque.remarque.value;
            //création >> nomChamp = nomVariable & nomChamp = nomVariable etc...
            var data = "remarque=" + remarque;

            req.onreadystatechange = function() { 
                if(req.readyState == 4)
                {
                    if(req.status == 200)
                    {
                        storing(req.responseText);  
                    }   
                    else    
                    {
                        alert("Error: returned status code " + req.status + " " + req.statusText);
                    }   
                } 
            }; 
            
            req.open("POST", "<?php echo $Home; ?>/lib/script/Devis/remarque.php?id=<?php echo $Devis->id; ?>", true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            //envoi
            req.send(data);  
        }
</script>
</head>

<body>
<CENTER>
<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/head.inc.php"); 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/menu.inc.php");
?>  

<div id="Content">
<div id="Center">
 
<div id="Page">
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } 
if (isset($MAJ)) { echo "<font color='#FF6600'>".stripslashes($MAJ)."</font><BR />"; } ?>

<a href="<?php echo $Home; ?>/Devis/Visualisation/?id=<?php echo $Devis->id; ?>" target="_blank"><acronym title="Aperçu en PDF"><img src="<?php echo $Home; ?>/lib/img/pdf.png"/></acronym></a></p>

<div id="info"><form name="Form_Date" action="<?php echo $Home; ?>/lib/script/Devis/date2.php?id=<?php echo $Devis->id; ?>" method="POST">
Devis N°: <?php echo $Devis->code; ?><BR />
Client N°: <?php echo $Devis->client; ?><BR /><BR />

Date :<input type="text" class="Mini" value="<?php echo date("d/m/Y", $Devis->created); ?>" name="date" required="required" onChange="submitFormDate()"/></form>
<div id="AffichageDate"></div>

<?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
<form name="form_devise" action="" method="POST">
<select name="devise" onChange="submitFormDevise()">
<option value="" >-- Devise --</option>
<option value="EUR" <?php if ($Devis->devise=="EUR") { echo "selected"; } ?> >EUR - Euro</option>
<option value="USD" <?php if ($Devis->devise=="USD") { echo "selected"; } ?> >USD - Dollar des États-Unies</option>
</select>
</form>
<div id="AffichageDevise"></div>

<form name="form_taux" action="<?php echo $Home; ?>/lib/script/Devis/taux2.php?id=<?php echo $Devis->id; ?>" method="POST">
<input type="text" class="Moyen" value="<?php echo $Devis->taux; ?>" name="taux" placeholder="Taux en vigueur" onChange="submitFormTaux()"/>
</form>
<div id="AffichageTaux"></div>
<?php } ?>
</div>

<?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
<div id="info">
<iframe name="prodouane" src="https://pro.douane.gouv.fr/debweb/cf.srv?etape=rechercheTauxTaux&amp;paysOrigine=<?php if ($Devis->devise==EUR) { echo "PL"; } if ($Devis->devise=="USD") { echo "US"; } ?>&amp;dateSaisie=<?php echo date("dmY", $Devis->created); ?>&amp;suivant=suivant" scrolling="yes"></iframe>
</div>
<?php } ?>

<a href="<?php echo $Home; ?>/Client/Modifier/?id=<?php echo $ClientDevis->id; ?>" ><acronym title="Modifier les informations client">
<div id="client">
<?php if (($Devis->client!="NULL")||(!empty($Devis->client))) {    
   echo $ClientDevis->civilite." ".$ClientDevis->nom." ".$ClientDevis->prenom."<BR />"
   .$ClientDevis->adresse."<BR />"
   .$ClientDevis->cp.", ".$ClientDevis->ville."<BR />
   Tel : ".$ClientDevis->tel;   
   } ?>
</div></a>

<table>
<TR>
    <TH class="Mini">
        ID
    </TH>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Référence
    </TH>
    <?php } ?>
    <TH>
        Description
    </TH>
    <TH>
        PU / HT
    </TH>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Quantité Box
    </TH>
    <?php } ?>
    <TH>
        Quantité
    </TH>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TH>
        Poid Brut
    </TH>
    <TH>
        Poid Net
    </TH>
    <?php } ?>
    <TH>
        TVA
    </TH>
    <TH>
        Marge
    </TH>   
  <TH class="Mini">
        Total
    </TH>
  <TH>
        Insérer 
    </TH>
    <TH>
        Action
    </TH>
</TR>
<?php
$I=1; 
while ($Article=$SelectArticle->fetch(PDO::FETCH_OBJ)) { 
    $PU=$Article->PU;
    $Marge=$Article->marge;

    if (($Marge<1) && ($Marge>=0)) { 
        $PUMarge=number_format($PU+($PU*$Marge), 2,".", "");
    }
    elseif (($Marge<=100) && ($Marge>=1)) {
        $PUMarge=number_format($PU+(($PU*$Marge)/100), 2,".", "");            
    }
    else {
        $PUMarge=number_format($PU, 2,".", "");
    } ?>
<TR>
     <TD>
        <?php echo $I; ?>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
     <TD>
        <?php echo $Article->reference; ?>
    </TD>
    <?php } ?>
     <TD class="Gauche">
    <?php echo nl2br(stripslashes($Article->description)); ?>
    </TD>
     <TD>
        <?php echo number_format($PUMarge, 2,".", "")." ".$Devise; ?>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <?php echo $Article->quantite_box; ?>
    </TD>
    <?php } ?>
     <TD>
        <?php echo $Article->quantite; ?>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <?php echo $Article->poid_brut; ?>
    </TD>
    <TD>
        <?php echo $Article->poid_net; ?>
    </TD>
    <?php } ?>
     <TD>
        <?php echo number_format($Article->TVA, 2,".", ""); ?>
    </TD>
      <TD>
        <?php echo $Article->marge; ?>
    </TD>   
     <TD>
        <?php echo number_format($PUMarge*$Article->quantite, 2,".", "")." ".$Devise; ?>
    </TD>
     <TD>
    
   </TD>
     <TD>
        <a href="<?php echo $Home; ?>/Devis/Modifier/Modifier/?id=<?php echo $Article->id; ?>&page=<?php echo $_GET['id']; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
      <a href="<?php echo $Home; ?>/Devis/Modifier/SupprArticle.php?id=<?php echo $Article->id; ?>&page=<?php echo $_GET['id']; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></a>
  </TD>
</TR>
<?php
$I++;
} ?>
<form name="form_ajout" action="" method="POST">
<TR>
    <TD>
        
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" type="text" name="reference"/>
    </TD>
    <?php } ?>
    <TD>
        <textarea class="Max" name="description" required="required"></textarea>
    </TD>
    <TD>
        <input class="Mini" type="text" name="PU" required="required"/>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" type="text" name="quantite_box" required="required"/>
    </TD>
    <?php } ?>
    <TD>
        <input class="Mini" type="text" name="quantite" required="required"/>
    </TD>
    <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
    <TD>
        <input class="Mini" type="text" name="poid_brut" required="required"/>
    </TD>
    <TD>
        <input class="Mini" type="text" name="poid_net" required="required"/>
    </TD>
    <?php } ?>
    <TD>
        <input class="Mini" type="text" name="TVA"/>
    </TD> 
    <TD>
        <input class="Mini" type="text" name="marge"/>
    </TD> 
  <TD>
  
  </TD>
  <TD>
    <acronym title="Insérer l'article dans la base de donnée"><input class="Mini" type="checkbox" name="inserer" value="1"/></acronym>
    </TD>
    <TD>
        <input type="submit" name="Valider" value="Ajouter"/>
    </TD>
</TR></form>
</table>

 <p>
 
 <?php
for ($i=0;$Article2=$SelectArticle2->fetch(PDO::FETCH_OBJ);$i++) {
    
    $PU2=$Article2->PU;
    $Marge2=$Article2->marge;

    if (($Marge2<1) && ($Marge2>=0)) { 
        $PUMarge2=number_format($PU2+($PU2*$Marge2), 2,".", "");
    }
    elseif (($Marge2<=100) && ($Marge2>=1)) {
        $PUMarge2=number_format($PU2+(($PU2*$Marge2)/100), 2,".", "");            
    }
    else {
        $PUMarge2=number_format($PU2, 2,".", "");
    }

    $Total=round($Article2->quantite*$PUMarge2, 3, PHP_ROUND_HALF_DOWN);
    $TotalHT+=$Total;
    $ToTVA=round($Total/100*$Article2->TVA, 3, PHP_ROUND_HALF_DOWN);
    $TotalTVA+=$ToTVA;
    $TotalTTC+=$Total+$ToTVA; 
    $TotalPiece+=$Article2->quantite;
    $TotalBox+=$Article2->quantite_box;
    $PoidBrut+=$Article2->poid_brut;
    $PoidNet+=$Article2->poid_net;
    
    if ($Devis->acompte==1) { $AccomptE= round( $TotalTTC/100*$Devis->acompte_montant, 3, PHP_ROUND_HALF_DOWN); }
    if ($Devis->acompte==2) { $AccomptE= round( $Devis->acompte_montant, 3, PHP_ROUND_HALF_DOWN); }
}
?>
<p><HR /></p>

<div id="Total">
<?php echo "Total HT : <b>".number_format($TotalHT, 2,".", "")." ".$Devise."</b><br />"; ?>
<?php echo "Total TVA : <b>".number_format($TotalTVA, 2,".", "")." ".$Devise."</b><br />"; ?>
<?php echo "Total TTC : <b>".number_format($TotalTTC, 2,".", "")." ".$Devise."</b><br />"; ?>
<?php echo "Accompte : <b>".number_format($AccomptE, 2,".", "")." ".$Devise."</b><br />"; ?>
</div>

<?php
if ($Devis->acompte=="0") {
  ?><form name="FormAcompte" action="" method="POST">
  
  Type d'acompte :
  <select name="type" id="type" onChange="form_x(this.form)">
  <option value="?id=<?php echo $Id; ?>" <?php if ($Type=="NULL") { echo "selected"; } ?> >- - - - - -</option>
  <option value="?id=<?php echo $Id; ?>&type=pourcentage" <?php if ($Type=="pourcentage") { echo "selected"; } ?> >%</option>
  <option value="?id=<?php echo $Id; ?>&type=euro" <?php if ($Type=="euro") { echo "selected"; } ?> >€¤</option>
  </select> <BR />

  <?php if ((isset($Type))&&($Type=="pourcentage")) { ?>  
    <input type="hidden" name="acompte" value="1"/>Montant en %<font color='#FF0000'>*</font> : <input type="text" name="accompte" value="<?php echo $Devis->acompte_montant; ?>"/> <acronym title="Active l'acompte paramétré"><input type="submit" name="Activer" value="Activer l'acompte"/></acronym></form><?php
  }
  if ((isset($Type))&&($Type=="euro")) {  ?>
    <input type="hidden" name="acompte" value="2"/>Montant en euro<font color='#FF0000'>*</font> : <input type="text" name="accompte" value="<?php echo $Devis->acompte_montant; ?>"/> <acronym title="Active l'acompte paramétré"><input type="submit" name="Activer" value="Activer l'acompte"/></acronym></form><?php
  }
}
  
else {
    ?><form name="FormAcompte" action="" method="POST"><input type="hidden" name="acompte" value="0"/><acronym title="Désactivé l'acompte paramétré"><input type="submit" name="Desactiver" value="Désactiver l'acompte"/></acronym></form><?php
}

?>
</p>

<?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")) { ?>

<p><HR /></p>

<form name="form_model2" action="" method="POST">
<span class="col_1">Delivery duty paid :</span> <input type="text" name="ddp" value="<?php echo $Transit->ddp; ?>"/><BR />   
<span class="col_1">Total boxes :</span> <?php echo $TotalBox; ?> <BR />  
<span class="col_1">Total pieces :</span> <?php echo $TotalPiece; ?> <BR />  
<span class="col_1">Gross weight :</span> <?php echo $PoidBrut; ?> <BR />  
<span class="col_1">Net weight :</span> <?php echo $PoidNet; ?> <BR /> 
<span class="col_1">Container N° :</span> <input type="text" name="cn" value="<?php echo $Transit->cn; ?>" />  <BR /> 
<span class="col_1">Origin product :</span> <input type="text" name="op" value="<?php echo $Transit->op; ?>" /> <BR />
<p><input type="submit" name="Model2" value="Modifier"/></p>   
</form>

<?php } 
if (($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>

<p><HR /></p>

<form name="form_model2" action="" method="POST">
<span class="col_1">Service de livraison payé  :</span> <input type="text" name="ddp" value="<?php echo $Transit->ddp; ?>"/><BR />   
<span class="col_1">Total des boîtes :</span> <?php echo $TotalBox; ?> <BR />  
<span class="col_1">Total pieces :</span> <?php echo $TotalPiece; ?> <BR />  
<span class="col_1">Poids brut :</span> <?php echo $PoidBrut; ?> <BR />  
<span class="col_1">Poids net :</span> <?php echo $PoidNet; ?> <BR /> 
<span class="col_1">Conteneur N ° :</span> <input type="text" name="cn" value="<?php echo $Transit->cn; ?>" />  <BR /> 
<span class="col_1">Produit d'origine :</span> <input type="text" name="op" value="<?php echo $Transit->op; ?>" /> <BR />
<p><input type="submit" name="Model2" value="Modifier"/></p>   
</form>

<?php } ?>

<p><HR /></p>

<form name="Form_Debut" action="<?php echo $Home; ?>/lib/script/Devis/debut2.php?id=<?php echo $Devis->id; ?>" method="POST">
Date du debut de la prestation<font color='#FF0000'>*</font> : <input type="text" class="Moyen" value="<?php echo $Devis->debut; ?>" name="debut" required="required" onChange="submitFormDebut()"/>
<div id="AffichageDebut"></div>
</form>

<form name="Form_Duree" action="<?php echo $Home; ?>/lib/script/Devis/duree2.php?id=<?php echo $Devis->id; ?>" method="POST">
Durée estimé de la prestation<font color='#FF0000'>*</font> : <input type="text" class="Moyen" value="<?php echo $Devis->duree; ?>" name="duree" required="required" onChange="submitFormDuree()"/>
<div id="AffichageDuree"></div>
</form>

<p><HR /></p>

<form name="Form_Remarque" action="<?php echo $Home; ?>/lib/script/Devis/remarque2.php?id=<?php echo $Devis->id; ?>" method="POST">
<textarea rows="5" class="Max" name="remarque" placeholder="Remarque" onChange="submitFormRemarque()"><?php echo stripslashes($Devis->remarque); ?></textarea>
<div id="AffichageRemarque"></div>
</form></p>

<p><HR /></p>

<ul class="accordion">
  <li class="has-sub">
    <label for="Article">Article existant</label><input id="Article"type="checkbox" />
    <ul class="sub">
      <li>
        <table>
            <TR>
                <TH>
                    Catégorie
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    Référence
                </TH>
                <?php } ?>
                <TH>
                    Description
                </TH>
                <TH>
                    PU / HT
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    Quantité Box
                </TH>
                <?php } ?>
                <TH>
                    Quantité
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    Poid Brut 
                </TH>
                <TH>
                    Poid Net
                </TH>
                <?php } ?>
                <TH>
                    TVA
                </TH>
                <TH>
                    Marge
                </TH>
                <TH>
                    
                </TH>
            </TR>

            <form name="form_recherche" action="#Article" method="POST">
            <TR>
                <TH>
                    <select name="RechercheCategorie">
                        <option value="">-- --</option><?php
                        while ($Categorie=$ListCategorie->fetch(PDO::FETCH_OBJ)) { 
                        echo "<option value='".$Categorie->categorie."'>".$Categorie->categorie."</option>";
                        } ?>
                    </select>
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    <input class="Mini" type="text" name="RechercheReference"/>
                </TH>
                <?php } ?>
                <TH>
                    <input class="Max" type="text" name="RechercheDescription"/>
                </TH>
                <TH>
                    <input class="Mini" type="text" name="RecherchePu"/>
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    
                </TH>
                <?php } ?>
                <TH>
                    
                </TH>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TH>
                    
                </TH>
                <TH>
                    
                </TH>
                <?php } ?>
                <TH>
                    <input class="Mini" type="text" name="RechercheTva"/>
                </TH>
                <TH>

                </TH>
                <TH>
                    <input type="submit" class="Mini" name="MoteurRecherche" value="Rechercher"/>
                </TH>
            </TR>
            </form>

            <?php while ($ArticleExist=$SelectArticleExist->fetch(PDO::FETCH_OBJ)) { ?>
            <form name="form_ajout_exist" action="" method="POST">

            <TR>
                <TD>
                    <?php echo stripslashes($ArticleExist->categorie); ?>
                </TD>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TD>
                    <input class="Mini" type="text" name="reference" value="<?php echo $ArticleExist->reference; ?>"/>
                </TD>
                <?php } ?>
                <TD>
                    <textarea class="Max" name="description" required="required"><?php echo stripslashes($ArticleExist->description); ?></textarea>
                </TD>
                <TD>
                    <input class="Mini" type="text" name="PU" required="required" value="<?php echo number_format($ArticleExist->PU, 3,".", ""); ?>"/>
                </TD>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TD>
                    <input class="Mini" type="text" name="quantite_box" required="required"/>
                </TD>
                <?php } ?>
                <TD>
                    <input class="Mini" type="text" name="quantite" required="required"/>
                </TD>
                <?php if (($Param->model=="model21")||($Param->model=="model22")||($Param->model=="model23")||($Param->model=="model31")||($Param->model=="model32")||($Param->model=="model33")) { ?>
                <TD>
                    <input class="Mini" type="text" name="poid_brut" required="required"/>
                </TD>
                <TD>
                    <input class="Mini" type="text" name="poid_net" required="required"/>
                </TD>
                <?php } ?>
                <TD>
                    <input class="Mini" type="text" name="TVA" required="required" value="<?php echo number_format($ArticleExist->TVA, 2,".", ""); ?>"/>
                </TD>
                <TD>
                    <input class="Mini" type="text" name="marge"/>
                </TD>
                <TD>
                    <input type="submit" name="Ajouter" value="Ajouter"/>
                </TD>
            </TR>

            </form>

            <?php } ?>

            </table>
      </li>
    </ul>
  </li>
</ul>
</div>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
<script type="text/javascript" src="select_acompte.js"></script>
</body>

</html>