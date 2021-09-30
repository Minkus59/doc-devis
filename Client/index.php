<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Erreur.=$_GET['erreur'];

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$SelectClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectClient->execute();

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheCode'])) {
        $RechercheCode=trim($_POST['RechercheCode']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code LIKE :code AND hash=:client");
        $SelectClient->execute(array(':client'=> $SessionClient,':code'=> "%".$RechercheCode."%")); 
    }
    if (!empty($_POST['RechercheNom'])) {
        $RechercheNom=trim($_POST['RechercheNom']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE nom LIKE :nom AND hash=:client");
        $SelectClient->execute(array(':client'=> $SessionClient, ':nom' => "%".$RechercheNom."%")); 
    }
    if (!empty($_POST['RecherchePrenom'])) {
        $RecherchePrenom=trim($_POST['RecherchePrenom']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE prenom LIKE :prenom AND hash=:client");
        $SelectClient->execute(array(':client'=> $SessionClient,':prenom' => "%".$RecherchePrenom."%")); 
    }
    if (!empty($_POST['RechercheCp'])) {
        $RechercheCp=trim($_POST['RechercheCp']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE cp=:cp AND hash=:client");
        $SelectClient->execute(array(':client'=> $SessionClient,':cp' => $RechercheCp)); 
    }
    if (!empty($_POST['RechercheVille'])) {
        $RechercheVille=trim($_POST['RechercheVille']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE ville LIKE :ville AND hash=:client");
        $SelectClient->execute(array(':client'=> $SessionClient,':ville' => "%".$RechercheVille."%")); 
    }
}

?>
<!-- *******************************
*** Script r�alis� par neuro-soft ***
********* www.neuro-soft.fr *********
*********************************-->
<!DOCTYPE html>
<html>
<head>
<title>Client</title>
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

<form name="form_test" action="<?php echo $Home; ?>/Client/Export.php" method="POST">
<input type="submit" name="Exporter" value="Exporter"/>
</form>

<article> 
    <table width=900>
    <tr>
        <th>
            Code client
        </th>
        <th>
            Nom
        </th>
        <th>
            Prenom
        </th>
        <th>
            Code postal
        </th>
        <th>
            Ville
        </th>
        <th>
      Action
        </th>
    </tr>
    
<form name="form_recherche" action="" method="POST">
<TR>
    <TH>
        <input class="Moyen" type="text" name="RechercheCode"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheNom"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RecherchePrenom"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheCp"/>
    </TH>
    <TH>
        <input class="Moyen" type="text" name="RechercheVille"/>
    </TH>
    <TH>
        <input type="submit" name="MoteurRecherche" value="Rechercher"/>
    </TH>
</TR>
</form>
    
<?php
while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>

    <tr>
        <td class="Moyen">
            <?php echo $Client->code; ?>
        </td>
        <td class="Moyen">
            <?php echo stripslashes($Client->nom); ?>
        </td>
        <td class="Moyen">
            <?php echo stripslashes($Client->prenom); ?>
        </td>
        <td class="Moyen">
            <?php echo $Client->cp; ?>
        </td>
        <td class="Moyen">
            <?php echo stripslashes($Client->ville); ?>
        </td>
        <td class="Moyen">
            <a href="<?php echo $Home; ?>/Client/Modifier/?id=<?php echo $Client->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Client/SupprClient.php?id=<?php echo $Client->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
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