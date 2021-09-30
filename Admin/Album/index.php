<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect3.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Now=time();
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');

$SelectPhoto=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Album");
$SelectPhoto->execute();

//--------- Etape
$Etape=$_SESSION['Etape'];

if (isset($Etape)) {
  $Etape1=true;

  if ($Etape=="2") {
    $Etape2=false;
    $Etape1=true;
  }
}
else {  
  $Etape1=false;
  $Etape2=true;
}

//--------- Ajouter une photo
// Upload, creation etape2
if (isset($_POST['Ajouter'])) {

    if ($_FILES['photo']['name']!="") {
        $chemin=$_FILES['photo']['name'];
        $fichier=basename($chemin);
        $taille_origin=filesize($_FILES['photo']['tmp_name']);
        $ext_origin=strchr($chemin, '.');

        $TailleImage=@getimagesize($_FILES['photo']['tmp_name']);
        $taille_max="20000000";

        $Code=md5(uniqid(rand(), true));
        $Hash=substr($Code, 0, 8);

        $repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Photo/";
        $repExt=$Home."/lib/Photo/";

        if (in_array($ext_origin, $ext2)) {
            if($taille_origin>$taille_max) {
                $Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
            }

            if (!isset($Erreur)) {
            
                if ($TailleImage[0]>960) {
                  $NouvelleLargeur_photo = 960;                
                } 
                if (($TailleImage[0]>480)&&($TailleImage[0]<960)) {
                  $NouvelleLargeur_photo = 480;               
                } 
                if ($TailleImage[0]<480) {
                  $NouvelleLargeur_photo = $TailleImage[0];                
                }                
                                              
                $NouvelleHauteur_photo = ( ($TailleImage[1] * (($NouvelleLargeur_photo)/$TailleImage[0])) );      
                $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
                $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
                imagealphablending($NouvelleImage_photo, false);
                imagesavealpha($NouvelleImage_photo, true);
                imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImage[0],$TailleImage[1]);

                $UpMaqimg=imagepng($NouvelleImage_photo, $repInt.$Hash.$ext_origin, 0);
    
                if ($UpMaqimg==false) {
                        $Erreur="Erreur de téléchargement, veuillez réassayer ultèrueurement";
                }
                else {
                    $_SESSION['Etape']="2";
                    $_SESSION['lien']=$repExt.$Hash.$ext_origin;
                    $_SESSION['lienInt']=$repInt.$Hash.$ext_origin;
                    $delai=0;
                    header("Refresh:".$delai.";url=".$Home."/Admin/Album/");
                }
            } 
        }
        if (in_array($ext_origin, $ext1)) {
            if($taille_origin>$taille_max) {
                $Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
            }

            if (!isset($Erreur)) {
            
                if ($TailleImage[0]>940) {
                  $NouvelleLargeur_photo = 940;                
                } 
                if (($TailleImage[0]>480)&&($TailleImage[0]<940)) {
                  $NouvelleLargeur_photo = 460;               
                } 
                if ($TailleImage[0]<480) {
                  $NouvelleLargeur_photo = $TailleImage[0];                
                }
                
                $NouvelleHauteur_photo = ( ($TailleImage[1] * (($NouvelleLargeur_photo)/$TailleImage[0])) );
                $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
                $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
                imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImage[0],$TailleImage[1]);

                $UpMaqimg=imagejpeg($NouvelleImage_photo, $repInt.$Hash.$ext_origin, 100);
    
                if ($UpMaqimg==false) {
                        $Erreur="Erreur de téléchargement, veuillez réassayer ultèrueurement";
                }
                else {
                    $_SESSION['Etape']="2";
                    $_SESSION['lien']=$repExt.$Hash.$ext_origin;
                    $_SESSION['lienInt']=$repInt.$Hash.$ext_origin;
                    $delai=0;
                    header("Refresh:".$delai.";url=".$Home."/Admin/Album/");
                }
            } 
        }
    }
}

//--------- Ajouter une photo
//--------- Validation, Insertion
if (isset($_POST['Valider'])) {
        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Album (lien) VALUES(:lien)");
        $Insert->BindParam(":lien", $_SESSION['lien'], PDO::PARAM_STR);
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
        }
        else  {     
            unset($_SESSION['Etape']);
            unset($_SESSION['lien']);
            unset($_SESSION['lienInt']);

            $Valid="Article ajouter avec succès";
            header("location:".$Home."/Admin/Album/?valid=".urlencode($Valid));
        }  
}

if (isset($_POST['Annuler'])) {
    unlink($_SESSION['lienInt']);
    
    unset($_SESSION['Etape']);
    unset($_SESSION['lien']);
    unset($_SESSION['lienInt']);
  
    header('location:'.$Home.'/Admin/Album');
}

if (isset($_POST['Rotation'])) {
    $ext_origin=strchr($_SESSION['lienInt'], '.');
    
    $TailleImage=@getimagesize($_SESSION['lien']);
    $degrees = 90;
    
    if (in_array($ext_origin, $ext1)) {
      $source = imagecreatefromjpeg($_SESSION['lienInt']);
      $rotate = imagerotate($source, $degrees, 0);  
    }
    else {
      $source = imagecreatefrompng($_SESSION['lienInt']); 
      imagealphablending($source, false);
      imagesavealpha($source, true);
      $transparency = imagecolorallocatealpha( $source, 0,0,0,127 ); 
      $rotate = imagerotate($source, $degrees, $transparency); 
      imagealphablending($rotate, false);
      imagesavealpha($rotate, true);  
    }
  
    unlink($_SESSION['lienInt']);
    
    if (in_array($ext_origin, $ext1)) {
      $nouvelle_image=imagejpeg($rotate, $_SESSION['lienInt'], 100);
    }
    else {
      $nouvelle_image=imagepng($rotate, $_SESSION['lienInt'], 0);
    }
      
    imagedestroy($rotate);
  
    $delai=0;
    header("Refresh:".$delai.";url=".$Home."/Admin/Album/");
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


<?php if ($Etape1==false) { ?>
<H1>Ajouter une photo</H1></p>

<form name="form_photo" action="" method="POST" enctype="multipart/form-data">

<input type="file"  placeholder="Photo" name="photo"/><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" title="Image au format (.jpg / .png)"/></p>

<input type="submit" name="Ajouter" value="Ajouter"/>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis</p>

<?php }
if ($Etape2==false) { ?>

<H1>Validation de la photo - Etape 2</H1></p>

<div id="valid_img">

<img src="<?php echo $_SESSION['lien']; ?>"></p>
</div>

<div id="valid_lien">

<form name="form_rotate" action="" method="POST">
<input type="submit" name="Rotation" value="Rotation 90°"/>
</form></p>

<form name="form_valid" action="" method="POST">
<input type="submit" name="Valider" value="Terminer"/>
</form>

<form name="form_delete" action="" method="POST">
<input type="submit" name="Annuler" value="Annuler"/>
</form>

</div>

<?php } ?> 

<p><HR /></p>

<H1>Album Photo</H1></p>

<table>
<tr><th>Image</th><th>Lien</th><th>Action</th></tr>
<?php

while ($Photo=$SelectPhoto->fetch(PDO::FETCH_OBJ)) {
      echo "<TR><TD><img height='80px' src='$Photo->lien'/></TD>";
      echo "<TD>".$Photo->lien."</TD>";
      echo "<TD><a href='$Home/Admin/Album/supprimer.php?id=$Photo->id'><img src='$Home/Admin/lib/img/supprimer.png'></a></TD></TR>";
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>