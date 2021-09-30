<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page");
$Select->execute();
    
?>

<!-- ************************************
*** Script rÃ©alisÃ© par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>

<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Liste des pages</H1></p>

<table>
<tr>
      <th>Position</th>
      <th>Libélé</th>
      <th>Lien de page</th>
      <th>Titre</th>
      <th>Description</th>
      <th>Date de création</th>
      <th>Action</th>
      </tr>
<?php

while ($Page=$Select->fetch(PDO::FETCH_OBJ)) {
?>
   <tr <?php if ($Page->statue==0) { echo "class='rouge'"; } else { echo "class='vert'"; } ?>>
   <td><?php echo $Page->position; ?></td>
   <td><?php echo $Page->libele; ?></td>
   <td><?php echo $Page->lien; ?></td>
   <td><?php echo $Page->titre; ?></td>
   <td><?php echo $Page->description; ?></td>
   <td><?php echo date("d-m-Y", time($Page->created)); ?></td>
   <td><?php 
   echo '<a title="Apperçu" href="'.$Page->lien.'"><img src="'.$Home.'/Admin/lib/img/apercu.png"></a>';
   echo '<a href="'.$Home.'/Admin/Page/Nouveau/?id='.$Page->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
   if ($Page->statue==1) { ?>
        <a title="Désactiver" href="<?php echo $Home; ?>/Admin/Page/desactiver.php?id=<?php echo $Page->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/desactiver.png" alt="Désactiver"></a>
  <?php } else { ?>
        <a title="Activer" href="<?php echo $Home; ?>/Admin/Page/activer.php?id=<?php echo $Page->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/activer.png" alt="Activer"></a>
  <?php } 
        echo '<a title="Supprimer" href="'.$Home.'/Admin/Page/supprimer.php?id='.$Page->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a></td></tr>';
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>