<?php 
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Valid=$_GET['valid'];
$Erreur=$_GET['erreur'];

$Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE hash=:hash ORDER BY date DESC");
$Selectrecu->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$Selectrecu->execute();

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE hash=:hash ORDER BY nom ASC");
$SelectClient->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectClient->execute();

$SelectDevis=$cnx->prepare("SELECT * FROM ".$Prefix."devis_devis WHERE hash=:hash ORDER BY id ASC");
$SelectDevis->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectDevis->execute();

$SelectFacture=$cnx->prepare("SELECT * FROM ".$Prefix."devis_facture WHERE hash=:hash ORDER BY id ASC");
$SelectFacture->bindParam(':hash', $SessionClient, PDO::PARAM_STR);
$SelectFacture->execute();

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheCode'])) {
        $RechercheCode=trim($_POST['RechercheCode']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE code LIKE :code AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':code'=> "%".$RechercheCode."%")); 
    }
    if (!empty($_POST['RechercheClient'])) {
        $RechercheClient=trim($_POST['RechercheClient']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE client=:client AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':client' => $RechercheClient)); 
    }
    if (!empty($_POST['RechercheSource'])) {
        $RechercheSource=trim($_POST['RechercheSource']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE source=:source AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':source' => $RechercheSource)); 
    }
    if (!empty($_POST['RechercheMontant'])) {
        $RechercheMontant=trim($_POST['RechercheMontant']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE montant LIKE :montant AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':montant' => "%".$RechercheMontant."%")); 
    }
    if (!empty($_POST['RechercheMotif'])) {
        $RechercheMotif=trim($_POST['RechercheMotif']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE motif LIKE :motif AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':motif' => "%".$RechercheMotif."%")); 
    }
    if (!empty($_POST['RechercheMode'])) {
        $RechercheMode=trim($_POST['RechercheMode']);
        $Selectrecu=$cnx->prepare("SELECT * FROM ".$Prefix."devis_recu WHERE mode=:mode AND hash=:hash ORDER BY date DESC");
        $Selectrecu->execute(array(':hash'=> $SessionClient,':mode' => $RechercheMode)); 
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

<H1>Liste des reçus</H1></p>

    <table width=900>
    <tr>
        <th>
            Code
        </th>
        <th>
            Source
        </th>
        <th>
            Client
        </th>
        <th>
            Montant
        </th>
        <th>
            Motif
        </th>
        <th>
            Mode de paiement
        </th>
        <th class="Mini">
            Date
        </th>
        <th>
            Action
        </th>
    </tr>

<form name="form_recherche" action="" method="POST">
<TR>
    <TH>
        <input type="text" class="Mini" name="RechercheCode"/>
    </TH>
    <TH>
        <select name="RechercheSource">
        <option value="">-- Selection --</option>
        <?php while($Devis=$SelectDevis->fetch(PDO::FETCH_OBJ)) { ?>
            <option value="<?php echo $Devis->code; ?>" ><?php echo $Devis->code; ?></option>
        <?php } ?>
        <?php while($Facture=$SelectFacture->fetch(PDO::FETCH_OBJ)) { ?>
            <option value="<?php echo $Facture->code; ?>" ><?php echo $Facture->code; ?></option>
        <?php } ?>
        </select>
    </TH>
    <TH>
        <select name="RechercheClient">
        <option value="">-- Selection --</option>
        <?php while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
            <option value="<?php echo $Client->code; ?>" ><?php echo $Client->nom; ?></option>
        <?php } ?>
        </select>
    </TH>
    <TH>
        <input class="Mini" type="text" name="RechercheMontant"/>
    </TH>
    <TH>
        <input type="text" name="RechercheMotif"/>
    </TH>
    <TH>
        <select name="RechercheMode">
            <option value="">-- --</option>
            <option value="Espece">Espece</option>
            <option value="Chéque">Chéque</option>
            <option value="Virement">Virement</option>
        </select>
    </TH>
    <TH>
        
    </TH>
    <TH>
        <input type="submit" class="Mini" name="MoteurRecherche" value="Rechercher"/>
    </TH>
</TR>
</form>

<?php
while($Recu=$Selectrecu->fetch(PDO::FETCH_OBJ)) {
      $Selectclient=$cnx->prepare("SELECT * FROM ".$Prefix."devis_client WHERE code=:code");
      $Selectclient->bindParam(':code', $Recu->client, PDO::PARAM_STR);
      $Selectclient->execute();
      $client=$Selectclient->fetch(PDO::FETCH_OBJ);
?>

    <tr>
        <td class="Center">
            <?php echo stripslashes($Recu->code); ?>
        </td>
        <td class="Center">
            <?php echo stripslashes($Recu->source); ?>
        </td>
        <td>
            <?php echo stripslashes($client->nom); ?>
        </td>
        <td class="Center">
            <?php echo $Recu->montant; ?>
        </td>
        <td class="Center">
            <?php echo stripslashes($Recu->motif); ?>
        </td>
        <td class="Center">
            <?php echo stripslashes($Recu->mode); ?>
        </td>
        <td class="Center">
            <?php echo date("d-m-y",$Recu->date); ?>
        </td>
        <td class="Center">
            <a target="_blank" href="<?php echo $Home; ?>/Recu/Visualisation/?id=<?php echo $Recu->id; ?>"><acronym title="Information"><img src="<?php echo $Home; ?>/lib/img/apercu.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Recu/Nouveau/?id=<?php echo $Recu->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Modif.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/Recu/supprimer.php?id=<?php echo $Recu->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Suppr.png"/></acronym></a>
        </td>
    </tr>

<?php
}
?>
    </table>
</article>

</div>
</div>

<?php require($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>

</CENTER>
</body>

</html>