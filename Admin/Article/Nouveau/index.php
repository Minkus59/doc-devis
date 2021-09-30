<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];
$Now=time();

$SelectPhoto=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Album");
$SelectPhoto->execute();

$SelectPage=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page");
$SelectPage->execute();  

if (isset($_GET['id'])) { 
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Actu=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    $Message=$_POST['message'];
    $Page=$_POST['page'];
    $Position=$_POST['position'];

    if (trim($Message)=="") {
        $Erreur="Le contenue est vide !";
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Article SET position=:position ,message=:message, page=:page, created=:created WHERE id=:id");
        $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
        $Insert->BindParam(":message", $Message, PDO::PARAM_STR);
        $Insert->BindParam(":page", $Page, PDO::PARAM_STR);   
        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
        }
        else  {     
            $Valid="Article modifier avec succès";
            header('location:'.$Home.'/Admin/Article/?id='.$Id.'&valid='.urlencode($Valid));
        }
    }
} 

if ((isset($_POST['Ajouter']))&&(!isset($_GET['id']))) {
    $Message=$_POST['message'];
    $Page=$_POST['page'];

    if (trim($Message)=="") {
        $Erreur="Le contenue est vide !";
    }
    else {
         //verifier si 1er article sinon position +1
         $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE page=:page");
         $Verif->BindParam(":page", $Page, PDO::PARAM_STR);
         $Verif->execute();
         $NbPage=$Verif->rowCount();

         if ($NbPage!=0) {
              $Position=$NbPage+1;
              $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Article (position, message, page, created) VALUES(:position, :message, :page, :created)");
              $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
              $Insert->BindParam(":message", $Message, PDO::PARAM_STR);
              $Insert->BindParam(":page", $Page, PDO::PARAM_STR);        
              $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
              $Insert->execute();
         }
         else {
              $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Article (message, page, created) VALUES(:message, :page, :created)");
              $Insert->BindParam(":message", $Message, PDO::PARAM_STR);
              $Insert->BindParam(":page", $Page, PDO::PARAM_STR);        
              $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
              $Insert->execute();
         }

         if ($Insert==false) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
         }
         else  {
            $Valid="Article ajouter avec succès";
            header('location:'.$Home.'/Admin/Article/?valid='.urlencode($Valid));
         }
    }
}
    
?>

<!-- ************************************
*** Script realise par NeuroSoft Team ***
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

<script type="text/javascript" src="<?php echo $Home; ?>/Admin/lib/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
  tinymce.init({
    force_br_newlines : true,
    force_p_newlines : false,
    forced_root_block : '', // Needed for 3.x
    relative_urls : true,
    remove_script_host : false,
    min_height : '350',
    selector: '#message',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    textcolor_map: [
        "000000", "Black",
        "993300", "Burnt orange",
        "333300", "Dark olive",
        "003300", "Dark green",
        "003366", "Dark azure",
        "000080", "Navy Blue",
        "333399", "Indigo",
        "333333", "Very dark gray",
        "800000", "Maroon",
        "FF6600", "NeuroSoft Team",
        "808000", "Olive",
        "008000", "Green",
        "008080", "Teal",
        "0000FF", "Blue",
        "666699", "Grayish blue",
        "808080", "Gray",
        "FF0000", "Red",
        "FF9900", "Amber",
        "99CC00", "Yellow green",
        "339966", "Sea green",
        "33CCCC", "Turquoise",
        "3366FF", "Royal blue",
        "800080", "Purple",
        "999999", "Medium gray",
        "FF00FF", "Magenta",
        "FFCC00", "Gold",
        "FFFF00", "Yellow",
        "00FF00", "Lime",
        "00FFFF", "Aqua",
        "00CCFF", "Sky blue",
        "993366", "Red violet",
        "FFFFFF", "White",
        "FF99CC", "Pink",
        "FFCC99", "Peach",
        "FFFF99", "Light yellow",
        "CCFFCC", "Pale green",
        "CCFFFF", "Pale cyan",
        "0090A0", "NeuroSoft Bleu",
        "ff6600", "NeuroSoft Orange"

    ]
});
</script>
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

<?php if (isset($_GET['id'])) { ?>
      <H1>Modifier un article</H1></p> <?php
} else { ?>
  <H1>Ajouter un nouvel article</H1></p> <?php
} ?>

<form name="form_actu" action="" method="POST" enctype="multipart/form-data">
<select name="page" required>
<option value="">--  --</option>
<?php while ($PageLien=$SelectPage->fetch(PDO::FETCH_OBJ)) { ?>
<option value='<?php echo $PageLien->lien; ?>' <?php if ((isset($_GET['id']))&&($Actu->page == $PageLien->lien)) { echo "selected"; } ?>><?php echo $PageLien->libele; ?></option>
<?php } ?>
</select><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Page ou apparaîtra l'article"/></p>

<textarea id="message" name="message" placeholder="Message*" require="required"><?php if (isset($_GET['id'])) { echo $Actu->message; } ?></textarea></p>

<?php if (isset($_GET['id'])) { ?>
      <input type="text" placeholder="Position" name="position" require="required" value="<?php echo $Actu->position; ?>"></p>
<?php } ?>

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/> <?php } else { ?><input type="submit" name="Ajouter" value="Ajouter"/><?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis</p>

<p><HR /></p>

<H1>Album Photo</H1></p>

<table>
<tr><th>Image</th><th>Lien</th></tr>
<?php

while ($Photo=$SelectPhoto->fetch(PDO::FETCH_OBJ)) {
      echo "<TR><TD><img height='80px' src='$Photo->lien'/></TD>";
      echo "<TD>".$Photo->lien."</TD></TR>";
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>